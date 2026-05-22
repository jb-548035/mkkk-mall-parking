@extends('layouts.admin-sidebar')

@section('title', 'Create Parking Slot')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Create New Parking Slot</h1>
    
    <div class="card-container">
        <form action="{{ route('admin.slots.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Slot Number</label>
                <input type="text" name="slot_number" value="{{ old('slot_number') }}" 
                    class="form-control" required>
                <p class="text-sm text-gray-500 mt-1">Example: A01, W03, D02</p>
                @error('slot_number') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Slot Type</label>
                <select name="type" class="form-control" required>
                    <option value="standard">Standard</option>
                    <option value="wheelchair">Wheelchair Accessible</option>
                    <option value="delivery">Delivery</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Initial Status</label>
                <select name="status" class="form-control" required>
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="form-checkbox">
                    <span class="text-gray-700">Active (slot can be used)</span>
                </label>
            </div>
            
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.slots.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Slot</button>
            </div>
        </form>
    </div>
</div>
@endsection