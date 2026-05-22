@extends('layouts.admin-sidebar')

@section('title', 'Revenue Report')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<a href="{{ route('admin.reports.index') }}">Reports</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Revenue</span>
@endsection

@section('content')
<div class="page-stack">
    <div class="flex justify-between items-center">
        <h1>Revenue Report</h1>
        <a href="{{ route('admin.reports.index') }}" class="btn-secondary flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
    
    <!-- Date Filter -->
    <div class="card-container">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm text-gray-700 mb-4">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-4">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
            </div>
            <div>
                <button type="submit" class="btn-primary">Filter</button>
            </div>
            <div>
                <a href="{{ route('admin.reports.export', ['type' => 'revenue', 'format' => 'csv']) . '?start_date=' . $startDate . '&end_date=' . $endDate }}" 
                   class="btn-secondary inline-block">Export CSV</a>
            </div>
            <div>
                <a href="{{ route('admin.reports.export', ['type' => 'revenue', 'format' => 'pdf']) . '?start_date=' . $startDate . '&end_date=' . $endDate }}" 
                   class="btn-secondary inline-block">Export PDF</a>
            </div>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-container">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
                <p class="text-sm text-gray-500">Total Revenue</p>
            </div>
            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalAmount, 2) }}</p>
        </div>
        <div class="card-container">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-blue-600"></i>
                </div>
                <p class="text-sm text-gray-500">Cash</p>
            </div>
            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($cashTotal, 2) }}</p>
        </div>
        <div class="card-container">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-credit-card text-purple-600"></i>
                </div>
                <p class="text-sm text-gray-500">Card</p>
            </div>
            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($cardTotal, 2) }}</p>
        </div>
        <div class="card-container">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-mobile-alt text-orange-600"></i>
                </div>
                <p class="text-sm text-gray-500">E-Wallet</p>
            </div>
            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($e_walletTotal, 2) }}</p>
        </div>
    </div>
    
    <!-- Transactions Table -->
    <div class="card-container overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Plate #</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $t->ticket->plate_number ?? 'N/A' }}</td>
                        <td class="font-semibold">₱{{ number_format($t->amount, 2) }}</td>
                        <td>
                            <span class="px-8 py-2 rounded-full text-xs 
                                @if($t->payment_method == 'cash') bg-green-100 text-green-700
                                @elseif($t->payment_method == 'card') bg-purple-100 text-purple-700
                                @elseif($t->payment_method == 'e_wallet') bg-orange-100 text-orange-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $t->payment_method == 'e_wallet' ? 'E-WALLET' : strtoupper($t->payment_method) }}
                            </span>
                        </td>
                        <td>{{ $t->processedBy->name ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500">No transactions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-5">{{ $transactions->appends(request()->query())->links() }}</div>
</div>
@endsection