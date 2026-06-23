<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Utang;

class ReturPembelianTest extends TestCase
{
    use RefreshDatabase;

    public function test_debit_note_reduces_utang_and_creates_credit_note()
    {
        $user = User::factory()->create(['role' => 'return_barang']);
        $barang = Barang::create(['kode_barang' => 'PB1', 'nama_barang' => 'Produk PB', 'stok_akhir' => 20]);

        $pembelian = Pembelian::create([
            'no_pembelian' => 'PO-001',
            'nama_supplier' => 'Supplier A',
            'barang_id' => $barang->id,
            'jumlah_beli' => 5,
            'harga_beli_hpp' => 100,
            'total_bayar' => 500,
            'tanggal_beli' => now(),
        ]);

        Utang::create([
            'no_utang_jurnal' => 'UT-001',
            'pembelian_id' => $pembelian->id,
            'total_utang' => 500.00,
            'total_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'tanggal_jatuh_tempo' => now(),
        ]);

        $nominal = 120;
        $response = $this->actingAs($user)->post('/warehouse/retur-pembelian', [
            'pembelian_id' => $pembelian->id,
            'barang_id' => $barang->id,
            'jenis_retur' => 'harga_debit_note',
            'qty_retur' => 2,
            'nominal_potongan' => $nominal,
            'alasan' => 'Kualitas kurang'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('returs', [
            'tipe' => 'pembelian',
            'referensi_id' => $pembelian->id,
            'nominal_potongan' => $nominal,
        ]);

        $this->assertDatabaseHas('credit_notes', [
            'tipe' => 'pembelian',
            'referensi_id' => $pembelian->id,
            'nominal' => $nominal,
        ]);

        $this->assertDatabaseHas('utangs', [
            'pembelian_id' => $pembelian->id,
            'potongan_dn' => $nominal,
        ]);
    }

    public function test_retur_fisik_reduces_stock_for_pembelian()
    {
        $user = User::factory()->create(['role' => 'return_barang']);
        $barang = Barang::create(['kode_barang' => 'PB2', 'nama_barang' => 'Produk PB2', 'stok_akhir' => 50]);

        $pembelian = Pembelian::create([
            'no_pembelian' => 'PO-002',
            'nama_supplier' => 'Supplier B',
            'barang_id' => $barang->id,
            'jumlah_beli' => 10,
            'harga_beli_hpp' => 200,
            'total_bayar' => 2000,
            'tanggal_beli' => now(),
        ]);

        $response = $this->actingAs($user)->post('/warehouse/retur-pembelian', [
            'pembelian_id' => $pembelian->id,
            'barang_id' => $barang->id,
            'jenis_retur' => 'fisik',
            'qty_retur' => 5,
            'status_kondisi' => 'bagus',
            'alasan' => 'Overstock'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('returs', [
            'tipe' => 'pembelian',
            'referensi_id' => $pembelian->id,
            'qty' => 5,
        ]);

        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'stok_akhir' => 45, // 50 - 5
        ]);
    }

    public function test_eksekusi_retur_pending_with_multiple_items_calculates_combined_dn_and_updates_utang()
    {
        $user = User::factory()->create(['role' => 'return_barang']);
        $barang1 = Barang::create(['kode_barang' => 'M1', 'nama_barang' => 'Produk M1', 'stok_akhir' => 10]);
        $barang2 = Barang::create(['kode_barang' => 'M2', 'nama_barang' => 'Produk M2', 'stok_akhir' => 15]);

        // Create PO items with same no_pembelian
        $pembelian1 = Pembelian::create([
            'no_pembelian' => 'PO-MULTI-001',
            'nama_supplier' => 'Supplier Multi',
            'barang_id' => $barang1->id,
            'jumlah_beli' => 5,
            'harga_beli_hpp' => 1000,
            'total_bayar' => 5000,
            'tanggal_beli' => now(),
        ]);

        $pembelian2 = Pembelian::create([
            'no_pembelian' => 'PO-MULTI-001',
            'nama_supplier' => 'Supplier Multi',
            'barang_id' => $barang2->id,
            'jumlah_beli' => 5,
            'harga_beli_hpp' => 2000,
            'total_bayar' => 10000,
            'tanggal_beli' => now(),
        ]);

        // Create 1 Utang record pointing to the first purchase item ID
        $utang = Utang::create([
            'no_utang_jurnal' => 'UT-MULTI-001',
            'pembelian_id' => $pembelian1->id,
            'total_utang' => 15000.00,
            'total_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'tanggal_jatuh_tempo' => now(),
        ]);

        // Create 2 pending return records in returs table sharing the same no_retur
        $noRetur = 'RE-QC-MULTI-999';
        
        $retur1Id = \DB::table('returs')->insertGetId([
            'no_retur' => $noRetur,
            'tipe' => 'pembelian',
            'jenis_retur' => 'harga_debit_note',
            'referensi_id' => $pembelian1->id,
            'barang_id' => $barang1->id,
            'qty' => 2,
            'kondisi' => 'tidak_mempengaruhi',
            'nominal_potongan' => 2000.00,
            'status_retur' => 'pending',
            'alasan' => 'Barang kurang M1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $retur2Id = \DB::table('returs')->insertGetId([
            'no_retur' => $noRetur,
            'tipe' => 'pembelian',
            'jenis_retur' => 'harga_debit_note',
            'referensi_id' => $pembelian2->id,
            'barang_id' => $barang2->id,
            'qty' => 1,
            'kondisi' => 'tidak_mempengaruhi',
            'nominal_potongan' => 2000.00,
            'status_retur' => 'pending',
            'alasan' => 'Barang kurang M2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Execute return by hitting the URL for the SECOND return item (which has referensi_id = pembelian2->id)
        $response = $this->actingAs($user)->post("/warehouse/retur-pembelian/eksekusi/{$retur2Id}");

        $response->assertRedirect();

        // 1. Verify both return records are completed
        $this->assertDatabaseHas('returs', [
            'id' => $retur1Id,
            'status_retur' => 'completed',
        ]);
        $this->assertDatabaseHas('returs', [
            'id' => $retur2Id,
            'status_retur' => 'completed',
        ]);

        // 2. Verify CreditNote is created with referensi_id pointing to pembelian1->id (the one with the Utang)
        // and nominal is combined (2000 + 2000 = 4000)
        $this->assertDatabaseHas('credit_notes', [
            'tipe' => 'pembelian',
            'referensi_id' => $pembelian1->id,
            'nominal' => 4000.00,
        ]);

        // 3. Verify Utang record's potongan_dn is updated to 4000.00
        $this->assertDatabaseHas('utangs', [
            'pembelian_id' => $pembelian1->id,
            'potongan_dn' => 4000.00,
        ]);
    }
}
