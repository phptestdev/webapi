<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\VhostInterface;
use App\Services\VhostNginxService;

class VhostServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            VhostInterface::class,
            VhostNginxService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
