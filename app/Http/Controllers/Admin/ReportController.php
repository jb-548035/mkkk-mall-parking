<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\ParkingSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
class ReportController extends Controller
{
    public function index()
    {
        // Summary statistics
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $totalTickets = Ticket::count();
        $activeTickets = Ticket::where('status', 'active')->count();
        $completedTickets = Ticket::where('status', 'exited')->count();
        $deliveryTickets = Ticket::where('is_delivery', true)->count();
        
        // Today's metrics
        $todayRevenue = Transaction::whereDate('created_at', today())->sum('amount');
        $todayTickets = Ticket::whereDate('created_at', today())->count();
        $todayEntries = Ticket::whereDate('entry_time', today())->count();
        $todayExits = Ticket::whereDate('exit_time', today())->count();
        $currentlyParked = Ticket::where('status', 'active')->count();
        
        // Occupancy metrics
        $totalSlots = ParkingSlot::count();
        $occupiedSlots = ParkingSlot::where('status', 'occupied')->count();
        $occupancyRate = $totalSlots > 0 ? round(($occupiedSlots / $totalSlots) * 100, 1) : 0;
        
        // Peak occupancy calculation (last 7 days max occupancy)
        $peakOccupancy = 0;
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyActive = Ticket::whereDate('entry_time', '<=', $date)
                ->where(function($q) use ($date) {
                    $q->whereNull('exit_time')->orWhereDate('exit_time', '>=', $date);
                })->count();
            $dailyOccupancy = $totalSlots > 0 ? round(($dailyActive / $totalSlots) * 100, 1) : 0;
            if ($dailyOccupancy > $peakOccupancy) $peakOccupancy = $dailyOccupancy;
        }
        
