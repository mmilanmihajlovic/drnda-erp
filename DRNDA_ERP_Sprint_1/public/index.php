<?php
require __DIR__ . '/../app/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\CaseController;

$page = $_GET['page'] ?? 'dashboard';
$method = $_SERVER['REQUEST_METHOD'];

$auth = new AuthController();
$cases = new CaseController();

if ($page === 'login' && $method === 'GET') return $auth->show();
if ($page === 'login' && $method === 'POST') return $auth->login();
if ($page === 'logout' && $method === 'POST') return $auth->logout();
if ($page === 'cases' && $method === 'GET') return $cases->index();
if ($page === 'case-create' && $method === 'GET') return $cases->create();
if ($page === 'case-store' && $method === 'POST') return $cases->store();
if ($page === 'case' && $method === 'GET') return $cases->show();

require_auth();
$allCases = read_data('cases');
$active = count(array_filter($allCases, fn(array $c): bool => $c['stage'] !== 'ZAVRSEN'));
$today = count(array_filter($allCases, fn(array $c): bool => substr($c['funeral_at'], 0, 10) === date('Y-m-d')));
view('dashboard', ['title' => 'Komandni centar', 'active' => $active, 'today' => $today]);
