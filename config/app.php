<?php

use Illuminate\Support\Facades\Facade;

$appUrl = env('APP_URL', 'http://localhost');
if ($appUrl && !str_contains($appUrl, '://')) {
    $appUrl = 'https://' . $appUrl;
}
$appUrl = rtrim($appUrl, '/');

return [
    'name' => env('APP_NAME', 'DRNDA ERP'),
    'env' => env('APP_ENV', 'production'),
    'debug' => true, // TEMP DEBUG - vidimo gresku
    'url' => $appUrl,
    'timezone' => 'Europe/Belgrade',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [],
    'maintenance' => ['driver' => 'file'],
    'providers' => Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
    ])->toArray(),
    'aliases' => Facade::defaultAliases()->merge([])->toArray(),
];
