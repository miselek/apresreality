<?php

/**
 * Vercel Serverless Entry Point for Laravel
 *
 * Sets up writable /tmp paths before Laravel boots.
 * Auto-migrates SQLite on cold start.
 */

// Enable error reporting for debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

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
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Set environment variables BEFORE Laravel boots
//    Redirect writable paths to /tmp (filesystem is read-only on Vercel)
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

// 3. Auto-migrate SQLite on cold start
$dbPath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
$needsMigration = false;

if (getenv('DB_CONNECTION') === 'sqlite') {
    if (!file_exists($dbPath)) {
        touch($dbPath);
        $needsMigration = true;
    } else {
        // Check if DB has tables (might be empty from a failed previous cold start)
        try {
            $pdo = new PDO('sqlite:' . $dbPath);
            $result = $pdo->query("SELECT count(*) FROM sqlite_master WHERE type='table' AND name='migrations'");
            $needsMigration = ($result->fetchColumn() == 0);
        } catch (\Exception $e) {
            $needsMigration = true;
        }
    }
}

if ($needsMigration) {
    if (!file_exists($dbPath)) {
        touch($dbPath);
    }

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

// 4. Normal request handling
require __DIR__ . '/../public/index.php';
