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
        Schema::create('pengirimans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->onDelete('cascade');
            $table->string('no_pengiriman')->unique(); // e.g. SJ-SO-xxxx-1
            $table->string('no_invoice')->unique();    // e.g. INV-SO-xxxx-1
            $table->date('tanggal_kirim');
            $table->string('plat_kendaraan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('pengiriman_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->constrained('pengirimans')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('jumlah_kirim');
            $table->integer('harga_satuan');
            $table->integer('subtotal'); // jumlah_kirim * harga_satuan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman_details');
        Schema::dropIfExists('pengirimans');
    }
};
