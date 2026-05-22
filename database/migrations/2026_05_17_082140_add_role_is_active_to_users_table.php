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
        Schema::table('users', function (Blueprint $table) {
            // Add role enum (admin or security) - default security
            $table->enum('role', ['admin', 'security'])->default('security')->after('password');
            
            // Add is_active flag for disabling guard accounts
            $table->boolean('is_active')->default(true)->after('role');
            
            // Add soft delete column (archive support)
            $table->softDeletes()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'deleted_at']);
        });
    }
};