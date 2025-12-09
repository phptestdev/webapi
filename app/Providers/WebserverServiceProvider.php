<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WebserverInterface;
use App\Services\NginxService;

class WebserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            WebserverInterface::class,
            NginxService::class
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
