<?php

/**
 * Vercel Serverless Entry Point for Laravel
 *
 * Sets up writable /tmp paths before Laravel boots.
 * Auto-migrates SQLite on cold start.
 */

// Enable error reporting
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
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

    // 2. Copy packages.php to writable /tmp (so Laravel can also write services.php next to it)
    $srcPackages = __DIR__ . '/../bootstrap/cache/packages.php';
    $dstPackages = '/tmp/bootstrap-cache/packages.php';
    if (file_exists($srcPackages) && !file_exists($dstPackages)) {
        copy($srcPackages, $dstPackages);
    }

    // 3. Force HTTPS (Vercel proxies to PHP over HTTP internally)
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;

    // 4. Set environment variables BEFORE Laravel boots
    $envOverrides = [
        'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
        'LOG_CHANNEL' => 'stderr',
        'CACHE_STORE' => 'array',
        'SESSION_DRIVER' => 'cookie',
        'ASSET_URL' => 'https://apresreality.vercel.app',
        // Redirect bootstrap cache to writable /tmp (project dir is read-only on Vercel)
        'APP_PACKAGES_CACHE' => '/tmp/bootstrap-cache/packages.php',
        'APP_SERVICES_CACHE' => '/tmp/bootstrap-cache/services.php',
        'APP_CONFIG_CACHE' => '/tmp/bootstrap-cache/config.php',
        'APP_ROUTES_CACHE' => '/tmp/bootstrap-cache/routes-v7.php',
        'APP_EVENTS_CACHE' => '/tmp/bootstrap-cache/events.php',
    ];

    foreach ($envOverrides as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    // 4. Auto-migrate SQLite on cold start
    $dbPath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
    $needsMigration = false;

    if (getenv('DB_CONNECTION') === 'sqlite') {
        if (!file_exists($dbPath)) {
            touch($dbPath);
            $needsMigration = true;
        } else {
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
        $app->useStoragePath('/tmp/storage');

        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->call('migrate', ['--force' => true, '--seed' => true]);

        $app->handleRequest(\Illuminate\Http\Request::capture());
        return;
    }

    // 5. Normal request handling
    require __DIR__ . '/../public/index.php';

} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo "FATAL ERROR in api/index.php:\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
