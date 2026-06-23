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
        Schema::table('barangs', function (Blueprint $table) {
            $table->index('nama_barang', 'idx_barangs_nama');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('nama_customer', 'idx_customers_nama');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->index('nama_supplier', 'idx_suppliers_nama');
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->index('no_pembelian', 'idx_pembelians_no');
            $table->index('nama_supplier', 'idx_pembelians_supplier');
            $table->index('tanggal_beli', 'idx_pembelians_tanggal');
            $table->index('created_at', 'idx_pembelians_created');
        });

        Schema::table('penjualans', function (Blueprint $table) {
            $table->index('tanggal_order', 'idx_penjualans_tanggal');
            $table->index('created_at', 'idx_penjualans_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropIndex('idx_barangs_nama');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_nama');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex('idx_suppliers_nama');
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropIndex('idx_pembelians_no');
            $table->dropIndex('idx_pembelians_supplier');
            $table->dropIndex('idx_pembelians_tanggal');
            $table->dropIndex('idx_pembelians_created');
        });

        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropIndex('idx_penjualans_tanggal');
            $table->dropIndex('idx_penjualans_created');
        });
    }
};
