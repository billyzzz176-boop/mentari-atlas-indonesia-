<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Piutang;

class ReturPenjualanTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_note_reduces_piutang_and_creates_credit_note()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'return_barang']);
        $customer = Customer::create(['id_cust' => 'CUST1', 'nama_customer' => 'Cust A']);
        $barang = Barang::create(['kode_barang' => 'B001', 'nama_barang' => 'Produk A', 'stok_akhir' => 10]);

        $penjualan = Penjualan::create([
            'no_so' => 'SO-001',
            'tanggal_order' => now(),
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'total_semua' => 200
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'harga_satuan' => 100,
            'subtotal' => 200
        ]);

        Piutang::create([
            'no_invoice' => 'INV-001',
            'penjualan_id' => $penjualan->id,
            'total_tagihan' => 200,
            'total_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'jatuh_tempo' => now()
        ]);

        // Act
        $nominal = 50;
        $response = $this->actingAs($user)->post('/warehouse/retur-penjualan', [
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'jenis_retur' => 'harga_credit_note',
            'qty_retur' => 1,
            'nominal_potongan' => $nominal,
            'alasan' => 'Selisih harga'
        ]);

        // Assert
        $response->assertRedirect();

        $this->assertDatabaseHas('returs', [
            'tipe' => 'penjualan',
            'referensi_id' => $penjualan->id,
            'nominal_potongan' => $nominal,
        ]);

        $this->assertDatabaseHas('credit_notes', [
            'tipe' => 'penjualan',
            'referensi_id' => $penjualan->id,
            'nominal' => $nominal,
        ]);

        $this->assertDatabaseHas('piutangs', [
            'penjualan_id' => $penjualan->id,
            'total_tagihan' => 150.00,
        ]);
    }

    public function test_retur_fisik_returns_stock_when_bagus()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'return_barang']);
        $customer = Customer::create(['id_cust' => 'CUST2', 'nama_customer' => 'Cust B']);
        $barang = Barang::create(['kode_barang' => 'B002', 'nama_barang' => 'Produk B', 'stok_akhir' => 5]);

        $penjualan = Penjualan::create([
            'no_so' => 'SO-002',
            'tanggal_order' => now(),
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'total_semua' => 200
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'harga_satuan' => 100,
            'subtotal' => 200
        ]);

        // Act
        $response = $this->actingAs($user)->post('/warehouse/retur-penjualan', [
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'jenis_retur' => 'fisik',
            'qty_retur' => 2,
            'status_kondisi' => 'bagus',
            'alasan' => 'Kirim salah'
        ]);

        // Assert
        $response->assertRedirect();

        $this->assertDatabaseHas('returs', [
            'tipe' => 'penjualan',
            'referensi_id' => $penjualan->id,
            'qty' => 2,
        ]);

        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'stok_akhir' => 7, // initial 5 + 2 returned
        ]);
    }

    public function test_multi_item_retur_penjualan()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'return_barang']);
        $customer = Customer::create(['id_cust' => 'CUST3', 'nama_customer' => 'Cust C']);
        $barang1 = Barang::create(['kode_barang' => 'B003', 'nama_barang' => 'Produk C1', 'stok_akhir' => 10]);
        $barang2 = Barang::create(['kode_barang' => 'B004', 'nama_barang' => 'Produk C2', 'stok_akhir' => 5]);

        $penjualan = Penjualan::create([
            'no_so' => 'SO-003',
            'tanggal_order' => now(),
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'total_semua' => 300
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang1->id,
            'jumlah' => 2,
            'harga_satuan' => 100,
            'subtotal' => 200
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang2->id,
            'jumlah' => 1,
            'harga_satuan' => 100,
            'subtotal' => 100
        ]);

        $piutang = Piutang::create([
            'no_invoice' => 'INV-003',
            'penjualan_id' => $penjualan->id,
            'total_tagihan' => 300,
            'total_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'jatuh_tempo' => now()
        ]);

        // Act
        $response = $this->actingAs($user)->post('/warehouse/retur-penjualan', [
            'penjualan_id' => $penjualan->id,
            'items' => [
                0 => [
                    'selected' => '1',
                    'barang_id' => $barang1->id,
                    'qty_retur' => 2,
                    'jenis_retur' => 'fisik',
                    'status_kondisi' => 'bagus',
                    'alasan' => 'Salah kirim item 1',
                ],
                1 => [
                    'selected' => '1',
                    'barang_id' => $barang2->id,
                    'qty_retur' => 1,
                    'jenis_retur' => 'harga_credit_note',
                    'nominal_potongan' => 50,
                    'alasan' => 'Diskon item 2',
                ]
            ]
        ]);

        // Assert
        $response->assertRedirect();

        // Check returs table
        $this->assertDatabaseHas('returs', [
            'tipe' => 'penjualan',
            'referensi_id' => $penjualan->id,
            'barang_id' => $barang1->id,
            'qty' => 2,
            'jenis_retur' => 'fisik',
            'kondisi' => 'bagus',
        ]);

        $this->assertDatabaseHas('returs', [
            'tipe' => 'penjualan',
            'referensi_id' => $penjualan->id,
            'barang_id' => $barang2->id,
            'qty' => 1,
            'jenis_retur' => 'harga_credit_note',
            'nominal_potongan' => 50,
        ]);

        // Check stocks
        $this->assertDatabaseHas('barangs', [
            'id' => $barang1->id,
            'stok_akhir' => 12, // initial 10 + 2 returned
        ]);

        $this->assertDatabaseHas('barangs', [
            'id' => $barang2->id,
            'stok_akhir' => 5, // no change for price CN return
        ]);

        // Check combined Piutang reduction (tagihan 300 - 200 - 50 = 50)
        $this->assertDatabaseHas('piutangs', [
            'id' => $piutang->id,
            'total_tagihan' => 50,
            'status_bayar' => 'belum_bayar',
        ]);
    }
}
