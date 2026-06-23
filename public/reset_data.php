<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// MENGARAH KE FOLDER aplikasi_mai
$appPath = __DIR__ . '/../aplikasi_mai/'; 

if (!file_exists($appPath . 'vendor/autoload.php')) {
    die("<h2 style='color:red;'>Gagal: Folder 'vendor' tidak ditemukan di dalam 'aplikasi_mai'. Coba upload ulang folder mentariatlas lu ke dalam folder aplikasi_mai di cPanel.</h2>");
}

require $appPath . 'vendor/autoload.php';
$app = require_once $appPath . 'bootstrap/app.php';

// Bootstrap the Laravel Console Kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2><span style='color: red;'>⚠️ Mentari Atlas - DANGER ZONE (Reset Database)</span></h2>";
echo "<hr>";

try {
    echo "<b>Menghapus seluruh tabel dan membuat ulang struktur (Migrate Fresh)...</b><br>";
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
        '--seed' => true,
        '--force' => true
    ]);
    
    echo "<pre style='background:#1e1e1e; color:#ffb86c; padding:10px; border-radius:5px;'>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    
    echo "<h3 style='color:green;'>✅ Database berhasil di-reset menjadi seperti pabrik!</h3>";
    echo "<p style='color:red;'><strong>Saran Keamanan:</strong> Jangan lupa <b>HAPUS</b> file <code>reset_data.php</code> ini dari cPanel kalau web sudah mulai dipakai jualan, biar data gak sengaja ke-reset orang iseng!</p>";
} catch (\Exception $e) {
    echo "<h3 style='color:red;'>❌ Gagal me-reset database:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
