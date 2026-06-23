<?php

define('LARAVEL_START', microtime(true));

// Determine the core Laravel path (local development vs shared hosting production)
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    $corePath = __DIR__.'/..';
} else {
    $corePath = __DIR__.'/../aplikasi_mai';
}

// Load Composer autoloader
if (file_exists($corePath.'/vendor/autoload.php')) {
    require $corePath.'/vendor/autoload.php';
} else {
    echo "Autoloader not found at " . htmlspecialchars($corePath) . "/vendor/autoload.php";
    exit;
}

// Bootstrap Laravel
$app = require_once $corePath.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

header('Content-Type: text/html; charset=utf-8');

try {
    $penjualans = \App\Models\Penjualan::with('customer')->orderBy('id', 'desc')->get();
    
    echo "<h3>Sistem Debug Penjualan</h3>";
    echo "Total Penjualan di DB: " . count($penjualans) . "<br><br>";
    
    if (count($penjualans) > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>No SO</th><th>Customer Name</th><th>Status Approval</th></tr>";
        foreach ($penjualans as $p) {
            $customerName = $p->customer ? $p->customer->nama_customer : "NULL/Tidak ditemukan";
            echo "<tr>";
            echo "<td>{$p->id}</td>";
            echo "<td>{$p->no_so}</td>";
            echo "<td>{$customerName}</td>";
            echo "<td>{$p->status_approval}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada data penjualan di database.";
    }
} catch (\Exception $e) {
    echo "Terjadi Error: " . $e->getMessage();
}
