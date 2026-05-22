<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Full database refresh with realistic mall parking data (Jan 1 → present).
 *
 * Run: php artisan db:seed --class=MallParkingProductionSeeder
 */
class MallParkingProductionSeeder extends Seeder
{
    private const HOURLY_RATE = 20.00;

    private const GRACE_MINUTES = 30;

    private const PASSWORD = 'password123';

    private Carbon $rangeStart;

    private Carbon $rangeEnd;

    /** @var array<int, object> */
    private array $guards = [];

    private int $adminId;

    /** @var array<string, array<int>> */
    private array $slotsByType = [
        'standard' => [],
        'wheelchair' => [],
        'delivery' => [],
    ];

    /** @var array<int, true> */
    private array $occupiedSlotIds = [];

    /** @var array<string, int> plate => vehicle_id */
    private array $vehicleMap = [];

    private int $ticketSeq = 0;

    private int $vehicleSeq = 0;

    public function run(): void
    {
        $this->rangeStart = Carbon::create(now()->year, 1, 1)->startOfDay();
        $this->rangeEnd = now();

        $this->command?->warn('Truncating operational tables (structure preserved)…');
        $this->truncateOperationalData();

        $this->command?->info('Seeding users, zones, slots, and settings…');
        $this->seedUsers();
        $this->seedZonesAndSlots();
        $this->seedSettings();
        $this->seedReportSchedules();

        $this->command?->info('Generating chronological parking operations (this may take a minute)…');
        $stats = $this->seedHistoricalOperations();

        $this->command?->info('Seeding audit trails and admin activity…');
        $this->seedAuditTrails($stats);
        $this->syncParkingSlotStatuses();

        $this->command?->newLine();
        $this->command?->info('✅ Mall parking production dataset ready.');
        $this->command?->table(
            ['Metric', 'Count'],
            [
                ['Tickets (total)', $stats['tickets']],
                ['Active (still inside)', $stats['active']],
                ['Exited (paid)', $stats['exited']],
                ['Voided', $stats['voided']],
                ['Transactions', $stats['transactions']],
                ['Vehicles', $stats['vehicles']],
                ['Activity logs', $stats['activity_logs']],
                ['Date range', $this->rangeStart->toDateString() . ' → ' . $this->rangeEnd->toDateTimeString()],
            ]
        );
        $this->command?->info('Login: admin@mkkkmall.com / guard.shift.a@mkkkmall.com — password: ' . self::PASSWORD);
    }

