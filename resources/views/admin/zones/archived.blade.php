@extends('layouts.admin-sidebar')

@section('title', 'Archived Zones')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<a href="{{ route('admin.zones.index') }}">Zones</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Archived</span>
@endsection

@section('content')
<div class="page-stack">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-2xl font-bold text-gray-900">
        <h1>Archived Zones</h1>
        <a href="{{ route('admin.zones.index') }}" class="btn-secondary flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Active Zones
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-600"></i>
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @forelse($archivedZones as $zone)
        <div class="card-container overflow-hidden opacity-75">
            <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gray-500 flex items-center justify-center">
                            <i class="fas fa-archive text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Zone {{ $zone->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $zone->description ?? 'No description' }}</p>
                            <p class="text-xs text-gray-400 mt-1">Archived: {{ $zone->deleted_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.zones.restore', $zone->id) }}" method="POST" class="inline" onsubmit="return confirmRestore('{{ $zone->name }}')">
                            @csrf
                            <button type="submit" class="icon-btn p-8 border-green-300 text-green-600 hover:bg-green-200" title="Restore Zone">
                                <i class="fas fa-trash-restore"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.zones.force-delete', $zone->id) }}" method="POST" class="inline" onsubmit="return confirmPermanentDelete('{{ $zone->name }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="icon-btn p-8 border-red-300 text-red-600 hover:bg-red-200" title="Permanently Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-gray-50">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $zone->total_slots_count }}</p>
                        <p class="text-xs text-gray-500">Total Slots</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $zone->available_slots }}</p>
                        <p class="text-xs text-gray-500">Available</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-red-600">{{ $zone->occupied_slots }}</p>
                        <p class="text-xs text-gray-500">Occupied</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $zone->pwd_occupied }}/{{ $zone->pwd_slots }}</p>
                        <p class="text-xs text-gray-500">PWD Used</p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 bg-white rounded-xl p-40 text-center border border-gray-100">
            <i class="fas fa-archive text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">No archived zones found.</p>
        </div>
        @endforelse
    </div>
    
    <div class="mt-5">{{ $archivedZones->links() }}</div>
</div>

<script>
function confirmRestore(zoneName) {
    return confirm(`Restore Zone ${zoneName}? All parking slots in this zone will also be restored.`);
}

function confirmPermanentDelete(zoneName) {
    return confirm(`⚠️ PERMANENT DELETE: Zone ${zoneName} and ALL its parking slots will be permanently removed from the database. This action CANNOT be undone. Continue?`);
}
</script>
@endsection