<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires dropping and recreating the column for ENUM changes
        // This is the safe way to add a new ENUM value
        DB::statement("ALTER TABLE activity_logs MODIFY action ENUM(
            'login', 'logout', 'issue_ticket', 'scan_exit', 'process_payment',
            'delivery_override', 'void_ticket', 'update_settings', 'create_user',
            'deactivate_user', 'convert_slot_type', 'password_changed', 'reset_password', 'update_user'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE activity_logs MODIFY action ENUM(
            'login', 'logout', 'issue_ticket', 'scan_exit', 'process_payment',
            'delivery_override', 'void_ticket', 'update_settings', 'create_user',
            'deactivate_user', 'convert_slot_type'
        ) NOT NULL");
    }
};