@extends('layouts.admin-sidebar')

@section('title', 'Parking Slots')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Parking Slots</span>
@endsection

@section('content')
<div class="page-stack">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Parking Slots Management</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.slots.archived') }}" class="btn-secondary flex items-center gap-2">
                <i class="fas fa-archive"></i> Archived Slots
            </a>
            <a href="{{ route('admin.slots.create') }}" class="btn-primary flex items-center gap-2">
                <i class="fas fa-plus"></i> Add New Slot
            </a>
        </div>
    </div>
    
    @foreach($zones as $zone)
    <div class="card-container overflow-hidden">
        <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-layer-group text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Zone {{ $zone['name'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $zone['available'] }}/{{ $zone['total'] }} available</p>
                </div>
            </div>
            <div class="progress-bar w-full sm:max-w-xs flex-shrink-0">
                <div class="progress-bar-fill" style="width: {{ $zone['percentage'] }}%"></div>
            </div>
        </div>
        
        <div class="p-4 sm:p-5">
            <div class="slot-grid">
                @foreach($zone['slots'] as $slot)
                <div class="relative min-w-0">
                    <div class="slot-card card-container rounded-xl transition-all hover:shadow-md
                        @if($slot->status == 'available') border-green-200 bg-green-50
                        @elseif($slot->status == 'occupied') border-red-200 bg-red-50
                        @else border-blue-200 bg-blue-50 @endif">
                        <span class="badge absolute -top-2 right-2 text-[0.65rem] px-2 py-0.5
                            @if($slot->status == 'available') badge-success
                            @elseif($slot->status == 'occupied') bg-red-100 text-red-700
                            @else badge-info @endif">
                            {{ ucfirst($slot->status) }}
                        </span>
                        <div class="flex items-center justify-between gap-1 mb-2">
                            <span class="text-sm font-bold text-gray-900 truncate">{{ $slot->slot_number }}</span>
                            @if($slot->type == 'wheelchair')
                            <i class="fas fa-wheelchair text-blue-500 text-xs flex-shrink-0" title="Accessible"></i>
                            @elseif($slot->type == 'delivery')
                            <i class="fas fa-truck text-orange-500 text-xs flex-shrink-0" title="Delivery"></i>
                            @endif
                        </div>
                        <div class="slot-card-actions">
                            <button type="button" onclick="openEditModal({{ $slot->id }}, '{{ $slot->slot_number }}', '{{ $slot->type }}', '{{ $slot->status }}', {{ $slot->is_active ? 'true' : 'false' }})" 
                                class="icon-btn text-xs" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" onclick="confirmArchive({{ $slot->id }}, '{{ $slot->slot_number }}')" 
                                class="icon-btn text-xs text-red-600 border-red-200 hover:bg-red-50" title="Archive">
                                <i class="fas fa-archive"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-overlay" onclick="closeModalOnOutside(event, 'editModal')">
    <!-- 1. Added dynamic max-height constraint and a vertical flex column strategy to the modal container box framework -->
    <div class="modal-container modal-container--sm max-h-[85vh] flex flex-col bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
        
        <form id="editForm" method="POST" class="flex flex-col flex-1 min-h-0 m-0">
            @csrf
            @method('PUT')
            
            <!-- Fixed Header Row Component -->
            <div class="modal-header flex-none">
                <h3 class="font-semibold">Edit Parking Slot</h3>
                <button type="button" onclick="closeModal('editModal')" class="modal-close">&times;</button>
            </div>
            
            <!-- 2. SCROLLABLE BODY BLOCK CONTAINER: Isolates options fields, locking your submit footer elements to the display window floor base base layer -->
            <div class="modal-body flex-1 overflow-y-auto p-6 min-h-0">
                <div class="form-group mb-4">
                    <label class="form-label font-medium block text-gray-700 mb-2" for="edit_slot_number">Slot Number</label>
                    <input type="text" name="slot_number" id="edit_slot_number" class="form-control w-full">
                </div>
                <div class="form-group mb-4">
                    <label class="form-label font-medium block text-gray-700 mb-2" for="edit_type">Type</label>
                    <select name="type" id="edit_type" class="form-control w-full">
                        <option value="standard">Standard</option>
                        <option value="wheelchair">Wheelchair Accessible</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label font-medium block text-gray-700 mb-2" for="edit_status">Status</label>
                    <select name="status" id="edit_status" class="form-control w-full">
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="text-sm text-gray-700 font-medium">Active (slot can be used)</span>
                    </label>
                </div>
            </div>
            
            <!-- Fixed Action Button Footer (Pinned cleanly at the absolute base of the overlay box framework) -->
            <div class="modal-footer flex justify-end gap-4 p-4 border-t border-gray-100 flex-none bg-gray-50 rounded-b-lg">
                <button type="button" onclick="closeModal('editModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Update Slot</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, slotNumber, type, status, isActive) {
    const form = document.getElementById('editForm');
    form.action = `/admin/slots/${id}`;
    document.getElementById('edit_slot_number').value = slotNumber;
    document.getElementById('edit_type').value = type;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_is_active').checked = isActive;
    openModal('editModal');
}

function confirmDelete(id, slotNumber) {
    if(confirm(`Delete slot ${slotNumber}? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/slots/${id}`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
}
function confirmArchive(id, slotNumber) {
    if(confirm(`Archive slot ${slotNumber}? This slot will be moved to archive and won't appear in active slots. You can restore it later.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/slots/${id}/archive`;
        form.innerHTML = `@csrf`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection