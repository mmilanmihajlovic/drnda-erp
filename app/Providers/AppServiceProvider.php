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
        // Prihvati sve hostove — Railway koristi interne hostove za healthcheck
        // koji mogu da ne propadnu Symfony validaciju
        \Symfony\Component\HttpFoundation\Request::setTrustedHosts(['.+']);

        // Ako smo u produkciji, forsiramo HTTPS za URL generisanje
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
