<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', [
                'login', 'logout', 'issue_ticket', 'scan_exit', 'process_payment',
                'delivery_override', 'void_ticket', 'update_settings', 'create_user',
                'deactivate_user', 'convert_slot_type'
            ]);
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onDelete('set null');
            $table->text('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps(); // created_at only matters for logs
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};