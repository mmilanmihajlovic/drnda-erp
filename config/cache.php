<?php

use Illuminate\Support\Str;

return [
    // HARDKODIRANO na 'file' - sprecava Redis timeout koji blokira bootstrap
    // Railway CACHE_STORE variable se ignoriše namerno
    'default' => 'file',
    'stores' => [
        'array'    => ['driver' => 'array', 'serialize' => false],
        'file'     => ['driver' => 'file', 'path' => storage_path('framework/cache/data'), 'lock_path' => storage_path('framework/cache/data')],
        'database' => ['driver' => 'database', 'connection' => env('DB_CACHE_CONNECTION'), 'table' => env('DB_CACHE_TABLE', 'cache'), 'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'), 'lock_table' => env('DB_CACHE_LOCK_TABLE')],
    ],
    'prefix' => Str::slug(env('APP_NAME', 'drnda'), '_').'_cache_',
];
