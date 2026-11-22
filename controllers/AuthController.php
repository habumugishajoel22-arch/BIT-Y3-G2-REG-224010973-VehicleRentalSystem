<?php
declare(strict_types=1);

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) { redirect('dashboard'); }
        render('auth/login', ['errors' => [], 'old' => []]);
    }

    public function login(): void
    {
        Csrf::verifyOrFail();

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $user = $username !== '' ? User::findByUsername($username) : null;

        // Same message for wrong username OR wrong password (do not reveal which)
        if (!$user || !password_verify($password, $user['password_hash'])) {
            render('auth/login', [
                'errors' => ['form' => 'Invalid username or password.'],
                'old'    => ['username' => $username],
            ]);
            return;
        }

        Auth::login($user);
        flash('success', 'Welcome back, ' . $user['username'] . '!');
        redirect('dashboard');
    }

    public function showRegister(): void
    {
        if (Auth::check()) { redirect('dashboard'); }
        render('auth/register', ['errors' => [], 'old' => []]);
    }

    public function register(): void
    {
        Csrf::verifyOrFail();

        $username = trim((string) ($_POST['username'] ?? ''));
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm  = (string) ($_POST['confirm_password'] ?? '');

        // Server-side validation (never trust only the JavaScript checks)
        $errors = [];
        if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
            $errors['username'] = 'Username must be 3-50 characters: letters, numbers, underscore.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }
        if (!isset($errors['username']) && User::findByUsername($username)) {
            $errors['username'] = 'That username is already taken.';
        }
        if (!isset($errors['email']) && User::findByEmail($email)) {
            $errors['email'] = 'That email is already registered.';
        }

        if (!$errors) {
            // The very first account becomes admin, the others are staff
            $role = User::countAll() === 0 ? 'admin' : 'staff';
            try {
                User::create($username, $email, password_hash($password, PASSWORD_DEFAULT), $role);
                flash('success', 'Account created. You can log in now.');
                redirect('login');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') { throw $ex; } // 23000 = duplicate key
                $errors['username'] = 'Username or email already exists.';
            }
        }

        render('auth/register', ['errors' => $errors, 'old' => ['username' => $username, 'email' => $email]]);
    }

    public function logout(): void
    {
        Csrf::verifyOrFail();
        Auth::logout();
        session_start();
        flash('success', 'You have been logged out.');
        redirect('login');
    }
}
