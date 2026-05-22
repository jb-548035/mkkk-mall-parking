@extends('layouts.admin-sidebar')

@section('title', 'Reports Dashboard')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Reports & Analytics</span>
@endsection

@section('content')
<div class="page-stack">
    <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
    
    <!-- Two Column Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Today's Summary Card -->
        <div class="card-container hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-calendar-day text-blue-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Today's Summary</h3>
            </div>
            <div class="section-stack">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Entries</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $todayEntries ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Exits</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $todayExits ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Currently Parked</span>
                    <span class="text-sm font-semibold text-blue-600">{{ $currentlyParked ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Peak Occupancy</span>
                    <span class="text-sm font-semibold text-orange-600">{{ $peakOccupancy ?? 0 }}%</span>
                </div>
            </div>
        </div>

        <!-- Weekly Trends Card -->
        <div class="card-container hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Weekly Trends</h3>
            </div>
            <div class="section-stack">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Avg. Daily Entries</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $avgDailyEntries ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Busiest Day</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $busiestDay ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Avg. Occupancy</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $avgOccupancy ?? 0 }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">PWD Slot Usage</span>
                    <div class="flex items-center gap-2">
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $pwdUsage ?? 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $pwdUsage ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stats Cards (Existing) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-container">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
                <p class="text-sm text-gray-500">Total Revenue</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">₱{{ number_format($totalRevenue, 2) }}</p>
            <p class="text-xs text-gray-500 mt-8">All time</p>
        </div>
        
        <div class="card-container">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-chart-simple text-blue-600"></i>
                </div>
                <p class="text-sm text-gray-500">Today's Revenue</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">₱{{ number_format($todayRevenue, 2) }}</p>
            <p class="text-xs text-gray-500 mt-8">{{ $todayTickets }} tickets today</p>
        </div>
        
        <div class="card-container">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-percent text-orange-600"></i>
                </div>
                <p class="text-sm text-gray-500">Occupancy Rate</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $occupancyRate }}%</p>
            <p class="text-xs text-gray-500 mt-8">{{ $occupiedSlots }}/{{ $totalSlots }} slots occupied</p>
        </div>
        
        <div class="card-container">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-truck text-purple-600"></i>
                </div>
                <p class="text-sm text-gray-500">Delivery Vehicles</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $deliveryTickets }}</p>
            <p class="text-xs text-gray-500 mt-8">Free parking provided</p>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="card-container">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
                <h2 class="font-semibold text-gray-900">Last 7 Days Revenue</h2>
            </div>
            <a href="{{ route('admin.reports.revenue') }}" class="btn-secondary flex items-center gap-2">View Details →</a>
        </div>
        
        @if($last7Days->count() > 0)
        <div class="h-64 flex items-end gap-4">
            @php
                // Get the max revenue value from the collection
                $maxRevenue = $last7Days->max('revenue');
                $maxRevenue = $maxRevenue > 0 ? $maxRevenue : 1;
            @endphp
            @foreach($last7Days as $day)
            <div class="flex-1 flex flex-col items-center group">
                @php
                    $barHeight = max(20, ($day['revenue'] / $maxRevenue) * 180);
                    $fillPercentage = ($day['revenue'] / $maxRevenue) * 100;
                @endphp
                <div class="w-full bg-blue-100 rounded-t-lg transition-all duration-300 group-hover:bg-blue-200" style="height: {{ $barHeight }}px;">
                    <div class="bg-blue-600 rounded-t-lg h-full transition-all duration-300" style="width: 100%; height: {{ $fillPercentage }}%"></div>
                </div>
                <p class="text-xs mt-8 text-gray-500">{{ $day['date'] }}</p>
                <p class="text-xs font-semibold text-gray-700">₱{{ number_format($day['revenue'], 0) }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-chart-line text-4xl mb-8 text-gray-300"></i>
            <p>No revenue data available for the last 7 days</p>
        </div>
        @endif
    </div>

    <!-- Export Reports Section (Figma Design) -->
    <div class="card-container">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                <i class="fas fa-file-export text-green-600"></i>
            </div>
            <h3 class="font-semibold text-gray-900">Export Reports</h3>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <a href="{{ route('admin.reports.export', ['type' => 'revenue', 'format' => 'csv']) }}" 
               class="flex items-center justify-center gap-2 px-16 py-12 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
                <i class="fas fa-file-csv text-green-600"></i>
                <span>Daily Report (CSV)</span>
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'revenue', 'format' => 'pdf']) }}" 
               class="flex items-center justify-center gap-2 px-16 py-12 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
                <i class="fas fa-file-pdf text-red-600"></i>
                <span>Weekly Report (PDF)</span>
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'occupancy', 'format' => 'csv']) }}" 
               class="flex items-center justify-center gap-2 px-16 py-12 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
                <i class="fas fa-file-excel text-green-700"></i>
                <span>Monthly Report (Excel)</span>
            </a>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                    <i class="fas fa-chart-simple text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Revenue Analysis</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">View detailed revenue breakdown by date range with filtering options.</p>
            <a href="{{ route('admin.reports.revenue') }}" class="inline-flex items-center gap-2 btn-secondary flex items-center gap-2">
                View Revenue Report <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-green-600 flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-sm"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Occupancy Analytics</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Analyze parking utilization, peak hours, and zone performance.</p>
            <a href="{{ route('admin.reports.occupancy') }}" class="inline-flex items-center gap-2 btn-secondary flex items-center gap-2">
                View Occupancy Report <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>
@endsection