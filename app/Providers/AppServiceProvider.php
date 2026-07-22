<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fix za Symfony 7 strict host validation u containerizovanom okruzenju
        SymfonyRequest::setTrustedHosts([]);

        // Forsiraj HTTPS u produkciji
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
