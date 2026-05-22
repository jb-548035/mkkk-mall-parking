<?php

namespace App\Providers;

use App\Models\ParkingSlot;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       if (env('APP_ENV') === 'production') {  
           URL::forceScheme('https');  
       }          
        Paginator::useTailwind();

        View::composer('layouts.guard-sidebar', function ($view) {
            $available = ParkingSlot::where('status', 'available')->where('is_active', true)->count();
            $notifications = [];

            if ($available <= 5) {
                $notifications[] = [
                    'message' => "Low capacity: only {$available} slot(s) remaining",
                    'time' => 'Just now',
                ];
            }

            $longStay = Ticket::where('status', 'active')
                ->where('entry_time', '<', now()->subHours(8))
                ->count();

            if ($longStay > 0) {
                $notifications[] = [
                    'message' => "{$longStay} vehicle(s) parked over 8 hours",
                    'time' => 'Review recommended',
                ];
            }

            $view->with([
                'guardNotifCount' => count($notifications),
                'guardNotifications' => $notifications,
            ]);
        });
    }
}
