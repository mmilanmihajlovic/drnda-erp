<?php

use Illuminate\Support\Facades\Facade;

$appUrl = env('APP_URL', 'http://localhost');
if ($appUrl && !str_contains($appUrl, '://')) {
    $appUrl = 'https://' . $appUrl;
}
$appUrl = rtrim($appUrl, '/');

// Hardkodiran APP_KEY ako Railway variable nije ispravan
// (sprecava RuntimeException: Unsupported cipher or incorrect key length)
$appKey = env('APP_KEY', '');
if (empty($appKey) || strlen(str_replace('base64:', '', $appKey)) < 20) {
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
