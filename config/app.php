<?php

use Illuminate\Support\Facades\Facade;

$appUrl = env('APP_URL', 'http://localhost');
if ($appUrl && !str_contains($appUrl, '://')) {
    $appUrl = 'https://' . $appUrl;
}
$appUrl = rtrim($appUrl, '/');

// Fallback APP_KEY ako Railway variable nije ispravno postavljen
$appKey = env('APP_KEY');
if (empty($appKey) || (!str_starts_with($appKey, 'base64:') && strlen($appKey) !== 32)) {
    $appKey = 'base64:xfr0gBDIetetvY3Sqp2KbL26kl4cTYLi4yI9mmNOFwA=';
}

return [
    'name' => env('APP_NAME', 'DRNDA ERP'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => $appUrl,
    'timezone' => 'Europe/Belgrade',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'cipher' => 'AES-256-CBC',
    'key' => $appKey,
    'previous_keys' => [],
    'maintenance' => ['driver' => 'file'],
    'providers' => Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
    ])->toArray(),
    'aliases' => Facade::defaultAliases()->merge([])->toArray(),
];
