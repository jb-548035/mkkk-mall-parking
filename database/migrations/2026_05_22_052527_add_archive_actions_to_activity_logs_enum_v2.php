<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE activity_logs MODIFY action ENUM(
            'login', 'logout', 'issue_ticket', 'scan_exit', 'process_payment',
            'delivery_override', 'void_ticket', 'update_settings', 'create_user',
            'deactivate_user', 'convert_slot_type', 'password_changed', 
            'reset_password', 'update_user', 'create_zone', 'update_zone', 
            'delete_zone', 'regenerate_slots', 'archive_slot', 'restore_slot', 
            'force_delete_slot', 'archive_zone', 'restore_zone', 'force_delete_zone',
            'archive_user', 'restore_user', 'force_delete_user'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE activity_logs MODIFY action ENUM(
            'login', 'logout', 'issue_ticket', 'scan_exit', 'process_payment',
            'delivery_override', 'void_ticket', 'update_settings', 'create_user',
            'deactivate_user', 'convert_slot_type', 'password_changed', 
            'reset_password', 'update_user', 'create_zone', 'update_zone', 
            'delete_zone', 'regenerate_slots', 'archive_slot', 'restore_slot', 
            'force_delete_slot'
        ) NOT NULL");
    }
};
