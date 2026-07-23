<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ── Health check: odgovori 200 PRE nego sto Laravel krene ─────────────────
// Railway salje GET /up bez ispravnog HOST headera → Laravel baca
// "Invalid URI: Host is malformed" jer parse_url() ne moze da parsira prazan host.
// Resenje: vrati 200 direktno, bez bootstrapovanja Laravel-a.
if (($_SERVER['REQUEST_URI'] ?? '/') === '/up') {
    http_response_code(200);
    header('Content-Type: text/plain');
    echo 'OK';
    exit;
}

// ── Maintenance mode ────────────────────────────────────────────────────────
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// ── Boot Laravel 12 ─────────────────────────────────────────────────────────
require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
