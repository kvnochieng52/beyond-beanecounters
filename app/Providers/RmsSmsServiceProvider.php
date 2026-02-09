<?php

namespace App\Providers;

use App\Services\RmsSmsService;
use Illuminate\Support\ServiceProvider;

class RmsSmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RmsSmsService::class, function ($app) {
            return new RmsSmsService();
        });

        $this->app->alias(RmsSmsService::class, 'rms-sms');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
