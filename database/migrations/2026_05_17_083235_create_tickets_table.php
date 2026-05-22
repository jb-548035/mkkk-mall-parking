<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('qr_code', 100)->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->string('plate_number', 20); // Direct field for redundancy/fast search
            $table->datetime('entry_time');
            $table->datetime('exit_time')->nullable();
            $table->decimal('fee', 10, 2)->default(0.00);
            $table->decimal('rate_at_entry', 10, 2); // Snapshot of hourly rate at entry
            $table->integer('grace_period_at_entry'); // Snapshot of grace period at entry
            $table->boolean('is_delivery')->default(false);
            $table->enum('status', ['active', 'exited', 'voided'])->default('active');
            $table->string('void_reason', 255)->nullable();
            
            // Foreign keys
            $table->foreignId('parking_slot_id')->constrained('parking_slots')->onDelete('restrict');
            $table->foreignId('issued_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('exited_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};