<?php
declare(strict_types=1);

class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true); // prevents session fixation
        $_SESSION['user'] = [
            'user_id'  => (int) $user['user_id'],
            'username' => $user['username'],
            'role'     => $user['role'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('error', 'Please log in first.');
            redirect('login');
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();
        if (self::user()['role'] !== $role) {
            http_response_code(403);
            exit('403 - You are not allowed to do this.');
        }
    }
}
