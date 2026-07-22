<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Check for maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

// Laravel 12: handleRequest() — ne stari Kernel::class pristup
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
