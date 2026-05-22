<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->string('recipient_email', 100);
            $table->enum('report_type', ['revenue', 'occupancy', 'summary', 'wheelchair_utilization']);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_schedules');
    }
};