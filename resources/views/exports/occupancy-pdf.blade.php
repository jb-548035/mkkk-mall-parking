<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Occupancy Report - MKKK Mall</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        h1 {
            color: #1a56db;
            border-bottom: 3px solid #1a56db;
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
            background-color: #1a56db;
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
        .summary-box {
            background: #e0f2fe;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <h1>MKKK Mall Parking Management System</h1>
    
    <div class="report-header">
        <p><strong>Report Type:</strong> Occupancy Report</p>
        <p><strong>Date:</strong> {{ $date ?? 'N/A' }}</p>
        <p><strong>Generated:</strong> {{ now()->format('F d, Y h:i:s A') }}</p>
    </div>
    
    <div class="summary-box">
        <strong>Summary:</strong> Total parking capacity is {{ isset($hourlyData[0]['capacity']) ? $hourlyData[0]['capacity'] : 0 }} slots.
    </div>
    
    <h2>Hourly Occupancy Data</h2>
    
    <table>
        <thead>
            <tr>
                <th>Hour</th>
                <th>Active Tickets</th>
                <th>Total Capacity</th>
                <th>Occupancy Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($hourlyData ?? []) as $data)
            <tr>
                <td>{{ $data['hour'] ?? 'N/A' }}</td>
                <td>{{ $data['active'] ?? 0 }}</td>
                <td>{{ $data['capacity'] ?? 0 }}</td>
                <td>
                    @php
                        $capacity = $data['capacity'] ?? 0;
                        $active = $data['active'] ?? 0;
                        $rate = $capacity > 0 ? round(($active / $capacity) * 100, 1) : 0;
                    @endphp
                    {{ $rate }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">No occupancy data available</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if(isset($topVehicles) && $topVehicles->count() > 0)
    <h2 style="margin-top: 30px;">Top 10 Frequent Parkers</h2>
    <table>
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
                <td>{{ $index + 1 }}</td>
                <td>{{ $vehicle->plate_number }}</td>
                <td>{{ $vehicle->visit_count }} times</td>
                <td>₱{{ number_format($vehicle->total_paid ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <div class="footer">
        MKKK Mall Parking Management System - Official Report<br>
        MacArthur Highway, Corner Don Julian Rodriguez Sr. Ave, Davao City
    </div>
</body>
</html>