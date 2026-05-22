@extends('layouts.admin-sidebar')

@section('title', 'Edit Zone')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<a href="{{ route('admin.zones.index') }}">Zones</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Edit {{ $zone->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-5">Edit Zone: {{ $zone->name }}</h1>

    <div class="card-container">
        <form action="{{ route('admin.zones.update', $zone) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="section-stack">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Zone Name</label>
                    <input type="text" name="name" value="{{ old('name', $zone->name) }}" class="form-control" required>
                    @error('name') <p class="text-red-500 text-sm mt-4">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description', $zone->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">Total Slots</label>
                        <input type="number" name="total_slots" value="{{ old('total_slots', $zone->total_slots) }}" class="form-control" required>
                        <p class="text-xs text-red-500 mt-4">Current slots: {{ $zone->parkingSlots()->count() }}. Cannot be less than this.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">PWD Slots</label>
                        <input type="number" name="pwd_slots" value="{{ old('pwd_slots', $zone->pwd_slots) }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">Delivery Slots</label>
                        <input type="number" name="delivery_slots" value="{{ old('delivery_slots', $zone->delivery_slots) }}" class="form-control" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $zone->sort_order) }}" class="form-control">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ $zone->is_active ? 'checked' : '' }} id="is_active" class="form-checkbox">
                    <label for="is_active" class="text-sm text-gray-700">Zone Active</label>
                </div>

                <div class="flex justify-end gap-3 pt-8">
                    <a href="{{ route('admin.zones.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Update Zone</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection