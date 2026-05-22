@extends('layouts.admin-sidebar')

@section('title', 'Create Zone')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<a href="{{ route('admin.zones.index') }}">Zones</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Create</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-5">Create New Parking Zone</h1>

    <div class="card-container">
        <form action="{{ route('admin.zones.store') }}" method="POST">
            @csrf

            <div class="section-stack">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Zone Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="e.g., A, B, C, North, South" required>
                    <p class="text-xs text-gray-500 mt-4">Single letter or short name (max 10 characters)</p>
                    @error('name') <p class="text-red-500 text-sm mt-4">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Description (Optional)</label>
                    <textarea name="description" rows="2" class="form-control" placeholder="e.g., Main entrance area, Near elevators...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">Total Slots</label>
                        <input type="number" name="total_slots" value="{{ old('total_slots', 50) }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">PWD Slots</label>
                        <input type="number" name="pwd_slots" value="{{ old('pwd_slots', 5) }}" class="form-control" required>
                        <p class="text-xs text-gray-500 mt-4">Dedicated accessible parking</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">Delivery Slots</label>
                        <input type="number" name="delivery_slots" value="{{ old('delivery_slots', 0) }}" class="form-control" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control">
                    <p class="text-xs text-gray-500 mt-4">Lower numbers appear first</p>
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-4"></i>
                        <div class="text-sm text-gray-600">
                            <p class="font-semibold text-gray-800 mb-4">Auto-generation Rules:</p>
                            <ul class="space-y-4 list-disc list-inside">
                                <li>PWD slots: First {{ old('pwd_slots', 5) }} slots will be PWD-accessible</li>
                                <li>Delivery slots: Next {{ old('delivery_slots', 0) }} slots will be for delivery vehicles</li>
                                <li>Remaining slots: {{ old('total_slots', 50) - old('pwd_slots', 5) - old('delivery_slots', 0) }} standard slots</li>
                                <li>Slot numbers format: [ZONE NAME][NUMBER] e.g., A01, A02</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-8">
                    <a href="{{ route('admin.zones.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Create Zone</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection