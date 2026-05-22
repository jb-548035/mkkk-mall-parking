<?php

namespace Database\Seeders;

use App\Models\ParkingSlot;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Original minimal bootstrap (admin + one guard + flat slots, no history).
 */
class LegacyMinimalSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mkkkmall.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        User::create([
            'name' => 'Security Guard',
            'email' => 'guard@mkkkmall.com',
            'password' => Hash::make('password123'),
            'role' => 'security',
            'is_active' => true,
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        for ($i = 1; $i <= 50; $i++) {
            ParkingSlot::create([
                'slot_number' => 'A' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'type' => 'standard',
                'status' => 'available',
                'is_active' => true,
            ]);
        }

        foreach (range(1, 5) as $i) {
            ParkingSlot::create([
                'slot_number' => 'W' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'type' => 'wheelchair',
                'status' => 'available',
                'is_active' => true,
            ]);
        }

        foreach (range(1, 3) as $i) {
            ParkingSlot::create([
                'slot_number' => 'D' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'type' => 'delivery',
                'status' => 'available',
                'is_active' => true,
            ]);
        }

        $settings = [
            ['key' => 'mall_name', 'value' => 'MKKK Mall'],
            ['key' => 'hourly_rate', 'value' => '20.00'],
            ['key' => 'grace_period_minutes', 'value' => '30'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
