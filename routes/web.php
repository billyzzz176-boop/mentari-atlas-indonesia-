<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PembelianController; 
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\BackOrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ActivityLogController; 
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LabaController;
use App\Http\Controllers\LaporanController; // Tambahkan LaporanController

// Halaman utama otomatis dialihkan langsung ke halaman login resmi
Route::get('/', function () {
    return redirect()->route('login');
});

// Rute Otentikasi (Publik)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Utilitas Migrasi (Publik untuk memudahkan sinkronisasi hosting)
Route::get('/run-migration', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "<h3>Artisan Migrate Output:</h3><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre><br><a href='/'>Kembali ke Beranda</a>";
    } catch (\Exception $e) {
        return "<h3>Artisan Migrate Gagal:</h3><pre>" . $e->getMessage() . "</pre>";
    }
});

// Rute yang WAJIB Login
Route::middleware(['auth'])->group(function () {

    // ===================================================
    // AKSES UMUM & UTILITY AJAX (Bisa Diakses Semua Role)
    // ===================================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Rute Bypass Symlink cPanel (403 Forbidden Fix)
    Route::get('/berkas/{path}', function ($path) {
        $file = storage_path('app/public/' . $path);
        if (!file_exists($file)) abort(404);
        return response()->file($file);
    })->where('path', '.*')->name('berkas');

    // ===================================================
    // PUSAT LAPORAN KUSTOM (SMART REPORT BUILDER)
    // ===================================================
    Route::get('/pusat-laporan', [LaporanController::class, 'index'])->name('laporan.hub');
    Route::post('/pusat-laporan/generate', [LaporanController::class, 'generate'])->name('laporan.generate');

    // Endpoint AJAX untuk mengambil item barang berdasarkan SO / PO
    Route::get('/get-items-so/{id}', [ReturController::class, 'getItemsSO'])->name('retur.get-items-so');
    Route::get('/get-items-po/{id}', [ReturController::class, 'getItemsPO'])->name('retur.get-items-po');

    // Endpoint API Notifikasi Real-time
    Route::get('/api/notifications', [DashboardController::class, 'getNotifications'])->name('api.notifications');

    // =======================================================================
    // RUTE BUAT ORDER: Semua Role (Termasuk Direktur) bisa tes buat order
    // =======================================================================
    Route::get('/penjualan/buat', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');

    // MANAJEMEN TINGKATAN & DATA CUSTOMER (Hanya Direktur & Hak Akses khusus - SALES DICABUT)
    Route::middleware(['role:direktur,tingkat_cust'])->group(function () {
        Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
        Route::post('/customer/{id}/update-tier', [CustomerController::class, 'updateTier'])->name('customer.updateTier');
        Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
        Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update');
        Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');
    });

    // HALAMAN KERJA DIVISI WAREHOUSE
    Route::middleware(['role:direktur,admin_warehouse,warehouse,admin warehouse'])->group(function () {
        Route::get('/warehouse/dashboard', [DashboardController::class, 'warehouseIndex'])->name('warehouse.dashboard');
        Route::post('/penjualan/{id}/packing-selesai', [PenjualanController::class, 'updateToPackingSelesai'])->name('penjualan.packingSelesai');
        Route::post('/penjualan/{id}/send-to-backorder', [PenjualanController::class, 'sendToBackorder'])->name('penjualan.sendToBackorder');
    });

    // FITUR RETUR BARANG
    Route::middleware(['role:direktur,return_barang'])->group(function () {
        Route::get('/warehouse/retur-penjualan', [ReturController::class, 'penjualanIndex'])->name('retur.penjualan.index');
        Route::get('/warehouse/retur-penjualan/create', [ReturController::class, 'penjualanCreate'])->name('retur.penjualan.create');
        Route::post('/warehouse/retur-penjualan', [ReturController::class, 'penjualanStore'])->name('retur.penjualan.store');
        
        Route::get('/warehouse/retur-pembelian', [ReturController::class, 'pembelianIndex'])->name('retur.pembelian.index');
        Route::get('/warehouse/retur-pembelian/create', [ReturController::class, 'pembelianCreate'])->name('retur.pembelian.create');
        Route::post('/warehouse/retur-pembelian', [ReturController::class, 'pembelianStore'])->name('retur.pembelian.store');
        
        // EKSEKUSI RETURN PENDING (RMA)
        Route::post('/warehouse/retur-pembelian/eksekusi/{id}', [ReturController::class, 'eksekusiReturPending'])->name('retur.pembelian.eksekusi');
    });

    // HALAMAN KERJA DIVISI KEUANGAN & PIUTANG
    Route::middleware(['role:direktur,admin_keuangan,keuangan,admin keuangan,admin_piutang,admin piutang'])->group(function () {
        Route::get('/keuangan/dashboard', [DashboardController::class, 'keuanganIndex'])->name('keuangan.dashboard');
    });

    // MANAJEMEN PENJUALAN (Riwayat SO)
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');

    // MASTER PEMBELIAN
    Route::middleware(['role:direktur,pembelian_stok'])->group(function () {
        Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
        Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
        Route::post('/pembelian/{id}/sortir', [PembelianController::class, 'prosesSortir'])->name('pembelian.sortir');
    });

    // MASTER DATA SUPPLIER
    Route::middleware(['role:direktur,data_supplier'])->group(function () {
        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
        Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
        Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    });

    // Route khusus akun staf
    Route::middleware(['role:direktur,akun_staf'])->group(function () {
        // MANAJEMEN ACCOUNTS / USER (Direktur & Hak Akses khusus)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware(['role:direktur,approval_so'])->group(function () {
        // Approval Direktur mutlak hanya untuk Direktur Utama
        Route::get('/penjualan/approval', [PenjualanController::class, 'approvalList'])->name('penjualan.approval');
        Route::post('/penjualan/approve/{id}', [PenjualanController::class, 'approve'])->name('penjualan.approve');
    });

    Route::middleware(['role:direktur,audit_trail'])->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
    });

    // KELOLA PENJUALAN (EDIT & HAPUS)
    Route::middleware(['role:sales,direktur'])->group(function () {
        Route::get('/sales/dashboard', [DashboardController::class, 'salesIndex'])->name('sales.dashboard');
        Route::get('/penjualan/{id}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
        Route::put('/penjualan/{id}', [PenjualanController::class, 'update'])->name('penjualan.update');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    });
    
    Route::get('/penjualan/{id}/cetak/faktur', [PenjualanController::class, 'printFaktur'])->name('penjualan.printFaktur');
    Route::get('/penjualan/{id}/cetak/surat-jalan', [PenjualanController::class, 'printSuratJalan'])->name('penjualan.printSuratJalan');
    Route::get('/pengiriman/{id}/cetak/faktur', [PenjualanController::class, 'printFakturPengiriman'])->name('penjualan.printFakturPengiriman');
    Route::get('/pengiriman/{id}/cetak/surat-jalan', [PenjualanController::class, 'printSuratJalanPengiriman'])->name('penjualan.printSuratJalanPengiriman');
    Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');

    // Temporary route to fix stuck SO
    Route::get('/fix-so', function() {
        // Fix SO yang tertahan di draft padahal sudah disetujui
        \App\Models\Penjualan::where('status_approval', 'disetujui')->where('status', 'draft')->update(['status' => 'diproses']);
        
        // Fix SO yang tertahan di menunggu_restock padahal sudah memiliki pengiriman
        $stuckBOs = \App\Models\Penjualan::where('status', 'menunggu_restock')->has('pengirimans')->get();
        foreach($stuckBOs as $so) {
            $so->status = 'ready_to_invoice';
            $so->save();
        }
        
        return 'Data SO yang tersangkut berhasil diperbaiki. Silakan kembali ke halaman aplikasi.';
    });

    // ANTRIAN BACK ORDER
    Route::middleware(['role:direktur,backorder'])->group(function () {
        Route::get('/backorder', [BackOrderController::class, 'index'])->name('backorder.index');
        Route::post('/backorder/penebusan/{id}', [BackOrderController::class, 'penebusan'])->name('backorder.penebusan');
    });

    // DATA BARANG
    Route::middleware(['role:direktur,data_barang'])->group(function () {
        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
        Route::get('/barang/{id}/history', [BarangController::class, 'history'])->name('barang.history');
        Route::post('/barang/import', [BarangController::class, 'importCsv'])->name('barang.import');
    });

    // LAPORAN LABA
    Route::middleware(['role:direktur,profit_laba'])->group(function () {
        Route::get('/laporan/laba', [LabaController::class, 'index'])->name('laba.index'); 
    });

    // KEUANGAN, PIUTANG, UTANG, CREDIT NOTE
    Route::middleware(['role:direktur,akses_keuangan,admin_keuangan,keuangan'])->group(function () {
        Route::post('/keuangan/credit-note/store', [KeuanganController::class, 'storeCreditNote'])->name('keuangan.creditNote.store');
        Route::get('/keuangan/piutang', [KeuanganController::class, 'piutangIndex'])->name('keuangan.piutang.index');
        Route::get('/keuangan/piutang/{id}', [KeuanganController::class, 'piutangShow'])->name('keuangan.piutang.show');
        Route::post('/keuangan/piutang/bayar/{id}', [KeuanganController::class, 'piutangBayar'])->name('keuangan.piutang.bayar');
        Route::get('/keuangan/utang', [KeuanganController::class, 'utangIndex'])->name('keuangan.utang.index');
        Route::get('/keuangan/utang/{id}', [KeuanganController::class, 'utangShow'])->name('keuangan.utang.show');
        Route::post('/keuangan/utang/bayar/{id}', [KeuanganController::class, 'utangBayar'])->name('keuangan.utang.bayar');
    });
});

// TEMPORARY ROUTE TO RUN DB MIGRATIONS FOR INDEXING
Route::get('/run-migration-indexes', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return '<h1>Migrasi Sukses!</h1><p>Database indexing telah berhasil ditambahkan. Silakan kembali ke <a href="/">Dashboard</a>.</p>';
    } catch (\Exception $e) {
        return '<h1>Gagal!</h1><p>' . $e->getMessage() . '</p>';
    }
});