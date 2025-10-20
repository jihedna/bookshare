<?php

namespace App\Providers;

use App\Auth\DoctrineUserProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class DoctrineUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Auth::provider('doctrine', function ($app, array $config) {
            return new DoctrineUserProvider();
        });
    }
}
