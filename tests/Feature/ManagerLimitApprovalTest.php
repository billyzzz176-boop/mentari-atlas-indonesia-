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

class ManagerLimitApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_cannot_approve_over_limit_order()
    {
        // 1. Create a Manager user and a Customer with a small plafon (limit)
        $manager = User::factory()->create([
            'role' => 'manager',
            'hak_akses' => ['approval_so']
        ]);
        
        $customer = Customer::create([
            'id_cust' => 'CUST_MGR1',
            'nama_customer' => 'Customer Limit Test',
            'plafon' => 1000, // Plafon is 1000
            'tempo_hari' => 30
        ]);
        
        // 2. Create a Sales Order with a total of 1500 (which exceeds the plafon of 1000)
        $penjualan = Penjualan::create([
            'no_so' => 'SO-MGR-001',
            'tanggal_order' => now(),
            'customer_id' => $customer->id,
            'user_id' => $manager->id,
            'total_semua' => 1500, // Exceeds limit
            'status_approval' => 'pending'
        ]);

        // Try to approve as manager
        $response = $this->actingAs($manager)->post(route('penjualan.approve', $penjualan->id), [
            'status' => 'disetujui',
            'catatan' => 'Test approval over limit as manager'
        ]);

        // Must fail and redirect back with error
        $response->assertSessionHasErrors(['error']);
        
        // Assert the database status has NOT changed to disetujui
        $penjualan->refresh();
        $this->assertNotEquals('disetujui', $penjualan->status_approval);
    }

    public function test_manager_can_approve_within_limit_order()
    {
        // 1. Create a Manager user and a Customer with a large plafon (limit)
        $manager = User::factory()->create([
            'role' => 'manager',
            'hak_akses' => ['approval_so']
        ]);
        
        $customer = Customer::create([
            'id_cust' => 'CUST_MGR2',
            'nama_customer' => 'Customer Limit Test 2',
            'plafon' => 5000, // Plafon is 5000
            'tempo_hari' => 30
        ]);
        
        // 2. Create a Sales Order with a total of 1500 (within limit)
        $penjualan = Penjualan::create([
            'no_so' => 'SO-MGR-002',
            'tanggal_order' => now(),
            'customer_id' => $customer->id,
            'user_id' => $manager->id,
            'total_semua' => 1500, // Within limit
            'status_approval' => 'pending'
        ]);

        // Try to approve as manager
        $response = $this->actingAs($manager)->post(route('penjualan.approve', $penjualan->id), [
            'status' => 'disetujui',
            'catatan' => 'Test approval within limit as manager'
        ]);

        // Must succeed
        $response->assertSessionHasNoErrors();
        
        // Assert the database status has changed to disetujui
        $penjualan->refresh();
        $this->assertEquals('disetujui', $penjualan->status_approval);
    }
}
