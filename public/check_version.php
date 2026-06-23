<?php
header('Content-Type: text/html; charset=utf-8');
echo "<h3>Diagnostik Versi Controller Mentari Atlas</h3>";

if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    $corePath = __DIR__.'/..';
} else {
    $corePath = __DIR__.'/../aplikasi_mai';
}

echo "Jalur Laravel Aktif: <b>" . htmlspecialchars($corePath) . "</b><br><br>";

$userControllerFile = $corePath . '/app/Http/Controllers/UserController.php';
$viewFile = $corePath . '/resources/views/warehouse/retur_penjualan_create.blade.php';

if (file_exists($viewFile)) {
    $viewContent = file_get_contents($viewFile);
    if (preg_match('/<select name="penjualan_id".*?>/s', $viewContent, $matches)) {
        echo "<span style='color: green; font-weight: bold;'>✅ OK:</span> Class select penjualan: <code>" . htmlspecialchars($matches[0]) . "</code><br>";
    } else {
        echo "<span style='color: red; font-weight: bold;'>❌ ERROR:</span> Tidak dapat menemukan tag select penjualan di view.<br>";
    }
} else {
    echo "<span style='color: red; font-weight: bold;'>❌ ERROR:</span> File view tidak ditemukan di: " . htmlspecialchars($viewFile) . "<br>";
}

if (file_exists($userControllerFile)) {
    $content = file_get_contents($userControllerFile);
    
    // Periksa apakah baris validasi 'manager' ada
    if (strpos($content, 'manager') !== false) {
        echo "<span style='color: green; font-weight: bold;'>✅ OK:</span> Validasi role 'manager' ditemukan di dalam UserController.php.<br>";
        
        // Cari baris validasi role untuk pembacaan visual
        preg_match_all('/\'role\'\s*=>\s*\'[^\']+\'/i', $content, $matches);
        if (!empty($matches[0])) {
            echo "Aturan validasi yang aktif di file:<br>";
            foreach ($matches[0] as $match) {
                echo "<pre style='background:#f4f4f4; padding:5px;'>" . htmlspecialchars($match) . "</pre>";
            }
        }
    } else {
        echo "<span style='color: red; font-weight: bold;'>❌ ERROR:</span> Validasi role 'manager' TIDAK ditemukan di dalam UserController.php.<br>";
        echo "Ini menandakan file update Anda belum ter-overwrite dengan benar di cPanel.<br>";
    }
} else {
    echo "<span style='color: red; font-weight: bold;'>❌ ERROR:</span> File UserController.php tidak ditemukan di jalur: " . htmlspecialchars($userControllerFile) . "<br>";
}

// Cek database dan data SO / PO
echo "<br><b>Diagnostik Data Database:</b><br>";
try {
    require_once $corePath . '/vendor/autoload.php';
    $app = require_once $corePath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $penjualanCount = \App\Models\Penjualan::count();
    echo "Jumlah Sales Order (SO): <b>" . $penjualanCount . "</b><br>";
    if ($penjualanCount > 0) {
        $pList = \App\Models\Penjualan::with('customer')->orderBy('id', 'desc')->take(5)->get();
        echo "5 SO Terakhir:<br><ul>";
        foreach ($pList as $p) {
            echo "<li>SO ID: " . $p->id . ", SO: " . htmlspecialchars($p->no_so) . " - " . htmlspecialchars($p->customer->nama_customer ?? 'Umum') . "</li>";
            
            // Cek muat barang SO
            try {
                $items = \Illuminate\Support\Facades\DB::table('penjualan_details')
                    ->join('barangs', 'penjualan_details.barang_id', '=', 'barangs.id')
                    ->where('penjualan_details.penjualan_id', $p->id)
                    ->select('penjualan_details.*', 'barangs.nama_barang')
                    ->get();
                echo " -- Barang di SO ini: <b>" . count($items) . " Pcs</b><br>";
                foreach ($items as $it) {
                    echo "   &bull; " . htmlspecialchars($it->nama_barang) . " (Qty: " . $it->jumlah . ", Satuan: " . $it->harga_satuan . ")<br>";
                }
            } catch (\Throwable $ex) {
                echo " -- <span style='color:red;'>Gagal muat barang SO: " . htmlspecialchars($ex->getMessage()) . "</span><br>";
            }
        }
        echo "</ul>";
    }

    $pembelianCount = \App\Models\Pembelian::distinct('no_pembelian')->count('no_pembelian');
    echo "Jumlah Purchase Order (PO) Unik: <b>" . $pembelianCount . "</b><br>";
    if ($pembelianCount > 0) {
        $poList = \App\Models\Pembelian::select('no_pembelian', 'nama_supplier')
            ->distinct()
            ->orderBy('no_pembelian', 'desc')
            ->take(5)
            ->get();
        echo "5 PO Terakhir:<br><ul>";
        foreach ($poList as $po) {
            echo "<li>PO: " . htmlspecialchars($po->no_pembelian) . " - " . htmlspecialchars($po->nama_supplier) . "</li>";
            
            // Cek muat barang PO
            try {
                $items = \Illuminate\Support\Facades\DB::table('pembelians')
                    ->join('barangs', 'pembelians.barang_id', '=', 'barangs.id')
                    ->where('pembelians.no_pembelian', $po->no_pembelian)
                    ->select('pembelians.*', 'barangs.nama_barang')
                    ->get();
                echo " -- Barang di PO ini: <b>" . count($items) . " Pcs</b><br>";
                foreach ($items as $it) {
                    echo "   &bull; " . htmlspecialchars($it->nama_barang) . " (Qty: " . $it->jumlah_beli . ", Beli HPP: " . ($it->harga_beli_hpp ?? 0) . ")<br>";
                }
            } catch (\Throwable $ex) {
                echo " -- <span style='color:red;'>Gagal muat barang PO: " . htmlspecialchars($ex->getMessage()) . "</span><br>";
            }
        }
        echo "</ul>";
    }
} catch (\Throwable $e) {
    echo "<span style='color: red;'>Gagal Query Database: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}

// Cek status OPcache
echo "<br><b>Status OPcache:</b><br>";
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status(false);
    if ($status) {
        echo "OPcache aktif.<br>";
        if (isset($status['scripts'][$userControllerFile])) {
            echo "File UserController.php tercache di OPcache. Terakhir diubah pada: " . date('Y-m-d H:i:s', $status['scripts'][$userControllerFile]['last_used_timestamp']) . "<br>";
        } else {
            echo "File UserController.php tidak tercache di OPcache.<br>";
        }
    } else {
        echo "OPcache tidak aktif atau tidak dikonfigurasi.<br>";
    }
} else {
    echo "Fungsi opcache_get_status tidak tersedia.<br>";
}

