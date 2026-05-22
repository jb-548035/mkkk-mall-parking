@extends('layouts.admin-sidebar')

@section('title', 'Archived Parking Slots')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<a href="{{ route('admin.slots.index') }}">Parking Slots</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Archived</span>
@endsection

@section('content')
<div class="page-stack">
    <div class="page-header text-2xl font-bold text-gray-900">
        <h1>Archived Parking Slots</h1>
        <a href="{{ route('admin.slots.index') }}" class="btn-secondary flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Active Slots
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-600"></i>
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="card-container overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Slot Number</th>
                        <th>Type</th>
                        <th>Archived Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivedSlots as $slot)
                    <tr>
                        <td class="font-medium">{{ $slot->slot_number }}</td>
                        <td>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($slot->type == 'wheelchair') bg-blue-100 text-blue-700
                                @elseif($slot->type == 'delivery') bg-orange-100 text-orange-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ ucfirst($slot->type) }}
                            </span>
                        </td>
                        <td>{{ $slot->deleted_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="confirmRestore({{ $slot->id }}, '{{ $slot->slot_number }}')" 
                                    class="icon-btn text-xs text-green-600 border-green-200 hover:bg-green-50" title="Restore">
                                    <i class="fas fa-trash-restore"></i>
                                </button>
                                <button type="button" onclick="confirmPermanentDelete({{ $slot->id }}, '{{ $slot->slot_number }}')" 
                                    class="icon-btn text-xs text-red-600 border-red-200 hover:bg-red-50" title="Permanently Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">No archived slots found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-5">{{ $archivedSlots->links() }}</div>
</div>

<script>
function confirmRestore(id, slotNumber) {
    if(confirm(`Restore slot ${slotNumber}? This slot will become active again.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/slots/${id}/restore`;
        form.innerHTML = `@csrf`;
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmPermanentDelete(id, slotNumber) {
    if(confirm(`⚠️ PERMANENT DELETE: Slot ${slotNumber} will be permanently removed from the database. This action CANNOT be undone. Continue?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/slots/${id}/force-delete`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection