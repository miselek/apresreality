<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // On Vercel serverless, redirect writable paths to /tmp
        if (env('VERCEL')) {
            $this->app->useStoragePath('/tmp/storage');

            config([
                'view.compiled' => '/tmp/storage/framework/views',
                'cache.stores.file.path' => '/tmp/storage/framework/cache',
                'logging.channels.single.path' => '/tmp/storage/logs/laravel.log',
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
