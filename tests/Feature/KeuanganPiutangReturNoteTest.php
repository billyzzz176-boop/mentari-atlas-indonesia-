<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Penjualan;
use App\Models\Barang;
use App\Models\Piutang;
use App\Models\PenjualanDetail;

class KeuanganPiutangReturNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_retur_penjualan_records_piutang_adjustment_note()
    {
        $user = User::factory()->create(['role' => 'return_barang']);

        $customer = Customer::create([
            'id_cust' => 'CUST002',
            'nama_customer' => 'PT Contoh Customer 2',
        ]);

        $barang = Barang::create([
            'kode_barang' => 'B300',
            'nama_barang' => 'Barang Retur',
            'stok_akhir' => 10,
            'barang_masuk' => 10,
            'barang_keluar' => 0,
            'harga_jual' => 100000,
        ]);

        $penjualan = Penjualan::create([
            'no_so' => 'SO-RETUR-001',
            'tanggal_order' => now()->toDateString(),
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'total_semua' => 200000,
            'status_approval' => 'disetujui',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'harga_satuan' => 100000,
            'subtotal' => 200000,
        ]);

        $piutang = Piutang::create([
            'no_invoice' => 'INV-RETUR-001',
            'penjualan_id' => $penjualan->id,
            'total_tagihan' => 200000,
            'total_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'jatuh_tempo' => now()->addDays(30)->toDateString(),
        ]);

        $response = $this->actingAs($user)->post(route('retur.penjualan.store'), [
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'jenis_retur' => 'harga_credit_note',
            'qty_retur' => 2,
            'nominal_potongan' => 200000,
            'status_kondisi' => 'bagus',
            'alasan' => 'Barang retur karena cacat',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('piutangs', [
            'id' => $piutang->id,
            'total_tagihan' => 0,
            'status_bayar' => 'lunas',
        ]);

        $this->assertDatabaseHas('pembayaran_piutangs', [
            'piutang_id' => $piutang->id,
            'jumlah_bayar' => 200000,
            'metode_pembayaran' => 'Retur Customer / Credit Note',
        ]);
    }
}
