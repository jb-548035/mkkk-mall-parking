<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ==================== PUBLIC ROUTES ====================

// Login page as landing page
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// API endpoint for real-time parking status (no auth required)
Route::get('/api/parking-status', function () {
    $totalSlots = App\Models\ParkingSlot::count();
    $availableSlots = App\Models\ParkingSlot::where('status', 'available')->count();
    
    return response()->json([
        'total_slots' => $totalSlots,
        'available_slots' => $availableSlots,
        'standard_available' => App\Models\ParkingSlot::where('status', 'available')->where('type', 'standard')->count(),
        'wheelchair_available' => App\Models\ParkingSlot::where('status', 'available')->where('type', 'wheelchair')->count(),
        'delivery_available' => App\Models\ParkingSlot::where('status', 'available')->where('type', 'delivery')->count(),
        'hourly_rate' => App\Models\Setting::get('hourly_rate', 20),
        'grace_period' => App\Models\Setting::get('grace_period_minutes', 30),
    ]);
});

// API endpoint for parking slots (for customer parking page)
Route::get('/api/parking-slots', function () {
    $slotsByZone = App\Models\ParkingSlot::where('is_active', true)
        ->select('id', 'slot_number', 'type', 'status', 'zone_name')
        ->get()
        ->groupBy('zone_name');
    
    $result = [];
    foreach ($slotsByZone as $zoneName => $slots) {
        $result[] = [
            'name' => $zoneName ?: 'Other',
            'description' => "Zone {$zoneName} Parking",
            'total_slots' => $slots->count(),
            'pwd_slots' => $slots->where('type', 'wheelchair')->count(),
            'available_slots' => $slots->where('status', 'available')->count(),
            'slots' => $slots->map(function($slot) {
                return [
                    'id' => $slot->id,
                    'slot_number' => $slot->slot_number,
                    'type' => $slot->type,
                    'status' => $slot->status,
                ];
            })->values()
        ];
    }
    
    usort($result, function($a, $b) {
        return strnatcmp($a['name'], $b['name']);
    });
    
    return response()->json($result);
});

// Customer public pages (no login required)
Route::get('/parking', function () {
    return view('customer.parking-dashboard');
})->name('customer.parking');

Route::get('/accessibility', function () {
    return view('customer.accessibility');
})->name('customer.accessibility');

Route::get('/mall-info', function () {
    return view('customer.mall-info');
})->name('customer.mall-info');

// ==================== AUTHENTICATED ROUTES ====================

// After user logs in, check if they need to change password
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard-redirect', function () {
        $user = auth()->user();
        if ($user->must_change_password) {
            return redirect()->route('password.change.form');
        }
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('guard.dashboard');
    })->name('dashboard.redirect');
});

// Authenticated profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\ReportController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/occupancy-trend', [App\Http\Controllers\Admin\ReportController::class, 'getOccupancyTrend'])->name('dashboard.occupancy-trend');
    Route::get('/dashboard/revenue-trend', [App\Http\Controllers\Admin\ReportController::class, 'getRevenueTrend'])->name('dashboard.revenue-trend');
    
    // Slot Archive/Restore routes (must come BEFORE resource)
    Route::get('/slots/archived', [App\Http\Controllers\Admin\ParkingSlotController::class, 'archived'])->name('slots.archived');
    Route::post('/slots/{slot}/archive', [App\Http\Controllers\Admin\ParkingSlotController::class, 'archive'])->name('slots.archive');
    Route::post('/slots/{slot}/restore', [App\Http\Controllers\Admin\ParkingSlotController::class, 'restore'])->name('slots.restore');
    Route::delete('/slots/{slot}/force-delete', [App\Http\Controllers\Admin\ParkingSlotController::class, 'forceDelete'])->name('slots.force-delete');
    
    // Parking Slot Management
    Route::resource('slots', App\Http\Controllers\Admin\ParkingSlotController::class);
    
    // Report routes
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue', [App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/occupancy', [App\Http\Controllers\Admin\ReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('/reports/export/{type}/{format}', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');    
    Route::get('/activity-logs', [App\Http\Controllers\Admin\ReportController::class, 'activityLogs'])->name('activity.logs'); 
    
    // User Archive/Restore routes (must come BEFORE resource)
    Route::get('/users/archived', [App\Http\Controllers\Admin\UserController::class, 'archived'])->name('users.archived');
    Route::post('/users/{user}/archive', [App\Http\Controllers\Admin\UserController::class, 'archive'])->name('users.archive');
    Route::post('/users/{user}/restore', [App\Http\Controllers\Admin\UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{user}/force-delete', [App\Http\Controllers\Admin\UserController::class, 'forceDelete'])->name('users.force-delete');    

    // User Management Routes
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::post('/users/{user}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');

    // Settings routes
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
    // Zone Archive/Restore routes (must come BEFORE resource)
    Route::get('/zones/archived', [App\Http\Controllers\Admin\ZoneController::class, 'archived'])->name('zones.archived');
    Route::post('/zones/{zone}/archive', [App\Http\Controllers\Admin\ZoneController::class, 'archive'])->name('zones.archive');
    Route::post('/zones/{zone}/restore', [App\Http\Controllers\Admin\ZoneController::class, 'restore'])->name('zones.restore');
    Route::delete('/zones/{zone}/force-delete', [App\Http\Controllers\Admin\ZoneController::class, 'forceDelete'])->name('zones.force-delete');
 
    // Zone Management Routes
    Route::resource('zones', App\Http\Controllers\Admin\ZoneController::class);
    Route::post('/zones/{zone}/regenerate', [App\Http\Controllers\Admin\ZoneController::class, 'regenerateSlots'])->name('zones.regenerate');
});

// ==================== GUARD ROUTES ====================
Route::middleware(['auth', 'role:security'])->prefix('guard')->name('guard.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Guard\TicketController::class, 'dashboard'])->name('dashboard');
    Route::get('/entry', [App\Http\Controllers\Guard\TicketController::class, 'entryForm'])->name('entry.form');
    Route::post('/entry', [App\Http\Controllers\Guard\TicketController::class, 'processEntry'])->name('entry.process');
    Route::get('/exit', [App\Http\Controllers\Guard\TicketController::class, 'exitForm'])->name('exit.form');
    Route::post('/exit/search', [App\Http\Controllers\Guard\TicketController::class, 'searchByPlate'])->name('exit.search');
    Route::post('/exit/scan', [App\Http\Controllers\Guard\TicketController::class, 'scanTicket'])->name('exit.scan');
    Route::post('/exit/payment', [App\Http\Controllers\Guard\TicketController::class, 'processPayment'])->name('exit.payment');
    Route::post('/exit/upload-qr', [App\Http\Controllers\Guard\TicketController::class, 'uploadQRCode'])->name('exit.upload-qr');
    Route::get('/exit/payment/{ticket}', [App\Http\Controllers\Guard\TicketController::class, 'paymentForm'])->name('exit.payment.form');
});

// ==================== AUTHENTICATION ROUTES ====================

// First-time password change (only for users who need to change password)
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [App\Http\Controllers\Auth\FirstLoginPasswordController::class, 'showChangeForm'])->name('password.change.form');
    Route::post('/change-password', [App\Http\Controllers\Auth\FirstLoginPasswordController::class, 'update'])->name('password.change.update');
});

// Temporary migration route (remove after deployment)
Route::get('/run-migrate', function() {
    Artisan::call('migrate --force');
    Artisan::call('db:seed --force');
    Artisan::call('config:cache');
    return 'Migrations completed!';
});

// This handles all guest/auth routes automatically (includes login, register, etc.)
require __DIR__.'/auth.php';