<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_number', 10)->unique();
            $table->enum('type', ['standard', 'wheelchair', 'delivery'])->default('standard');
            $table->enum('status', ['available', 'occupied'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_slots');
    }
};