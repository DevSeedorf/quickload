<?php
// Enable error reporting
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Set up writable directories in /tmp
$tmpDir = '/tmp/storage';
$cacheDir = '/tmp/bootstrap/cache';

// Create directories if they don't exist
if (!file_exists($tmpDir)) {
    mkdir($tmpDir, 0755, true);
}
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Set Laravel environment paths
putenv("APP_STORAGE_PATH=$tmpDir");
putenv("VIEW_COMPILED_PATH=$tmpDir/framework/views");
putenv("APP_BOOTSTRAP_CACHE_PATH=$cacheDir");

// Load Laravel
require __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../bootstrap/app.php';

