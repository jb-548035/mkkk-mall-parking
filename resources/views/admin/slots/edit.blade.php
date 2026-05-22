@extends('layouts.admin-sidebar')

@section('title', 'Edit Parking Slot')

@section('content')
<!-- Outer wrapper locked to 70% of the screen height -->
<div class="max-w-2xl mx-auto px-4 h-[70vh] flex flex-col">
    <!-- Fixed Page/Modal Title Header -->
    <h1 class="text-2xl font-bold mb-4 flex-none">Edit Parking Slot: {{ $slot->slot_number }}</h1>
    
    <!-- Turn card-container into a proper flexbox wrapper -->
    <div class="card-container flex-1 flex flex-col min-h-0 bg-white p-0 overflow-hidden">
        <form action="{{ route('admin.slots.update', $slot) }}" method="POST" class="flex flex-col flex-1 min-h-0">
            @csrf
            @method('PUT')
            
            <!-- 1. SCROLLABLE BODY ONLY (Contains inputs, NO buttons) -->
            <div class="flex-1 overflow-y-auto p-6 min-h-0">            
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Slot Number</label>
                    <input type="text" name="slot_number" value="{{ old('slot_number', $slot->slot_number) }}" 
                        class="form-control w-full" required>
                    @error('slot_number') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Slot Type</label>
                    <select name="type" class="form-control w-full" required>
                        <option value="standard" {{ $slot->type == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="wheelchair" {{ $slot->type == 'wheelchair' ? 'selected' : '' }}>Wheelchair Accessible</option>
                        <option value="delivery" {{ $slot->type == 'delivery' ? 'selected' : '' }}>Delivery</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Status</label>
                    <select name="status" class="form-control w-full" required>
                        <option value="available" {{ $slot->status == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ $slot->status == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ $slot->is_active ? 'checked' : '' }} class="form-checkbox">
                        <span class="text-gray-700">Active (slot can be used)</span>
                    </label>
                </div>
            </div>
            
            <!-- 2. PINNED BOTTOM FOOTER (Completely outside the scroll wrapper) -->
            <div class="flex justify-end gap-4 p-4 border-t border-gray-200 flex-none bg-gray-50 rounded-b-lg">
                <a href="{{ route('admin.slots.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Slot</button>
            </div>
        </form>
    </div>
</div>
@endsection
