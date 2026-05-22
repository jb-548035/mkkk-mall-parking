<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Temporarily convert column to VARCHAR to bypass ENUM restrictions
        DB::statement("ALTER TABLE transactions MODIFY payment_method VARCHAR(50)");
        
        // Step 2: Update all 'nfc' values to 'e_wallet'
        DB::statement("UPDATE transactions SET payment_method = 'e_wallet' WHERE payment_method = 'nfc'");
        
        // Step 3: Convert back to ENUM with the new values
        DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('cash', 'card', 'e_wallet', 'delivery_override') NOT NULL");
    }

    public function down(): void
    {
        // Step 1: Convert to VARCHAR
        DB::statement("ALTER TABLE transactions MODIFY payment_method VARCHAR(50)");
        
        // Step 2: Revert 'e_wallet' values back to 'nfc'
        DB::statement("UPDATE transactions SET payment_method = 'nfc' WHERE payment_method = 'e_wallet'");
        
        // Step 3: Convert back to original ENUM
        DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('cash', 'card', 'nfc', 'delivery_override') NOT NULL");
    }
};