<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parking_slots', function (Blueprint $table) {
            $table->foreignId('zone_id')->nullable()->constrained('zones')->onDelete('set null');
            $table->string('zone_name')->nullable(); // Denormalized for quick access
        });
    }

    public function down(): void
    {
        Schema::table('parking_slots', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropColumn(['zone_id', 'zone_name']);
        });
    }
};