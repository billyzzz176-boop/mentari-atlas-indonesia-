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

class BackOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_backorder_list_shows_multiple_items_on_mobile_and_desktop()
    {
        $user = User::factory()->create(['role' => 'direktur']);
        $customer = Customer::create(['id_cust' => 'CUST_BO1', 'nama_customer' => 'Customer BO 1']);
        
        $barang1 = Barang::create(['kode_barang' => 'BO_B1', 'nama_barang' => 'Barang BO 1', 'stok_akhir' => 0]);
        $barang2 = Barang::create(['kode_barang' => 'BO_B2', 'nama_barang' => 'Barang BO 2', 'stok_akhir' => 0]);

        $penjualan = Penjualan::create([
            'no_so' => 'SO-BO-001',
            'tanggal_order' => now(),
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'total_semua' => 3000,
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
            'jumlah' => 2,
            'harga_satuan' => 750,
            'subtotal' => 1500
        ]);

        // Simulating the action to send all items to Back Order due to 0 stock
        $responseSend = $this->actingAs($user)->post(route('penjualan.sendToBackorder', $penjualan->id));
        $responseSend->assertRedirect();

        // Verify that 2 backorder items are created
        $this->assertDatabaseHas('back_orders', [
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang1->id,
            'jumlah_diminta' => 3,
            'jumlah_kurang' => 3,
            'status_bo' => 'antrean'
        ]);

        $this->assertDatabaseHas('back_orders', [
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang2->id,
            'jumlah_diminta' => 2,
            'jumlah_kurang' => 2,
            'status_bo' => 'antrean'
        ]);

        // Get the list page
        $responseList = $this->actingAs($user)->get(route('backorder.index'));
        $responseList->assertOk();
        
        // Assert both items are rendered
        $responseList->assertSee('Barang BO 1');
        $responseList->assertSee('Barang BO 2');
        $responseList->assertSee('SO-BO-001');
    }

    public function test_backorder_penebusan_fulfills_specific_item_and_updates_stock_and_piutang()
    {
        $user = User::factory()->create(['role' => 'direktur']);
        $customer = Customer::create(['id_cust' => 'CUST_BO2', 'nama_customer' => 'Customer BO 2']);
        
        $barang1 = Barang::create(['kode_barang' => 'BO_B3', 'nama_barang' => 'Barang BO 3', 'stok_akhir' => 10]); // stocked
        $barang2 = Barang::create(['kode_barang' => 'BO_B4', 'nama_barang' => 'Barang BO 4', 'stok_akhir' => 0]);

        $penjualan = Penjualan::create([
            'no_so' => 'SO-BO-002',
            'tanggal_order' => now(),
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'total_semua' => 4000,
            'status_approval' => 'disetujui',
        ]);
        $penjualan->status = 'ready_to_invoice';
        $penjualan->save();

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang1->id,
            'jumlah' => 4,
            'harga_satuan' => 500,
            'subtotal' => 2000
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang2->id,
            'jumlah' => 2,
            'harga_satuan' => 1000,
            'subtotal' => 2000
        ]);

        // Create Piutang with initially 0 amount because no stock is shipped yet
        $piutang = Piutang::create([
            'no_invoice' => 'INV-BO-002',
            'penjualan_id' => $penjualan->id,
            'total_tagihan' => 0,
            'total_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'jatuh_tempo' => now()->addDays(30)->toDateString()
        ]);

        // Create the backorders manually
        $bo1 = BackOrder::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang1->id,
            'jumlah_diminta' => 4,
            'jumlah_kurang' => 4,
            'status_bo' => 'antrean'
        ]);

        $bo2 = BackOrder::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang2->id,
            'jumlah_diminta' => 2,
            'jumlah_kurang' => 2,
            'status_bo' => 'antrean'
        ]);

        // Fulfill the first backorder (BO 3) which has stock
        $responsePenebusan = $this->actingAs($user)->post(route('backorder.penebusan', $bo1->id));
        
        if ($responsePenebusan->getSession()->has('errors')) {
            dd($responsePenebusan->getSession()->get('errors'));
        }

        $responsePenebusan->assertRedirect();

        // 1. Verify BO 1 is terpenuhi and BO 2 remains antrean
        $this->assertDatabaseHas('back_orders', [
            'id' => $bo1->id,
            'status_bo' => 'terpenuhi'
        ]);

        $this->assertDatabaseHas('back_orders', [
            'id' => $bo2->id,
            'status_bo' => 'antrean'
        ]);

        // 2. Verify stock of barang1 is decreased (10 - 4 = 6)
        $this->assertDatabaseHas('barangs', [
            'id' => $barang1->id,
            'stok_akhir' => 6
        ]);

        // 3. Verify stock history recorded
        $this->assertDatabaseHas('stock_histories', [
            'barang_id' => $barang1->id,
            'event_type' => 'backorder_fulfillment',
            'change' => -4,
            'stock_before' => 10,
            'stock_after' => 6
        ]);

        // 4. Verify Piutang is updated with tagihan (4 * 500 = 2000)
        $this->assertDatabaseHas('piutangs', [
            'id' => $piutang->id,
            'total_tagihan' => 2000.00
        ]);
    }
}
