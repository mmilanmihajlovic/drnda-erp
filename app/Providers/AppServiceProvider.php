<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // URL::forceScheme za produkciju (HTTPS generisanje)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
