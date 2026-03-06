<?php

/**
 * Vercel Serverless Entry Point for Laravel
 *
 * Sets up writable /tmp paths before Laravel boots.
 * Auto-migrates SQLite on cold start.
 */

// 1. Ensure ALL writable directories exist in /tmp FIRST
$tmpDirs = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/app/public/properties',
    '/tmp/storage/framework',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap-cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Clear cached package manifest (dev packages like pail are not installed on Vercel)
$bootstrapCache = __DIR__ . '/../bootstrap/cache';
foreach (['packages.php', 'services.php'] as $cacheFile) {
    $path = $bootstrapCache . '/' . $cacheFile;
    if (file_exists($path)) {
        @unlink($path);
    }
}

// 3. Set environment variables BEFORE Laravel boots
//    so config files read /tmp paths via env()
$envOverrides = [
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'LOG_CHANNEL' => 'stderr',
    'CACHE_STORE' => 'array',
    'SESSION_DRIVER' => 'cookie',
];

foreach ($envOverrides as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

// 4. Auto-migrate SQLite on cold start
$dbPath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
if (getenv('DB_CONNECTION') === 'sqlite' && !file_exists($dbPath)) {
    touch($dbPath);

    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Override storage path to /tmp
    $app->useStoragePath('/tmp/storage');

    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('migrate', ['--force' => true, '--seed' => true]);

    // Handle the web request
    $app->handleRequest(\Illuminate\Http\Request::capture());
    return;
}

// 5. Normal request handling
require __DIR__ . '/../public/index.php';
