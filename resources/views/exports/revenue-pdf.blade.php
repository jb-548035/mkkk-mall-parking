<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue Report - MKKK Mall</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        h1 {
            color: #16a34a;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .report-header {
            margin-bottom: 20px;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #16a34a;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .total-box {
            background: #dcfce7;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>MKKK Mall Parking Management System</h1>
    
    <div class="report-header">
        <p><strong>Report Type:</strong> Revenue Report</p>
        <p><strong>Period:</strong> {{ $startDate ?? 'N/A' }} to {{ $endDate ?? 'N/A' }}</p>
        <p><strong>Generated:</strong> {{ now()->format('F d, Y h:i:s A') }}</p>
    </div>
    
    <div class="total-box">
        <strong>Total Revenue: ₱{{ number_format($totalAmount ?? 0, 2) }}</strong>
    </div>
    
    @if(isset($totalCount) && $totalCount > 100)
    <div style="background: #fef3c7; padding: 10px; margin: 15px 0; border-radius: 5px; text-align: center;">
        <p style="color: #92400e; font-size: 11px;">
            ⚠️ Showing last 100 transactions only. Total transactions in period: {{ $totalCount }}. 
            Use CSV export for complete data.
        </p>
    </div>
    @endif    
    
    <h2>Transaction Details</h2>
    
    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Plate Number</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Processed By</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($transactions ?? []) as $t)
            <tr>
                <td>{{ $t->created_at ? $t->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                <td>{{ $t->ticket->plate_number ?? 'N/A' }}</td>
                <td>₱{{ number_format($t->amount ?? 0, 2) }}</td>
                <td>{{ $t->payment_method == 'e_wallet' ? 'E-WALLET' : strtoupper($t->payment_method ?? 'N/A') }}</td>
                <td>{{ $t->processedBy->name ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No transactions found for the selected period</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        MKKK Mall Parking Management System - Official Report<br>
        MacArthur Highway, Corner Don Julian Rodriguez Sr. Ave, Davao City
    </div>
</body>
</html>