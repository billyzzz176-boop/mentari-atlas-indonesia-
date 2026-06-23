<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->dropUnique('pembelians_no_pembelian_unique');
            });
        } catch (\Exception $e) {
            // Already dropped or not exists
        }

        try {
            Schema::table('returs', function (Blueprint $table) {
                $table->dropUnique('returs_no_retur_unique');
            });
        } catch (\Exception $e) {
            // Already dropped or not exists
        }

        if (!Schema::hasColumn('pembelians', 'foto_invoice')) {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->string('foto_invoice')->nullable()->after('status_barang');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pembelians', 'foto_invoice')) {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->dropColumn('foto_invoice');
            });
        }

        Schema::table('returs', function (Blueprint $table) {
            $table->unique('no_retur');
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->unique('no_pembelian');
        });
    }
};
