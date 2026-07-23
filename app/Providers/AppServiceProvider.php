<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Railway healthcheck salje HOST: 0.0.0.0 ili prazan string
        // .* prihvata SVE hostove ukljucujuci prazne (za razliku od .+ koji odbija prazne)
        SymfonyRequest::setTrustedHosts(['.*']);

        // Forsiraj HTTPS za URL generisanje u produkciji
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
