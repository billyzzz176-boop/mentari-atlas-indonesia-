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

echo "<h2><span style='color: blue;'>⚙️ Mentari Atlas - System Maintenance</span></h2>";
echo "<hr>";

try {
    echo "<b>Membersihkan Semua Cache Server...</b><br>";
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "<pre style='background:#1e1e1e; color:#0f0; padding:10px; border-radius:5px;'>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";

    echo "<b>Memperbaiki Jalur Gambar (Storage Link)...</b><br>";
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        echo "<pre style='background:#1e1e1e; color:#0f0; padding:10px; border-radius:5px;'>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Throwable $ex) {
        echo "<pre style='background:#1e1e1e; color:#ffc107; padding:10px; border-radius:5px;'>⚠️ Warning: Gagal membuat storage link (kemungkinan fungsi PHP exec() dinonaktifkan di hosting Anda). Ini aman diabaikan jika link storage sebelumnya sudah terbentuk.</pre>";
    }

    echo "<b>Menjalankan Migrasi Database (Struktur & Kolom Baru)...</b><br>";
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "<pre style='background:#1e1e1e; color:#0f0; padding:10px; border-radius:5px;'>⚙️ " . \Illuminate\Support\Facades\Artisan::output() . "</pre>";

    echo "<b>Resetting PHP OPcache...</b><br>";
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo "<pre style='background:#1e1e1e; color:#0f0; padding:10px; border-radius:5px;'>✅ OPcache reset successfully.</pre>";
    } else {
        echo "<pre style='background:#1e1e1e; color:#0f0; padding:10px; border-radius:5px;'>⚠️ OPcache is not enabled or opcache_reset function is disabled.</pre>";
    }

    echo "<h3 style='color:green;'>✅ Mantap! Sistem web sekarang sudah fresh.</h3>";
} catch (\Exception $e) {
    echo "<h3 style='color:red;'>❌ Gagal membersihkan cache:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
