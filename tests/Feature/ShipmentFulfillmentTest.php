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
use App\Models\BackOrder;
use App\Models\Pengiriman;
use App\Models\PengirimanDetail;

class ShipmentFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_packing_creates_initial_shipment_with_details()
    {
        $user = User::factory()->create(['role' => 'direktur']);
        $customer = Customer::create(['id_cust' => 'CUST_SH1', 'nama_customer' => 'Customer Ship 1', 'tempo_hari' => 30]);
        
        $barang1 = Barang::create(['kode_barang' => 'SH_B1', 'nama_barang' => 'Barang Ship 1', 'stok_akhir' => 10, 'barang_keluar' => 0]);
        $barang2 = Barang::create(['kode_barang' => 'SH_B2', 'nama_barang' => 'Barang Ship 2', 'stok_akhir' => 0, 'barang_keluar' => 0]); // out of stock

        $penjualan = Penjualan::create([
            'no_so' => 'SO-SH-001',
            'tanggal_order' => now(),
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'total_semua' => 2500,
            'status_approval' => 'disetujui'
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang1->id,
            'jumlah' => 3,
            'harga_satuan' => 500,
            'subtotal' => 1500
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang2->id,
            'jumlah' => 1,
            'harga_satuan' => 1000,
            'subtotal' => 1000
        ]);

        // Trigger packing
        $responsePacking = $this->actingAs($user)->post(route('penjualan.packingSelesai', $penjualan->id));
        $responsePacking->assertRedirect();

        // 1. Verify that 1 shipment is created
        $this->assertDatabaseHas('pengirimans', [
            'penjualan_id' => $penjualan->id,
            'no_pengiriman' => 'SJ-SH-001-1',
            'no_invoice' => 'INV-SH-001-1'
        ]);

        $pengiriman = Pengiriman::where('penjualan_id', $penjualan->id)->first();
        $this->assertNotNull($pengiriman);

        // 2. Verify shipment details (only ready item should be shipped)
        $this->assertDatabaseHas('pengiriman_details', [
            'pengiriman_id' => $pengiriman->id,
            'barang_id' => $barang1->id,
            'jumlah_kirim' => 3,
            'harga_satuan' => 500,
            'subtotal' => 1500
        ]);

        // Item 2 should not have a shipment detail since qty shipped = 0
        $this->assertDatabaseMissing('pengiriman_details', [
            'pengiriman_id' => $pengiriman->id,
            'barang_id' => $barang2->id
        ]);

        // 3. Verify backorder created for item 2
        $this->assertDatabaseHas('back_orders', [
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang2->id,
            'jumlah_diminta' => 1,
            'jumlah_kurang' => 1,
            'status_bo' => 'antrean'
        ]);

        // 4. Verify print route works
        $responsePrintSJ = $this->actingAs($user)->get(route('penjualan.printSuratJalanPengiriman', $pengiriman->id));
        $responsePrintSJ->assertOk();
        $responsePrintSJ->assertSee('Barang Ship 1');

        $responsePrintInv = $this->actingAs($user)->get(route('penjualan.printFakturPengiriman', $pengiriman->id));
        $responsePrintInv->assertOk();
        $responsePrintInv->assertSee('Barang Ship 1');
    }

    public function test_backorder_release_creates_subsequent_shipments()
    {
        $user = User::factory()->create(['role' => 'direktur']);
        $customer = Customer::create(['id_cust' => 'CUST_SH2', 'nama_customer' => 'Customer Ship 2', 'tempo_hari' => 30]);
        
        $barang = Barang::create(['kode_barang' => 'SH_B3', 'nama_barang' => 'Barang Ship 3', 'stok_akhir' => 10, 'barang_keluar' => 0]);

        $penjualan = Penjualan::create([
            'no_so' => 'SO-SH-002',
            'tanggal_order' => now(),
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'total_semua' => 1000,
            'status_approval' => 'disetujui'
        ]);
        $penjualan->status = 'ready_to_invoice';
        $penjualan->save();

        // Create initial shipment manually for sequence 1
        $pengiriman1 = Pengiriman::create([
            'penjualan_id' => $penjualan->id,
            'no_pengiriman' => 'SJ-SH-002-1',
            'no_invoice' => 'INV-SH-002-1',
            'tanggal_kirim' => now()->toDateString()
        ]);

        // Create BO manually
        $bo = BackOrder::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'jumlah_diminta' => 2,
            'jumlah_kurang' => 2,
            'status_bo' => 'antrean'
        ]);

        $detail = PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'harga_satuan' => 500,
            'subtotal' => 1000
        ]);

        // Create Piutang
        $piutang = Piutang::create([
            'no_invoice' => 'INV-SH-002',
            'penjualan_id' => $penjualan->id,
            'total_tagihan' => 0,
            'total_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'jatuh_tempo' => now()->addDays(30)->toDateString()
        ]);

        // Trigger BO release
        $responseRelease = $this->actingAs($user)->post(route('backorder.penebusan', $bo->id));
        $responseRelease->assertRedirect();

        // Verify shipment 2 is created
        $this->assertDatabaseHas('pengirimans', [
            'penjualan_id' => $penjualan->id,
            'no_pengiriman' => 'SJ-SH-002-2',
            'no_invoice' => 'INV-SH-002-2'
        ]);

        $pengiriman2 = Pengiriman::where('no_pengiriman', 'SJ-SH-002-2')->first();
        $this->assertNotNull($pengiriman2);

        $this->assertDatabaseHas('pengiriman_details', [
            'pengiriman_id' => $pengiriman2->id,
            'barang_id' => $barang->id,
            'jumlah_kirim' => 2,
            'harga_satuan' => 500,
            'subtotal' => 1000
        ]);
    }
}
