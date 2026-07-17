<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Piutang;
use App\Models\User;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Nonaktifkan foreign key checks untuk truncate
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Customer::truncate();
        Supplier::truncate();
        Barang::truncate();
        Penjualan::truncate();
        PenjualanDetail::truncate();
        Piutang::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $salesUser = User::where('role', 'sales')->first() ?? User::first();
        $direkturUser = User::where('role', 'direktur')->first() ?? User::first();

        // 1. Buat Dummy Supplier (5)
        $suppliers = [];
        for ($i = 1; $i <= 5; $i++) {
            $supplier = new Supplier();
            $supplier->kode_supplier = 'SUP-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $supplier->nama_supplier = $faker->company;
            $supplier->telepon = $faker->phoneNumber;
            $supplier->alamat = $faker->address;
            $supplier->jatuh_tempo_hari = 30;
            $supplier->save();
            $suppliers[] = $supplier;
        }

        // 2. Buat Dummy Customer (10)
        $customers = [];
        for ($i = 1; $i <= 10; $i++) {
            $customers[] = Customer::create([
                'id_cust' => 'CUST-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_customer' => $faker->company,
                'tingkat_customer' => $faker->randomElement(['Platinum', 'Gold', 'Silver', 'Reguler']),
                'npwp' => '01.' . $faker->randomNumber(8) . '.' . $faker->randomNumber(1) . '-' . $faker->randomNumber(3) . '.000',
                'ktp' => $faker->nik,
                'no_telp' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'plafon' => $faker->randomElement([50000000, 100000000, 200000000]),
                'tempo_hari' => $faker->randomElement([14, 30, 45, 60]),
            ]);
        }

        // 3. Buat Dummy Barang (30)
        $barangs = [];
        for ($i = 1; $i <= 30; $i++) {
            $hargaBeli = $faker->randomElement([50000, 100000, 150000, 250000, 500000]);
            $hargaJual = $hargaBeli + ($hargaBeli * 0.3); // Profit 30%
            
            $stok = $faker->randomElement([5, 10, 50, 100, 150]); // Ada stok kritis (5, 10)
            
            $barangs[] = Barang::create([
                'kode_barang' => 'BRG-' . strtoupper($faker->lexify('???')) . '-' . $faker->randomNumber(3),
                'nama_barang' => 'Sparepart ' . $faker->words(2, true),
                'kategori' => $faker->randomElement(['Otomotif', 'Mesin', 'Elektrikal', 'Aksesoris']),
                'merek' => $faker->randomElement(['Yamaha', 'Honda', 'Suzuki', 'Toyota', 'Mitsubishi']),
                'satuan' => $faker->randomElement(['Pcs', 'Set', 'Dus']),
                'stok_awal' => 0,
                'barang_masuk' => $stok + 10,
                'barang_keluar' => 10,
                'stok_akhir' => $stok,
                'stok_rusak' => $faker->numberBetween(0, 5),
                'harga_beli' => $hargaBeli,
                'harga_jual' => $hargaJual,
                'lokasi_rak' => 'Rak ' . $faker->randomLetter . '-' . $faker->numberBetween(1, 10),
                'supplier' => $faker->randomElement($suppliers)->nama_supplier,
            ]);
        }

        // 4. Buat Dummy Penjualan (SO) (50)
        $statuses = ['draft', 'pending', 'diproses', 'packing_selesai', 'ready_to_invoice', 'completed', 'ditolak', 'menunggu_restock'];
        
        for ($i = 1; $i <= 50; $i++) {
            $status = $faker->randomElement($statuses);
            $customer = $faker->randomElement($customers);
            $tanggal = $faker->dateTimeBetween('-5 months', 'now');
            
            $so = new Penjualan();
            $so->no_so = 'SO-' . date('Ymd', $tanggal->getTimestamp()) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $so->tanggal_order = $tanggal;
            $so->customer_id = $customer->id;
            $so->user_id = $salesUser->id;
            $so->total_semua = 0;
            $so->status_approval = in_array($status, ['diproses', 'packing_selesai', 'ready_to_invoice', 'completed', 'menunggu_restock']) ? 'disetujui' : ($status == 'ditolak' ? 'ditolak' : 'pending');
            $so->catatan = $faker->sentence;
            $so->sales_created_at = $tanggal;
            $so->approved_by = in_array($status, ['diproses', 'packing_selesai', 'ready_to_invoice', 'completed', 'menunggu_restock']) ? $direkturUser->id : null;
            $so->approved_at = in_array($status, ['diproses', 'packing_selesai', 'ready_to_invoice', 'completed', 'menunggu_restock']) ? Carbon::instance($tanggal)->addHours(2) : null;
            $so->status = $status;
            $so->created_at = $tanggal;
            $so->updated_at = $tanggal;
            $so->save();

            $totalSemua = 0;
            // Tambahkan 2-5 item barang per SO
            $jmlItem = $faker->numberBetween(2, 5);
            $selectedBarangs = $faker->randomElements($barangs, $jmlItem);
            
            foreach ($selectedBarangs as $barang) {
                $jumlah = $faker->numberBetween(1, 10);
                $subtotal = $jumlah * $barang->harga_jual;
                $totalSemua += $subtotal;
                
                PenjualanDetail::create([
                    'penjualan_id' => $so->id,
                    'barang_id' => $barang->id,
                    'hpp' => $barang->harga_beli,
                    'harga_satuan' => $barang->harga_jual,
                    'diskon' => 0,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal
                ]);
            }
            
            $so->update(['total_semua' => $totalSemua]);

            // Jika status diproses atau packing selesai, buat tagihan Piutang
            if (in_array($status, ['diproses', 'packing_selesai', 'ready_to_invoice', 'completed', 'menunggu_restock'])) {
                $totalDibayar = $faker->randomElement([0, 0, $totalSemua * 0.5, $totalSemua]);
                $statusBayar = 'belum_bayar';
                if ($totalDibayar > 0 && $totalDibayar < $totalSemua) $statusBayar = 'cicil';
                if ($totalDibayar == $totalSemua) $statusBayar = 'lunas';

                Piutang::create([
                    'no_invoice' => 'INV-' . date('Ymd', $tanggal->getTimestamp()) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'penjualan_id' => $so->id,
                    'total_tagihan' => $totalSemua,
                    'total_dibayar' => $totalDibayar,
                    'status_bayar' => $statusBayar,
                    'jatuh_tempo' => Carbon::instance($tanggal)->addDays($customer->tempo_hari),
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal
                ]);
            }
        }

        // 5. Buat Dummy Pembelian (PO) & Utang
        for ($i = 1; $i <= 10; $i++) {
            $supplier = $faker->randomElement($suppliers);
            $tanggal = $faker->dateTimeBetween('-3 months', 'now');
            $barang = $faker->randomElement($barangs);
            $qty = $faker->numberBetween(10, 50);
            $totalBayar = $qty * $barang->harga_beli;

            $pembelian = \App\Models\Pembelian::create([
                'no_pembelian' => 'PO-' . date('Ymd', $tanggal->getTimestamp()) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_supplier' => $supplier->nama_supplier,
                'tanggal_beli' => $tanggal,
                'barang_id' => $barang->id,
                'harga_beli_hpp' => $barang->harga_beli,
                'jumlah_beli' => $qty,
                'total_bayar' => $totalBayar,
                'status_barang' => 'selesai',
                'created_at' => $tanggal,
                'updated_at' => $tanggal
            ]);

            $totalDibayar = $faker->randomElement([0, $totalBayar * 0.5, $totalBayar]);
            $statusBayar = 'belum_bayar';
            if ($totalDibayar > 0 && $totalDibayar < $totalBayar) $statusBayar = 'cicil'; 
            if ($totalDibayar == $totalBayar) $statusBayar = 'lunas';

            // Simpan Utang menggunakan DB::table untuk bypass error kolom jika model Utang belum sempurna
            DB::table('utangs')->insert([
                'no_utang_jurnal' => 'UTG-' . $pembelian->no_pembelian,
                'pembelian_id' => $pembelian->id,
                'total_utang' => $totalBayar,
                'total_dibayar' => $totalDibayar,
                'status_bayar' => $statusBayar,
                'tanggal_jatuh_tempo' => Carbon::instance($tanggal)->addDays(30),
                'created_at' => $tanggal,
                'updated_at' => $tanggal
            ]);
        }
    }
}
