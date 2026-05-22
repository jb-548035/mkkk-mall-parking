@extends('layouts.admin-sidebar')

@section('title', 'Occupancy Report')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<a href="{{ route('admin.reports.index') }}">Reports</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Occupancy</span>
@endsection

@section('content')
<div class="page-stack">
    <div class="flex justify-between items-center">
        <h1>Occupancy Report</h1>
        <a href="{{ route('admin.reports.index') }}" class="btn-secondary flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
    
    <!-- Date Filter -->
    <div class="card-container">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm text-gray-700 mb-4">Date</label>
                <input type="date" name="date" value="{{ $date }}" class="form-control">
            </div>
            <div>
                <button type="submit" class="btn-primary">Filter</button>
            </div>
            <div>
                <a href="{{ route('admin.reports.export', ['type' => 'occupancy', 'format' => 'csv']) . '?date=' . $date }}" 
                   class="btn-secondary inline-block">Export CSV</a>
            </div>
            <div>
                <a href="{{ route('admin.reports.export', ['type' => 'occupancy', 'format' => 'pdf']) . '?date=' . $date }}" 
                   class="btn-secondary inline-block">Export PDF</a>
            </div>
        </form>
    </div>
    
    <!-- Hourly Occupancy Chart -->
    <div class="card-container">
        <h3 class="font-semibold text-gray-900 mb-5">Hourly Occupancy ({{ $date }})</h3>
        <div class="h-64 flex items-end gap-4">
            @php
                $maxActive = !empty($hourlyData) ? max(array_column($hourlyData, 'active')) : 1;
            @endphp
            @foreach($hourlyData as $data)
            <div class="flex-1 flex flex-col items-center">
                @php
                    $heightPercent = $maxActive > 0 ? ($data['active'] / $maxActive) * 100 : 0;
                @endphp
                <div class="w-full bg-blue-200 rounded-t-lg transition-all" style="height: {{ max(20, $heightPercent * 2) }}px;">
                    <div class="bg-blue-600 rounded-t-lg h-full transition-all" style="height: {{ $heightPercent }}%"></div>
                </div>
                <p class="text-xs mt-8 text-gray-500">{{ $data['hour'] }}</p>
                <p class="text-xs font-semibold text-gray-700">{{ $data['active'] }}/{{ $data['capacity'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Top Frequent Parkers -->
    <div class="card-container overflow-hidden">
        <h3 class="font-semibold text-gray-900 p-5 border-b border-gray-100">Top 10 Frequent Parkers</h3>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Plate Number</th>
                        <th>Visit Count</th>
                        <th>Total Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topVehicles as $index => $vehicle)
                    <tr>
                        <td class="font-semibold">{{ $index + 1 }}</td>
                        <td class="font-mono">{{ $vehicle->plate_number }}</td>
                        <td>{{ $vehicle->visit_count }} times</td>
                        <td>₱{{ number_format($vehicle->total_paid, 2) }}</td>
                    </tr>
                    @endforeach
                    @if($topVehicles->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">No parking data available</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection