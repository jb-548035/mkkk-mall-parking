<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'e_wallet', 'delivery_override']);
            $table->string('gateway_reference', 100)->nullable();
            $table->enum('status', ['completed', 'failed'])->default('completed');
            $table->foreignId('processed_by')->constrained('users')->onDelete('restrict');
            $table->timestamps(); // created_at only (no updated_at needed for transactions)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};