    private function truncateOperationalData(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'activity_logs',
            'transactions',
            'tickets',
            'slot_type_conversion_logs',
            'report_schedules',
            'parking_slots',
            'vehicles',
            'settings',
            'zones',
            'sessions',
            'password_reset_tokens',
            'users',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    private function seedUsers(): void
    {
        $now = now();
        $password = Hash::make(self::PASSWORD);

        $users = [
            [
                'name' => 'MKKK System Administrator',
                'email' => 'admin@mkkkmall.com',
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Ramon Dela Cruz — Shift A',
                'email' => 'guard.shift.a@mkkkmall.com',
                'role' => 'security',
                'is_active' => true,
            ],
            [
                'name' => 'Maria Santos — Shift B',
                'email' => 'guard.shift.b@mkkkmall.com',
                'role' => 'security',
                'is_active' => true,
            ],
            [
                'name' => 'Jose Reyes — Shift C',
                'email' => 'guard.shift.c@mkkkmall.com',
                'role' => 'security',
                'is_active' => true,
            ],
            [
                'name' => 'Inactive Guard (Archived)',
                'email' => 'guard.inactive@mkkkmall.com',
                'role' => 'security',
                'is_active' => false,
            ],
        ];

        foreach ($users as $row) {
            DB::table('users')->insert([
                'name' => $row['name'],
                'email' => $row['email'],
                'email_verified_at' => $now,
                'password' => $password,
                'role' => $row['role'],
                'is_active' => $row['is_active'],
                'must_change_password' => false,
                'password_changed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->adminId = (int) DB::table('users')->where('email', 'admin@mkkkmall.com')->value('id');
        $this->guards = DB::table('users')
            ->where('role', 'security')
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->all();
    }

    private function seedZonesAndSlots(): void
    {
        $zones = [
            [
                'name' => 'B1',
                'description' => 'Basement B1 — Covered car parking',
                'total_slots' => 67,
                'pwd_slots' => 5,
                'delivery_slots' => 2,
                'sort_order' => 1,
                'prefix' => 'B1',
                'standard' => 60,
                'wheelchair' => 5,
                'delivery' => 2,
            ],
            [
                'name' => 'GF',
                'description' => 'Ground Floor — Main mall entrance',
                'total_slots' => 47,
                'pwd_slots' => 5,
                'delivery_slots' => 2,
                'sort_order' => 2,
                'prefix' => 'GF',
                'standard' => 40,
                'wheelchair' => 5,
                'delivery' => 2,
            ],
            [
                'name' => 'OP',
                'description' => 'Open Parking — Rooftop / outdoor',
                'total_slots' => 38,
                'pwd_slots' => 0,
                'delivery_slots' => 4,
                'sort_order' => 3,
                'prefix' => 'OP',
                'standard' => 30,
                'wheelchair' => 0,
                'delivery' => 4,
                'motorcycle_standard' => 4,
            ],
        ];

        $now = now();

        foreach ($zones as $z) {
            $zoneId = DB::table('zones')->insertGetId([
                'name' => $z['name'],
                'description' => $z['description'],
                'total_slots' => $z['total_slots'],
                'pwd_slots' => $z['pwd_slots'],
                'delivery_slots' => $z['delivery_slots'],
                'is_active' => true,
                'sort_order' => $z['sort_order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $prefix = $z['prefix'];

            $this->createSlotsForZone($zoneId, $z['name'], $prefix, 'standard', $z['standard'], 'S');
            $this->createSlotsForZone($zoneId, $z['name'], $prefix, 'wheelchair', $z['wheelchair'], 'W');
            $this->createSlotsForZone($zoneId, $z['name'], $prefix, 'delivery', $z['delivery'], 'D');

            if (! empty($z['motorcycle_standard'])) {
                $this->createSlotsForZone($zoneId, $z['name'], $prefix, 'standard', $z['motorcycle_standard'], 'MC');
            }
        }
    }

    private function createSlotsForZone(
        int $zoneId,
        string $zoneName,
        string $prefix,
        string $type,
        int $count,
        string $typeCode
    ): void {
        if ($count <= 0) {
            return;
        }

        $now = now();

        for ($i = 1; $i <= $count; $i++) {
            $slotNumber = $prefix . '-' . $typeCode . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $id = DB::table('parking_slots')->insertGetId([
                'slot_number' => $slotNumber,
                'type' => $type,
                'status' => 'available',
                'is_active' => true,
                'zone_id' => $zoneId,
                'zone_name' => $zoneName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->slotsByType[$type][] = (int) $id;
        }
    }

    private function seedSettings(): void
    {
        $totalStandard = count($this->slotsByType['standard']);
        $totalWheelchair = count($this->slotsByType['wheelchair']);
        $totalDelivery = count($this->slotsByType['delivery']);

        $settings = [
            ['key' => 'mall_name', 'value' => 'MKKK Mall'],
            ['key' => 'mall_address', 'value' => 'MacArthur Highway, Corner Don Julian Rodriguez Sr. Ave, Davao City'],
            ['key' => 'mall_hours_open', 'value' => '08:00'],
            ['key' => 'mall_hours_close', 'value' => '22:00'],
            ['key' => 'mall_website', 'value' => 'mkkkmall.ph'],
            ['key' => 'hourly_rate', 'value' => (string) self::HOURLY_RATE],
            ['key' => 'grace_period_minutes', 'value' => (string) self::GRACE_MINUTES],
            ['key' => 'total_standard_slots', 'value' => (string) $totalStandard],
            ['key' => 'total_wheelchair_slots', 'value' => (string) $totalWheelchair],
            ['key' => 'total_delivery_slots', 'value' => (string) $totalDelivery],
            ['key' => 'accepted_payments', 'value' => 'Cash, Credit Card, Debit Card, E-Wallet Mobile Payments'],
            ['key' => 'has_wheelchair_entrance', 'value' => 'true'],
            ['key' => 'has_wheelchair_restroom', 'value' => 'true'],
            ['key' => 'has_changing_table', 'value' => 'true'],
            ['key' => 'contact_phone', 'value' => '(082) 305-1200'],
        ];

        $now = now();
        $rows = [];

        foreach ($settings as $s) {
            $rows[] = [
                'key' => $s['key'],
                'value' => $s['value'],
                'description' => null,
                'updated_by' => $this->adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('settings')->insert($rows);
    }

    private function seedReportSchedules(): void
    {
        $now = now();

        DB::table('report_schedules')->insert([
            [
                'name' => 'Daily Revenue Summary',
                'frequency' => 'daily',
                'recipient_email' => 'finance@mkkkmall.com',
                'report_type' => 'revenue',
                'is_active' => true,
                'last_sent_at' => $now->copy()->subDay(),
                'created_by' => $this->adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Weekly Occupancy Report',
                'frequency' => 'weekly',
                'recipient_email' => 'operations@mkkkmall.com',
                'report_type' => 'occupancy',
                'is_active' => true,
                'last_sent_at' => $now->copy()->subDays(3),
                'created_by' => $this->adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function seedHistoricalOperations(): array
    {
        $tickets = [];
        $transactions = [];

        $targetActive = random_int(28, 42);
        $activeCreated = 0;

        $day = $this->rangeStart->copy();

        while ($day->lte($this->rangeEnd)) {
            $isWeekend = $day->isWeekend();
            $isToday = $day->isSameDay($this->rangeEnd);
            $monthFactor = 0.85 + ($day->month / 12) * 0.25;

            $base = $isWeekend ? random_int(95, 145) : random_int(48, 88);
            $dailyEntries = (int) round($base * $monthFactor);

            if ($isToday) {
                $dailyEntries = (int) round($dailyEntries * ($this->rangeEnd->hour / 22));
                $dailyEntries = max($dailyEntries, 12);
            }

            for ($e = 0; $e < $dailyEntries; $e++) {
                $entryTime = $this->randomEntryTime($day);
                if ($entryTime->gt($this->rangeEnd)) {
                    continue;
                }

                $isDelivery = random_int(1, 100) <= 8;
                $useWheelchair = ! $isDelivery && random_int(1, 100) <= 5;
                $slotType = $isDelivery ? 'delivery' : ($useWheelchair ? 'wheelchair' : 'standard');
                $slotId = $this->reserveSlot($slotType);

                if ($slotId === null) {
                    continue;
                }

                $durationMinutes = $this->randomParkingDuration($isDelivery);
                $exitTime = $entryTime->copy()->addMinutes($durationMinutes);

                $stayActive = false;
                if ($isToday && $activeCreated < $targetActive && random_int(1, 100) <= 35) {
                    $exitTime = $this->rangeEnd->copy()->addHours(random_int(2, 48));
                    $stayActive = true;
                    $activeCreated++;
                } elseif ($exitTime->gt($this->rangeEnd)) {
                    if ($activeCreated < $targetActive && $day->diffInDays($this->rangeEnd) <= 3) {
                        $stayActive = true;
                        $activeCreated++;
                    } else {
                        $exitTime = $this->rangeEnd->copy()->subMinutes(random_int(5, 120));
                    }
                }

                $plate = $this->randomPlate($slotType);
                $vehicleId = $this->resolveVehicle($plate, $entryTime);
                $guard = $this->guards[array_rand($this->guards)];
                $ticketId = ++$this->ticketSeq;

                $status = 'active';
                $fee = 0.0;
                $exitedBy = null;
                $voidReason = null;
                $deletedAt = null;

                if (! $stayActive) {
                    $status = 'exited';
                    $fee = $this->calculateFee($entryTime, $exitTime, $isDelivery);
                    $exitedBy = $guard->id;
                    $this->releaseSlot($slotId);
                }

                $tickets[] = [
                    'id' => $ticketId,
                    'qr_code' => $this->uniqueQr($ticketId),
                    'vehicle_id' => $vehicleId,
                    'plate_number' => $plate,
                    'entry_time' => $entryTime,
                    'exit_time' => $stayActive ? null : $exitTime,
                    'fee' => $fee,
                    'rate_at_entry' => self::HOURLY_RATE,
                    'grace_period_at_entry' => self::GRACE_MINUTES,
                    'is_delivery' => $isDelivery,
                    'status' => $status,
                    'void_reason' => $voidReason,
                    'parking_slot_id' => $slotId,
                    'issued_by' => $guard->id,
                    'exited_by' => $exitedBy,
                    'created_at' => $entryTime,
                    'updated_at' => $stayActive ? $entryTime : $exitTime,
                    'deleted_at' => $deletedAt,
                ];

                if ($status === 'exited') {
                    $paymentMethod = $this->paymentMethod($isDelivery, $fee);
                    $payAt = $exitTime->copy()->addMinutes(random_int(1, 8));

                    $transactions[] = [
                        'ticket_id' => $ticketId,
                        'amount' => $fee,
                        'payment_method' => $paymentMethod,
                        'gateway_reference' => $paymentMethod === 'card' ? 'REF_' . strtoupper(Str::random(10)) : null,
                        'status' => 'completed',
                        'processed_by' => $guard->id,
                        'created_at' => $payAt,
                        'updated_at' => $payAt,
                    ];
                }

                if (count($tickets) >= 400) {
                    $this->flushTickets($tickets);
                    $tickets = [];
                }
                if (count($transactions) >= 400) {
                    $this->flushTransactions($transactions);
                    $transactions = [];
                }
            }

            $day->addDay();
        }

        $this->flushTickets($tickets);
        $this->flushTransactions($transactions);

        $this->resetAutoIncrements();
        $this->ensureActiveFleet(random_int(32, 45));
        $this->seedOperationalActivityLogs();
        $this->applyVoidedTickets();
        $this->updateVehicleVisitCounts();

        $active = (int) DB::table('tickets')->where('status', 'active')->whereNull('deleted_at')->count();
        $exited = (int) DB::table('tickets')->where('status', 'exited')->whereNull('deleted_at')->count();
        $voided = (int) DB::table('tickets')->where('status', 'voided')->count();
        $txCount = (int) DB::table('transactions')->count();
        $vehicleCount = (int) DB::table('vehicles')->count();
        $logCount = (int) DB::table('activity_logs')->count();

        return [
            'tickets' => $active + $exited + $voided,
            'active' => $active,
            'exited' => $exited,
            'voided' => $voided,
            'transactions' => $txCount,
            'vehicles' => $vehicleCount,
            'activity_logs' => $logCount,
        ];
    }

    private function seedOperationalActivityLogs(): void
    {
        $logs = [];
        $exited = DB::table('tickets')
            ->join('transactions', 'transactions.ticket_id', '=', 'tickets.id')
            ->select(
                'tickets.id',
                'tickets.plate_number',
                'tickets.parking_slot_id',
                'tickets.entry_time',
                'tickets.issued_by',
                'transactions.amount',
                'transactions.payment_method',
                'transactions.processed_by',
                'transactions.created_at as paid_at'
            )
            ->where('tickets.status', 'exited')
            ->orderByRaw('RAND()')
            ->limit(2500)
            ->get();

        foreach ($exited as $row) {
            $entryAt = Carbon::parse($row->entry_time);
            $paidAt = Carbon::parse($row->paid_at);

            $logs[] = $this->logRow((int) $row->issued_by, 'issue_ticket', (int) $row->id, [
                'plate_number' => $row->plate_number,
                'slot' => $row->parking_slot_id,
            ], $entryAt);

            $logs[] = $this->logRow((int) $row->processed_by, 'process_payment', (int) $row->id, [
                'amount' => (float) $row->amount,
                'method' => $row->payment_method,
            ], $paidAt);

            if (count($logs) >= 500) {
                $this->flushActivityLogs($logs);
                $logs = [];
            }
        }

        $active = DB::table('tickets')
            ->where('status', 'active')
            ->select('id', 'plate_number', 'parking_slot_id', 'entry_time', 'issued_by')
            ->get();

        foreach ($active as $row) {
            $logs[] = $this->logRow((int) $row->issued_by, 'issue_ticket', (int) $row->id, [
                'plate_number' => $row->plate_number,
                'slot' => $row->parking_slot_id,
            ], Carbon::parse($row->entry_time));
        }

        $this->flushActivityLogs($logs);
    }

    private function seedAuditTrails(array $stats): void
    {
        $logs = [];
        $admin = $this->adminId;

        $zoneCreatedAt = $this->rangeStart->copy()->addDays(2);
        $logs[] = $this->logRow($admin, 'create_zone', null, ['zone_name' => 'B1'], $zoneCreatedAt);
        $logs[] = $this->logRow($admin, 'create_zone', null, ['zone_name' => 'GF'], $zoneCreatedAt->copy()->addHour());
        $logs[] = $this->logRow($admin, 'create_zone', null, ['zone_name' => 'OP'], $zoneCreatedAt->copy()->addHours(2));
        $logs[] = $this->logRow($admin, 'update_settings', null, ['keys' => ['hourly_rate', 'grace_period_minutes']], $this->rangeStart->copy()->addDays(10));
        $logs[] = $this->logRow($admin, 'regenerate_slots', null, ['zone' => 'B1'], $this->rangeStart->copy()->addDays(45));

        foreach ($this->guards as $guard) {
            for ($d = 0; $d < $this->rangeStart->diffInDays($this->rangeEnd); $d += random_int(3, 5)) {
                $at = $this->rangeStart->copy()->addDays($d)->setTime(random_int(6, 8), random_int(0, 59));
                if ($at->lte($this->rangeEnd)) {
                    $logs[] = $this->logRow($guard->id, 'login', null, ['shift' => $guard->name], $at);
                    $logs[] = $this->logRow($guard->id, 'logout', null, [], $at->copy()->addHours(random_int(7, 9)));
                }
            }
        }

        $sampleTickets = DB::table('tickets')
            ->where('status', 'exited')
            ->inRandomOrder()
            ->limit(25)
            ->pluck('id');

        foreach ($sampleTickets as $tid) {
            $logs[] = $this->logRow($this->guards[0]->id, 'scan_exit', (int) $tid, ['source' => 'qr_scan'], $this->rangeEnd->copy()->subDays(random_int(1, 30)));
        }

        $deliveryTickets = DB::table('tickets')->where('is_delivery', true)->where('status', 'exited')->limit(15)->pluck('id');
        foreach ($deliveryTickets as $tid) {
            $logs[] = $this->logRow($this->guards[1]->id, 'delivery_override', (int) $tid, ['reason' => 'Grace period — no charge'], $this->rangeEnd->copy()->subDays(random_int(2, 60)));
        }

        $conversionSlots = array_slice($this->slotsByType['delivery'], 0, min(2, count($this->slotsByType['delivery'])));
        if ($conversionSlots !== []) {
            DB::table('slot_type_conversion_logs')->insert([
                'admin_id' => $admin,
                'conversion_type' => 'delivery_to_standard',
                'affected_slot_ids' => json_encode($conversionSlots),
                'reason' => 'Weekend mall event — extra car capacity',
                'reverted_at' => $this->rangeStart->copy()->addDays(90),
                'reverted_by' => $admin,
                'created_at' => $this->rangeStart->copy()->addDays(88),
                'updated_at' => $this->rangeStart->copy()->addDays(90),
            ]);
            $logs[] = $this->logRow($admin, 'convert_slot_type', null, [
                'type' => 'delivery_to_standard',
                'slots' => $conversionSlots,
            ], $this->rangeStart->copy()->addDays(88));
        }

        $this->flushActivityLogs($logs);
    }

    private function ensureActiveFleet(int $target): void
    {
        $current = (int) DB::table('tickets')->where('status', 'active')->whereNull('deleted_at')->count();

        if ($current >= $target) {
            return;
        }

        $needed = $target - $current;
        $guard = $this->guards[array_rand($this->guards)];

        for ($i = 0; $i < $needed; $i++) {
            $entryTime = $this->rangeEnd->copy()->subHours(random_int(1, 72))->subMinutes(random_int(0, 59));
            $isDelivery = random_int(1, 100) <= 10;
            $slotType = $isDelivery ? 'delivery' : (random_int(1, 100) <= 6 ? 'wheelchair' : 'standard');
            $slotId = $this->reserveSlot($slotType);

            if ($slotId === null) {
                break;
            }

            $plate = $this->randomPlate($slotType);
            $vehicleId = $this->resolveVehicle($plate, $entryTime);
            $ticketId = ++$this->ticketSeq;

            DB::table('tickets')->insert([
                'id' => $ticketId,
                'qr_code' => $this->uniqueQr($ticketId),
                'vehicle_id' => $vehicleId,
                'plate_number' => $plate,
                'entry_time' => $entryTime,
                'exit_time' => null,
                'fee' => 0,
                'rate_at_entry' => self::HOURLY_RATE,
                'grace_period_at_entry' => self::GRACE_MINUTES,
                'is_delivery' => $isDelivery,
                'status' => 'active',
                'void_reason' => null,
                'parking_slot_id' => $slotId,
                'issued_by' => $guard->id,
                'exited_by' => null,
                'created_at' => $entryTime,
                'updated_at' => $entryTime,
                'deleted_at' => null,
            ]);
        }

        $this->resetAutoIncrements();
    }

    private function resetAutoIncrements(): void
    {
        $nextTicket = $this->ticketSeq + 1;
        $nextVehicle = $this->vehicleSeq + 1;
        DB::statement("ALTER TABLE tickets AUTO_INCREMENT = {$nextTicket}");
        DB::statement("ALTER TABLE vehicles AUTO_INCREMENT = {$nextVehicle}");
    }

    private function applyVoidedTickets(): void
    {
        $totalTickets = (int) DB::table('tickets')->count();
        $voidCount = max(15, (int) floor($totalTickets * 0.004));

        $ids = DB::table('tickets')
            ->where('status', 'exited')
            ->where('exit_time', '<', $this->rangeEnd->copy()->subDays(14))
            ->inRandomOrder()
            ->limit($voidCount)
            ->pluck('id');

        $reasons = [
            'Duplicate ticket issued at entry',
            'Customer dispute — supervisor approval',
            'System test ticket',
            'Wrong plate encoded',
        ];

        foreach ($ids as $id) {
            $ticket = DB::table('tickets')->where('id', $id)->first();
            if (! $ticket) {
                continue;
            }

            DB::table('transactions')->where('ticket_id', $id)->delete();

            DB::table('tickets')->where('id', $id)->update([
                'status' => 'voided',
                'void_reason' => $reasons[array_rand($reasons)],
                'fee' => 0,
                'deleted_at' => $ticket->exit_time,
                'updated_at' => $ticket->exit_time,
            ]);

            $this->releaseSlot((int) $ticket->parking_slot_id);

            DB::table('activity_logs')->insert(
                $this->logRow(
                    $this->adminId,
                    'void_ticket',
                    (int) $id,
                    ['reason' => $reasons[array_rand($reasons)]],
                    Carbon::parse($ticket->exit_time)->addMinutes(30)
                )
            );
        }
    }

    private function syncParkingSlotStatuses(): void
    {
        DB::table('parking_slots')->update(['status' => 'available', 'updated_at' => now()]);

        $activeSlotIds = DB::table('tickets')
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->pluck('parking_slot_id');

        if ($activeSlotIds->isNotEmpty()) {
            DB::table('parking_slots')
                ->whereIn('id', $activeSlotIds)
                ->update(['status' => 'occupied', 'updated_at' => now()]);
        }

        $this->occupiedSlotIds = array_fill_keys($activeSlotIds->all(), true);
    }

    private function updateVehicleVisitCounts(): void
    {
        DB::statement('
            UPDATE vehicles v
            SET total_visits = (
                SELECT COUNT(*) FROM tickets t WHERE t.vehicle_id = v.id
            ),
            first_seen_at = (
                SELECT MIN(entry_time) FROM tickets t WHERE t.vehicle_id = v.id
            )
        ');
    }

    private function randomEntryTime(Carbon $day): Carbon
    {
        $peakHours = [10, 11, 12, 13, 17, 18, 19];
        $hour = random_int(1, 100) <= 55
            ? $peakHours[array_rand($peakHours)]
            : random_int(8, 21);

        return $day->copy()->setTime($hour, random_int(0, 59), random_int(0, 59));
    }

    private function randomParkingDuration(bool $isDelivery): int
    {
        if ($isDelivery) {
            return random_int(12, 45);
        }

        $roll = random_int(1, 100);

        if ($roll <= 18) {
            return random_int(12, self::GRACE_MINUTES - 1);
        }
        if ($roll <= 68) {
            return random_int(60, 200);
        }
        if ($roll <= 88) {
            return random_int(200, 420);
        }
        if ($roll <= 96) {
            return random_int(420, 720);
        }

        return random_int(540, 1080);
    }

    private function calculateFee(Carbon $entry, Carbon $exit, bool $isDelivery): float
    {
        if ($isDelivery) {
            return 0.0;
        }

        $minutesParked = $entry->diffInMinutes($exit);
        $billable = max(0, $minutesParked - self::GRACE_MINUTES);

        if ($billable === 0) {
            return 0.0;
        }

        $hours = (int) ceil($billable / 60);

        return round($hours * self::HOURLY_RATE, 2);
    }

    private function paymentMethod(bool $isDelivery, float $fee): string
    {
        if ($isDelivery) {
            return 'delivery_override';
        }

        if ($fee <= 0) {
            return 'cash';
        }

        $methods = ['cash', 'cash', 'cash', 'card', 'e_wallet'];

        return $methods[array_rand($methods)];
    }

    private function reserveSlot(string $type): ?int
    {
        $pool = $this->slotsByType[$type] ?? [];
        shuffle($pool);

        foreach ($pool as $slotId) {
            if (! isset($this->occupiedSlotIds[$slotId])) {
                $this->occupiedSlotIds[$slotId] = true;

                return $slotId;
            }
        }

        foreach ($this->slotsByType['standard'] as $slotId) {
            if (! isset($this->occupiedSlotIds[$slotId])) {
                $this->occupiedSlotIds[$slotId] = true;

                return $slotId;
            }
        }

        return null;
    }

    private function releaseSlot(int $slotId): void
    {
        unset($this->occupiedSlotIds[$slotId]);
    }

    private function randomPlate(string $slotType): string
    {
        $letters = 'ABCDEFGHJKLMNPRSTUVWXYZ';
        $len = strlen($letters) - 1;
        $pick = fn () => $letters[random_int(0, $len)];
        $nums = (string) random_int(1000, 9999);

        if ($slotType === 'delivery') {
            return 'DLV ' . $pick() . $pick() . ' ' . $nums;
        }

        if ($slotType === 'wheelchair' || random_int(1, 100) <= 3) {
            return 'PWD ' . $pick() . $pick() . ' ' . $nums;
        }

        if (random_int(1, 100) <= 12) {
            return random_int(100, 999) . ' ' . $pick() . $pick() . $pick();
        }

        return $pick() . $pick() . $pick() . ' ' . $nums;
    }

    private function resolveVehicle(string $plate, Carbon $firstSeen): int
    {
        if (isset($this->vehicleMap[$plate])) {
            return $this->vehicleMap[$plate];
        }

        $id = ++$this->vehicleSeq;

        DB::table('vehicles')->insert([
            'id' => $id,
            'plate_number' => $plate,
            'first_seen_at' => $firstSeen,
            'total_visits' => 1,
            'notes' => null,
            'created_at' => $firstSeen,
            'updated_at' => $firstSeen,
        ]);

        $this->vehicleMap[$plate] = $id;

        return $id;
    }

    private function uniqueQr(int $ticketId): string
    {
        return 'MKKK-' . strtoupper(Str::random(16)) . '-' . str_pad((string) $ticketId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * @param  array<string, mixed>  $details
     * @return array<string, mixed>
     */
    private function logRow(int $userId, string $action, ?int $ticketId, array $details, Carbon $at): array
    {
        return [
            'user_id' => $userId,
            'action' => $action,
            'ticket_id' => $ticketId,
            'details' => json_encode($details),
            'ip_address' => '127.0.0.1',
            'created_at' => $at,
            'updated_at' => $at,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function flushTickets(array $rows): void
    {
        if ($rows === []) {
            return;
        }
        DB::table('tickets')->insert($rows);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function flushTransactions(array $rows): void
    {
        if ($rows === []) {
            return;
        }
        DB::table('transactions')->insert($rows);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function flushActivityLogs(array $rows): void
    {
        if ($rows === []) {
            return;
        }
        foreach (array_chunk($rows, 300) as $chunk) {
            DB::table('activity_logs')->insert($chunk);
        }
    }
}
