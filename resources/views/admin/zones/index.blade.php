@extends('layouts.admin-sidebar')

@section('title', 'Manage Zones')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Zones</span>
@endsection

@section('content')
<div class="page-stack">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Parking Zones Management</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.zones.archived') }}" class="btn-secondary flex items-center gap-2">
                <i class="fas fa-archive"></i> Archived Zones
            </a>
            <a href="{{ route('admin.zones.create') }}" class="btn-primary flex items-center gap-2">
                <i class="fas fa-plus"></i> Add New Zone
            </a>
        </div>
    </div>    

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-600"></i>
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @foreach($zones as $zone)
        <div class="card-container overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-layer-group text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Zone {{ $zone->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $zone->description ?? 'No description' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.zones.edit', $zone) }}" class="icon-btn p-8" title="Edit Zone">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.zones.archive', $zone) }}" method="POST" class="inline" onsubmit="return confirmArchive('{{ $zone->name }}')">
                            @csrf
                            <button type="submit" class="icon-btn p-8 border-red-300 text-red-600 hover:bg-red-200" title="Archive Zone">
                                <i class="fas fa-archive"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-5">
                <!-- Stats Grid -->
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

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-4">
                        <span class="text-gray-600">Occupancy Rate</span>
                        <span class="font-semibold">{{ $zone->getOccupancyRate() }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $zone->getOccupancyRate() }}%"></div>
                    </div>
                </div>

                <!-- Slot Type Breakdown -->
                <div class="space-y-8">
                    <div class="flex items-center justify-between text-sm">
                        <span><i class="fas fa-car text-gray-400 mr-8"></i>Standard</span>
                        <span>{{ $zone->standardSlots()->count() }} slots</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span><i class="fas fa-wheelchair text-blue-500 mr-8"></i>PWD (Accessible)</span>
                        <span>{{ $zone->pwd_slots }} slots ({{ $zone->pwdSlots()->where('status', 'available')->count() }} available)</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span><i class="fas fa-truck text-orange-500 mr-8"></i>Delivery</span>
                        <span>{{ $zone->delivery_slots }} slots</span>
                    </div>
                </div>

                <!-- Regenerate Slots - Fixed with POST form -->
                <div class="mt-16 pt-4 border-t border-gray-100">
                    <form action="{{ route('admin.zones.regenerate', $zone) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-yellow-600 hover:underline flex items-center gap-4" onclick="return confirm('Regenerate all slots in this zone? This will reset all slot statuses.')">
                            <i class="fas fa-sync-alt"></i> Regenerate Slots
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($zones->isEmpty())
    <div class="bg-white rounded-xl p-40 text-center border border-gray-100">
        <i class="fas fa-layer-group text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500">No zones created yet. Create your first parking zone!</p>
        <a href="{{ route('admin.zones.create') }}" class="inline-block mt-16 text-blue-600 hover:underline">Create Zone →</a>
    </div>
    @endif
</div>
<script>
function confirmArchive(zoneName) {
    return confirm(`Archive Zone ${zoneName}? All parking slots in this zone will also be archived. You can restore them later.`);
}    
</script>
@endsection