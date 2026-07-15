<?php
namespace App\Controllers;

final class AuthController
{
    public function show(): void { view('auth/login', ['title' => 'Prijava']); }
    public function login(): void
    {
        verify_csrf();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = null;
        foreach (read_data('users') as $candidate) if (strcasecmp($candidate['email'], $email) === 0) { $user = $candidate; break; }
        if (!$user || !password_verify($password, $user['password_hash'])) {
            view('auth/login', ['title' => 'Prijava', 'error' => 'Pogrešan email ili lozinka.']); return;
        }
        $_SESSION['user_id'] = $user['id']; redirect('/');
    }
    public function logout(): void { verify_csrf(); session_destroy(); redirect('/?page=login'); }
}
