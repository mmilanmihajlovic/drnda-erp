<?php
session_start();
$config = require __DIR__ . '/../config/app.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $path = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($path)) require $path;
});

function data_file(string $name): string { return __DIR__ . '/../database/data/' . $name . '.json'; }
function read_data(string $name): array {
    $file = data_file($name);
    if (!is_file($file)) return [];
    $data = json_decode((string) file_get_contents($file), true);
    return is_array($data) ? $data : [];
}
function write_data(string $name, array $data): void {
    $file = data_file($name);
    if (!is_dir(dirname($file))) mkdir(dirname($file), 0775, true);
    file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}
function view(string $name, array $data = []): void {
    extract($data);
    $view = __DIR__ . '/Views/' . $name . '.php';
    require __DIR__ . '/Views/layouts/app.php';
}
function redirect(string $path): never { header('Location: ' . $path); exit; }
function auth_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    foreach (read_data('users') as $user) if ((int)$user['id'] === (int)$_SESSION['user_id']) return $user;
    return null;
}
function require_auth(): void { if (!auth_user()) redirect('/?page=login'); }
function csrf_token(): string { $_SESSION['csrf'] ??= bin2hex(random_bytes(24)); return $_SESSION['csrf']; }
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_token'] ?? '')) { http_response_code(419); exit('Nevažeći CSRF token.'); }
}