        // Weekly trends
        $avgDailyEntries = 0;
        $busiestDay = 'N/A';
        $dayCounts = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Ticket::whereDate('entry_time', $date)->count();
            $dayName = $date->format('l');
            $dayCounts[$dayName] += $count;
            $avgDailyEntries += $count;
        }
        $avgDailyEntries = round($avgDailyEntries / 7, 1);
        $busiestDay = array_keys($dayCounts, max($dayCounts))[0];
        
        // Average occupancy for last 7 days
        $avgOccupancy = 0;
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyActive = Ticket::whereDate('entry_time', '<=', $date)
                ->where(function($q) use ($date) {
                    $q->whereNull('exit_time')->orWhereDate('exit_time', '>=', $date);
                })->count();
            $dailyOccupancy = $totalSlots > 0 ? round(($dailyActive / $totalSlots) * 100, 1) : 0;
            $avgOccupancy += $dailyOccupancy;
        }
        $avgOccupancy = round($avgOccupancy / 7, 1);
        
        // PWD slot usage
        $totalPwdSlots = ParkingSlot::where('type', 'wheelchair')->count();
        $occupiedPwdSlots = ParkingSlot::where('type', 'wheelchair')->where('status', 'occupied')->count();
        $pwdUsage = $totalPwdSlots > 0 ? round(($occupiedPwdSlots / $totalPwdSlots) * 100, 1) : 0;
        
        // Last 7 days revenue chart data
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Transaction::whereDate('created_at', $date)->sum('amount');
            $last7Days->push([
                'date' => $date->format('D, M j'),
                'revenue' => $revenue,
            ]);
        }
        
        return view('admin.reports.index', compact(
            'totalRevenue', 'totalTickets', 'activeTickets', 'completedTickets', 'deliveryTickets',
            'todayRevenue', 'todayTickets', 'todayEntries', 'todayExits', 'currentlyParked',
            'occupancyRate', 'occupiedSlots', 'totalSlots', 'peakOccupancy',
            'avgDailyEntries', 'busiestDay', 'avgOccupancy', 'pwdUsage',
            'last7Days'
        ));
    }    

    public function getOccupancyTrend(Request $request)
    {
        // Capture user selection dropdown query or default to 7 days
        $days = intval($request->get('days', 7));
        
        $chartLabels = [];
        $occupancyData = [];
        
        $totalSlots = \App\Models\ParkingSlot::count();
        
        // Loop backwards to build date matrices sequentially matching your calculation style
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('M d');
            
            // Exact mirror calculation logic from your index framework
            $dailyActive = \App\Models\Ticket::whereDate('entry_time', '<=', $date)
                ->where(function($q) use ($date) {
                    $q->whereNull('exit_time')
                    ->orWhereDate('exit_time', '>=', $date);
                })->count();
                
            $occupancyData[] = $dailyActive;
        }
        
        return response()->json([
            'labels' => $chartLabels,
            'data' => $occupancyData,
        ]);
    }    

    public function getRevenueTrend(Request $request)
    {
        // Capture user selection dropdown query or default to 7 days
        $days = intval($request->get('days', 7));
        
        $revenueLabels = [];
        $revenueData = [];
        
        // Loop backwards to build date matrices sequentially matching your calculation style
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Match the label format used on your initial dashboard charts (e.g., "Mon, May 22")
            $revenueLabels[] = $date->format('D, M j');
            
            // Sum the completed transaction sums for that specific day
            $revenue = \App\Models\Transaction::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('amount');
                
            $revenueData[] = floatval($revenue);
        }
        
        return response()->json([
            'labels' => $revenueLabels,
            'data' => $revenueData,
        ]);
    }

    public function revenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $transactions = Transaction::with('ticket', 'processedBy')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $totalAmount = Transaction::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('amount');
        
        $cashTotal = Transaction::where('payment_method', 'cash')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('amount');
        
        $cardTotal = Transaction::where('payment_method', 'card')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('amount');
        
        $e_walletTotal = Transaction::where('payment_method', 'e_wallet')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('amount');
        
        return view('admin.reports.revenue', compact(
            'transactions', 'totalAmount', 'cashTotal', 'cardTotal', 'e_walletTotal',
            'startDate', 'endDate'
        ));
    }
    
    public function occupancy(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        
        // Hourly occupancy for the selected date
        $hourlyData = [];
        for ($hour = 9; $hour <= 20; $hour++) { // Mall hours 9 AM - 8 PM
            $hourStart = "$date $hour:00:00";
            $hourEnd = "$date " . ($hour + 1) . ":00:00";
            
            $activeTickets = Ticket::where('entry_time', '<=', $hourEnd)
                ->where(function($q) use ($hourStart) {
                    $q->whereNull('exit_time')
                      ->orWhere('exit_time', '>=', $hourStart);
                })
                ->count();
            
            $hourlyData[] = [
                'hour' => $hour . ':00',
                'active' => $activeTickets,
                'capacity' => ParkingSlot::count(),
            ];
        }
        
        // Top 10 frequent parkers
        $topVehicles = DB::table('tickets')
            ->select('plate_number', DB::raw('COUNT(*) as visit_count'), DB::raw('SUM(fee) as total_paid'))
            ->groupBy('plate_number')
            ->orderBy('visit_count', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.reports.occupancy', compact('hourlyData', 'topVehicles', 'date'));
    }
    
    public function export($type, $format, Request $request)
    {
        try {
            if ($type === 'revenue' && $format === 'pdf') {
                $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
                $endDate = $request->get('end_date', now()->format('Y-m-d'));
                
                // Limit to last 100 transactions for PDF to avoid memory issues
                $transactions = Transaction::with('ticket', 'processedBy')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->orderBy('created_at', 'desc')
                    ->limit(100)
                    ->get();
                
                $totalAmount = Transaction::whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->sum('amount');
                
                $data = [
                    'transactions' => $transactions,
                    'totalAmount' => $totalAmount,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'totalCount' => Transaction::whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate)
                        ->count(),
                ];
                
                $pdf = Pdf::loadView('exports.revenue-pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                
                return $pdf->download("revenue_report_{$startDate}_to_{$endDate}.pdf");
            }
                        
            if ($type === 'occupancy' && $format === 'pdf') {
                $date = $request->get('date', today()->format('Y-m-d'));
                
                $hourlyData = [];
                $totalCapacity = ParkingSlot::count();
                
                for ($hour = 9; $hour <= 20; $hour++) {
                    $hourStart = "$date $hour:00:00";
                    $hourEnd = "$date " . ($hour + 1) . ":00:00";
                    
                    $activeTickets = Ticket::where('entry_time', '<=', $hourEnd)
                        ->where(function($q) use ($hourStart) {
                            $q->whereNull('exit_time')->orWhere('exit_time', '>=', $hourStart);
                        })->count();
                    
                    $hourlyData[] = [
                        'hour' => $hour . ':00',
                        'active' => $activeTickets,
                        'capacity' => $totalCapacity,
                    ];
                }
                
                $topVehicles = DB::table('tickets')
                    ->select('plate_number', DB::raw('COUNT(*) as visit_count'), DB::raw('SUM(fee) as total_paid'))
                    ->groupBy('plate_number')
                    ->orderBy('visit_count', 'desc')
                    ->limit(10)
                    ->get();
                
                $data = [
                    'hourlyData' => $hourlyData,
                    'topVehicles' => $topVehicles,
                    'date' => $date,
                ];
                
                $pdf = Pdf::loadView('exports.occupancy-pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                
                return $pdf->download("occupancy_report_{$date}.pdf");
            }
            
            // Handle CSV exports
            if ($type === 'revenue' && $format === 'csv') {
                $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
                $endDate = $request->get('end_date', now()->format('Y-m-d'));
                
                $transactions = Transaction::with('ticket', 'processedBy')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->get();
                
                $filename = "revenue_report_{$startDate}_to_{$endDate}.csv";
                $headers = ['Transaction ID', 'Ticket QR', 'Plate Number', 'Amount', 'Payment Method', 'Processed By', 'Date'];
                
                $callback = function() use ($transactions, $headers) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $headers);
                    foreach ($transactions as $t) {
                        fputcsv($file, [
                            $t->id,
                            substr($t->ticket->qr_code ?? '', 0, 20),
                            $t->ticket->plate_number ?? 'N/A',
                            $t->amount,
                            $t->payment_method === 'e_wallet' ? 'E-WALLET' : strtoupper($t->payment_method),
                            $t->processedBy->name ?? 'N/A',
                            $t->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                    fclose($file);
                };
                
                return response()->stream($callback, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]);
            }
            
            if ($type === 'occupancy' && $format === 'csv') {
                $date = $request->get('date', today()->format('Y-m-d'));
                
                $hourlyData = [];
                $totalCapacity = ParkingSlot::count();
                
                for ($hour = 9; $hour <= 20; $hour++) {
                    $hourStart = "$date $hour:00:00";
                    $hourEnd = "$date " . ($hour + 1) . ":00:00";
                    
                    $activeTickets = Ticket::where('entry_time', '<=', $hourEnd)
                        ->where(function($q) use ($hourStart) {
                            $q->whereNull('exit_time')->orWhere('exit_time', '>=', $hourStart);
                        })->count();
                    
                    $hourlyData[] = [
                        'hour' => $hour . ':00',
                        'active' => $activeTickets,
                        'capacity' => $totalCapacity,
                    ];
                }
                
                $filename = "occupancy_report_{$date}.csv";
                $headers = ['Hour', 'Active Tickets', 'Total Capacity', 'Occupancy Rate %'];
                
                $callback = function() use ($hourlyData, $headers) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $headers);
                    foreach ($hourlyData as $data) {
                        $rate = $data['capacity'] > 0 ? round(($data['active'] / $data['capacity']) * 100, 1) : 0;
                        fputcsv($file, [
                            $data['hour'],
                            $data['active'],
                            $data['capacity'],
                            $rate . '%',
                        ]);
                    }
                    fclose($file);
                };
                
                return response()->stream($callback, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]);
            }
            
            return redirect()->back()->with('error', 'Invalid report type or format');
            
        } catch (\Exception $e) {
            \Log::error('Export Error: ' . $e->getMessage());
            \Log::error('Export Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    public function activityLogs(Request $request)
    {
        $logs = \App\Models\ActivityLog::with('user', 'ticket')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        $actions = [
            'login' => 'bg-green-100 text-green-800',
            'logout' => 'bg-gray-100 text-gray-800',
            'issue_ticket' => 'bg-blue-100 text-blue-800',
            'scan_exit' => 'bg-purple-100 text-purple-800',
            'process_payment' => 'bg-green-100 text-green-800',
            'delivery_override' => 'bg-yellow-100 text-yellow-800',
            'void_ticket' => 'bg-red-100 text-red-800',
            'update_settings' => 'bg-indigo-100 text-indigo-800',
            'create_user' => 'bg-teal-100 text-teal-800',
            'deactivate_user' => 'bg-orange-100 text-orange-800',
            'convert_slot_type' => 'bg-pink-100 text-pink-800',
        ];
        
        return view('admin.activity-logs', compact('logs', 'actions'));
    }  
    
    public function dashboard()
    {
        // KPI Data
        $occupied = \App\Models\Ticket::where('status', 'active')->count();
        $totalSlots = \App\Models\ParkingSlot::count();
        $available = $totalSlots - $occupied;
        $occupancyRate = $totalSlots > 0 ? round(($occupied / $totalSlots) * 100, 1) : 0;
        $todayRevenue = \App\Models\Transaction::whereDate('created_at', today())->sum('amount');
        
        // Zone Summary - Dynamic based on actual zone_names
        $zones = [];
        $zonesData = \App\Models\ParkingSlot::select('zone_name')
            ->whereNotNull('zone_name')
            ->distinct()
            ->get()
            ->pluck('zone_name')
            ->toArray();
        
        foreach ($zonesData as $zoneName) {
            $zoneSlots = \App\Models\ParkingSlot::where('zone_name', $zoneName)->get();
            $zoneTotal = $zoneSlots->count();
            $zoneOccupied = $zoneSlots->where('status', 'occupied')->count();
            $zoneAvailable = $zoneTotal - $zoneOccupied;
            
            $zones[] = [
                'name' => $zoneName,
                'total' => $zoneTotal,
                'occupied' => $zoneOccupied,
                'available' => $zoneAvailable,
                'percentage' => $zoneTotal > 0 ? round(($zoneOccupied / $zoneTotal) * 100, 1) : 0
            ];
        }
        
        // Sort zones by name
        usort($zones, function($a, $b) {
            return strnatcmp($a['name'], $b['name']);
        });
        
        // Chart Data - Last 7 Days
        $chartLabels = [];
        $occupancyData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('D');
            $dailyTickets = \App\Models\Ticket::whereDate('created_at', $date)->count();
            $occupancyData[] = $dailyTickets;
        }
        
        // Revenue Chart Data
        $revenueLabels = [];
        $revenueData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueLabels[] = $date->format('M j');
            $dailyRevenue = \App\Models\Transaction::whereDate('created_at', $date)->sum('amount');
            $revenueData[] = $dailyRevenue;
        }
        
        // Recent Activity
        $recentActivities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($log) {
                $icons = [
                    'issue_ticket' => 'fa-ticket-alt',
                    'process_payment' => 'fa-credit-card',
                    'login' => 'fa-sign-in-alt',
                    'logout' => 'fa-sign-out-alt',
                    'create_user' => 'fa-user-plus',
                    'update_settings' => 'fa-cog',
                ];
                return [
                    'icon' => $icons[$log->action] ?? 'fa-bell',
                    'description' => ($log->user->name ?? 'System') . ' ' . str_replace('_', ' ', $log->action),
                    'time' => $log->created_at->diffForHumans()
                ];
            });
        
        return view('admin.dashboard', compact(
            'occupied', 'available', 'occupancyRate', 'todayRevenue',
            'zones', 'chartLabels', 'occupancyData', 'revenueLabels', 'revenueData', 'recentActivities'
        ));
    }
}