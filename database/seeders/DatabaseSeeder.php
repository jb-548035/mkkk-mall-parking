<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Default: full production-grade refresh (truncate + seed Jan 1 → now).
     *
     * Legacy minimal seed only:
     *   php artisan db:seed --class=Database\\Seeders\\LegacyMinimalSeeder
     */
    public function run(): void
    {
        $this->call(MallParkingProductionSeeder::class);
    }
}
