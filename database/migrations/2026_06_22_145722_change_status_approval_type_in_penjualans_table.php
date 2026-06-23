<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE penjualans MODIFY COLUMN status_approval VARCHAR(50) DEFAULT 'pending'");
            DB::statement("ALTER TABLE penjualans MODIFY COLUMN status VARCHAR(50) DEFAULT 'draft'");
        } catch (\Exception $e) {
            // Ignore error for SQLite
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't revert to ENUM because we might have strings that don't match the old ENUM values
    }
};
