<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add 'e_wallet' to ENUM while keeping 'nfc'
        DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('cash', 'card', 'nfc', 'delivery_override', 'e_wallet') NOT NULL");
        
        // Step 2: Update existing 'nfc' records to 'e_wallet'
        DB::statement("UPDATE transactions SET payment_method = 'e_wallet' WHERE payment_method = 'nfc'");
        
        // Step 3: Remove 'nfc' from ENUM
        DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('cash', 'card', 'e_wallet', 'delivery_override') NOT NULL");
    }

    public function down(): void
    {
        // Step 1: Add 'nfc' back to ENUM
        DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('cash', 'card', 'e_wallet', 'delivery_override', 'nfc') NOT NULL");
        
        // Step 2: Update existing 'e_wallet' records back to 'nfc'
        DB::statement("UPDATE transactions SET payment_method = 'nfc' WHERE payment_method = 'e_wallet'");
        
        // Step 3: Remove 'e_wallet' from ENUM
        DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('cash', 'card', 'nfc', 'delivery_override') NOT NULL");
    }
};