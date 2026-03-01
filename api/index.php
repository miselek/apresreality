<?php

/**
 * Vercel Serverless Entry Point for Laravel
 *
 * Bootstraps Laravel in a serverless environment.
 * On cold starts with SQLite, auto-runs migrations + seed.
 */

// Ensure writable directories exist in /tmp
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

// Auto-migrate SQLite on cold start
$dbPath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
if (getenv('DB_CONNECTION') === 'sqlite' && !file_exists($dbPath)) {
    touch($dbPath);

    // Bootstrap Laravel for artisan commands
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('migrate', ['--force' => true, '--seed' => true]);

    // Handle the actual web request
    $app->handleRequest(\Illuminate\Http\Request::capture());
    return;
}

// Normal request handling
require __DIR__ . '/../public/index.php';
