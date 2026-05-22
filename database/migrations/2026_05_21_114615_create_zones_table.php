<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10)->unique(); // A, B, C, D, etc.
            $table->string('description')->nullable();
            $table->integer('total_slots')->default(0);
            $table->integer('pwd_slots')->default(5); // Each zone has 5 PWD slots
            $table->integer('delivery_slots')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};