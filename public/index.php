<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine the core Laravel path (local development vs shared hosting production)
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    $corePath = __DIR__.'/..';
} else {
    $corePath = __DIR__.'/../aplikasi_mai';
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $corePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $corePath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once $corePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
