@extends('layouts.guard-sidebar')

@section('title', 'Dashboard')
@section('breadcrumbs')
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-800 font-medium">Dashboard</span>
@endsection

@section('content')
@php
    $capacityAccent = $availableSlots <= 5 ? 'red' : ($availableSlots <= 15 ? 'amber' : 'green');
@endphp
<div class="page-stack">
    <header class="guard-page-header">
        <h1>Security Operations Dashboard</h1>
        <p>Real-time gate metrics, parking flow, and shift activity at a glance.</p>
    </header>

    <div class="stats-grid">
        <x-guard-kpi-card
            title="Total Vehicles Inside"
            :value="$totalInside"
            icon="car"
            accent="indigo"
            hint="Active tickets"
        />
        <x-guard-kpi-card
            title="Today's Total Entries"
            :value="$todayEntries"
            icon="right-to-bracket"
            accent="blue"
            hint="Since midnight"
        />
        <x-guard-kpi-card
            title="Remaining Capacity"
            :value="$availableSlots"
            icon="square-parking"
            :accent="$capacityAccent"
            :hint="'of ' . $totalCapacity . ' slots'"
        />
        <x-guard-kpi-card
            title="Fees Collected Today"
            :value="'₱' . number_format($feesToday, 2)"
            icon="coins"
            accent="amber"
            hint="Completed payments"
        />
    </div>

    <div class="charts-grid">
        <div class="guard-chart-card">
            <h3><i class="fas fa-chart-line text-blue-600 mr-2"></i>Peak Parking Hours</h3>
            <canvas id="guardPeakChart" height="200" aria-label="Peak parking hours chart"></canvas>
        </div>
        <div class="guard-chart-card">
            <h3><i class="fas fa-chart-pie text-indigo-600 mr-2"></i>Vehicle Type Breakdown</h3>
            <canvas id="guardTypeChart" height="200" aria-label="Vehicle type breakdown chart"></canvas>
        </div>
    </div>

    <div class="card-container p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h3 class="font-semibold text-gray-900 m-0">Quick Actions</h3>
                <p class="caption mt-1 mb-0">Jump to common guard workflows</p>
            </div>
        </div>
        <div class="guard-quick-actions">
            <a href="{{ route('guard.entry.form') }}" class="guard-quick-btn">
                <i class="fas fa-ticket"></i>
                <span>Issue Ticket</span>
            </a>
            <a href="{{ route('guard.exit.form') }}" class="guard-quick-btn">
                <i class="fas fa-qrcode"></i>
                <span>Scan QR Exit</span>
            </a>
            <a href="#recent-activity" class="guard-quick-btn">
                <i class="fas fa-list-ul"></i>
                <span>View Active Parking Log</span>
            </a>
        </div>
    </div>

    <div class="card-container overflow-hidden" id="recent-activity">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="font-semibold text-gray-900 m-0">Recent Activity</h3>
                <p class="caption mt-1 mb-0">Last 5 vehicle entries and exits</p>
            </div>
            <a href="{{ route('guard.entry.form') }}" class="text-sm text-blue-600 hover:underline font-medium">New Entry</a>
        </div>
        <div class="table-wrap">
            <table class="data-table min-w-full">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Plate</th>
                        <th>Ticket</th>
                        <th>Slot</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivity as $ticket)
                    <tr>
                        <td>{{ $ticket->entry_time?->format('M d, H:i') ?? $ticket->created_at->format('M d, H:i') }}</td>
                        <td class="font-semibold">{{ $ticket->plate_number }}</td>
                        <td class="font-mono text-xs">{{ Str::limit($ticket->qr_code, 12) }}</td>
                        <td>{{ $ticket->parkingSlot?->slot_number ?? '—' }}</td>
                        <td>
                            @if($ticket->is_delivery && $ticket->status === 'active')
                                <span class="guard-badge guard-badge--delivery">Delivery</span>
                            @elseif($ticket->status === 'active')
                                <span class="guard-badge guard-badge--inside">Inside</span>
                            @else
                                <span class="guard-badge guard-badge--exited">Exited</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-8">No recent activity yet today.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const peakCtx = document.getElementById('guardPeakChart');
    if (peakCtx) {
        new Chart(peakCtx, {
            type: 'line',
            data: {
                labels: @json($peakLabels),
                datasets: [{
                    label: 'Entries',
                    data: @json($peakData),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    const typeCtx = document.getElementById('guardTypeChart');
    if (typeCtx) {
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($typeCounts)),
                datasets: [{
                    data: @json(array_values($typeCounts)),
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16 } },
                },
            },
        });
    }
})();
</script>
@endsection
