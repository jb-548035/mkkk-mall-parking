@extends('layouts.admin-sidebar')

@section('title', 'Dashboard')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}" class="text-gray-500">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Overview</span>
@endsection

@section('content')
<div class="page-stack">
    <!-- KPI Cards - 8-Point Grid -->
    <div class="stats-grid">
        <x-stat-card title="Vehicles Parked" :value="$occupied" icon="car" color="blue" />
        <x-stat-card title="Available Slots" :value="$available" icon="parking" color="green" />
        <x-stat-card title="Occupancy Rate" :value="$occupancyRate . '%'" icon="chart-line" color="purple" />
        <x-stat-card title="Today's Revenue" :value="'₱' . number_format($todayRevenue, 2)" icon="wallet" color="orange" />
    </div>
    
    <!-- Charts Row -->
    <div class="charts-grid">
        <!-- Daily Occupancy Chart -->
        <div class="card-container chart-card p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 mb-5">
                <h3 class="font-semibold text-gray-900">Daily Occupancy Trend</h3>
                <select id="chartRange" class="form-control text-sm max-w-[180px]">
                    <option value="7">Last 7 Days</option>
                    <option value="14">Last 14 Days</option>
                    <option value="30">Last 30 Days</option>
                </select>
            </div>
            <canvas id="occupancyChart" height="200"></canvas>
        </div>
        
        <!-- Revenue Chart -->
        <div class="card-container chart-card p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 mb-5">
                <h3 class="font-semibold text-gray-900">Revenue Trend</h3>
                <select id="revenueRange" class="form-control text-sm max-w-[180px]">
                    <option value="7">Last 7 Days</option>
                    <option value="14">Last 14 Days</option>
                    <option value="30">Last 30 Days</option>
                </select>
            </div>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Zone Summary -->
    <div class="card-container p-5">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-900">Zone Summary</h3>
            <p class="text-sm text-gray-500">Overview of real-time availability</p>
        </div>
        <div class="zone-cards-grid">
            @foreach($zones as $zone)
            <div class="card-container p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-gray-900">Zone {{ $zone['name'] }}</p>
                    <i class="fas fa-map-marker-alt text-gray-400 text-sm"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-4">{{ $zone['occupied'] }}/{{ $zone['total'] }}</p>
                <div class="progress-bar mb-4">
                    <div class="progress-bar-fill" style="width: {{ $zone['percentage'] }}%"></div>
                </div>
                <p class="caption mt-8">{{ $zone['available'] }} slots available</p>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="card-container overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Recent Activity</h3>
            <a href="{{ route('admin.activity.logs') }}" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentActivities as $activity)
            <div class="px-5 py-4 flex items-center gap-4 transition-all hover:bg-gray-50">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas {{ $activity['icon'] }} text-gray-500 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-800">{{ $activity['description'] }}</p>
                    <p class="caption">{{ $activity['time'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Declare chart instances globally to allow dynamic access
let occupancyChartInstance;
let revenueChartInstance;

// Initialize Occupancy Line Chart
const ctx1 = document.getElementById('occupancyChart').getContext('2d');
occupancyChartInstance = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Occupied Slots',
            data: {!! json_encode($occupancyData) !!},
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.05)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
});

// Initialize Revenue Bar Chart
const ctx2 = document.getElementById('revenueChart').getContext('2d');
revenueChartInstance = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: {!! json_encode($revenueLabels) !!},
        datasets: [{
            label: 'Revenue (₱)',
            data: {!! json_encode($revenueData) !!},
            backgroundColor: '#10b981',
            borderRadius: 8
        }]
    },
    options: { responsive: true, maintainAspectRatio: true }
});

// Occupancy Filter Event Listener
document.getElementById('chartRange').addEventListener('change', function () {
    const selectedDays = this.value;
    
    fetch(`{{ route('admin.dashboard.occupancy-trend') }}?days=${selectedDays}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(payload => {
        occupancyChartInstance.data.labels = payload.labels;
        occupancyChartInstance.data.datasets[0].data = payload.data;
        occupancyChartInstance.update();
    })
    .catch(err => console.error("Error updating occupancy chart values:", err));
});

// NEW: Revenue Filter Event Listener
document.getElementById('revenueRange').addEventListener('change', function () {
    const selectedDays = this.value;
    
    fetch(`{{ route('admin.dashboard.revenue-trend') }}?days=${selectedDays}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(payload => {
        revenueChartInstance.data.labels = payload.labels;
        revenueChartInstance.data.datasets[0].data = payload.data;
        revenueChartInstance.update();
    })
    .catch(err => console.error("Error updating revenue chart values:", err));
});
</script>
@endsection