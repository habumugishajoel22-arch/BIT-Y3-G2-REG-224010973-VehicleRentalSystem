<?php
declare(strict_types=1);

// Admin only (the router enforces this)
class UserController
{
    public function index(): void
    {
        render('users/index', ['users' => User::all()]);
    }

    public function createForm(): void
    {
        render('users/form', ['title' => 'Add user', 'action' => url('user_create'), 'errors' => [],
            'user' => ['username' => '', 'email' => '', 'role' => 'staff'], 'isSelf' => false, 'isNew' => true]);
    }

    public function store(): void
    {
        Csrf::verifyOrFail();
        [$errors, $d] = $this->validate(null, true);
        if (!$errors) {
            try {
                User::create($d['username'], $d['email'], password_hash($d['password'], PASSWORD_DEFAULT), $d['role']);
                flash('success', 'User created.');
                redirect('users');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') { throw $ex; }
                $errors['username'] = 'Username or email already exists.';
            }
        }
        render('users/form', ['title' => 'Add user', 'action' => url('user_create'), 'errors' => $errors,
            'user' => $d, 'isSelf' => false, 'isNew' => true]);
    }

    public function editForm(): void
    {
        $u = $this->findOrAbort();
        render('users/form', ['title' => 'Edit user', 'action' => url('user_edit', ['id' => $u['user_id']]),
            'errors' => [], 'user' => $u, 'isSelf' => $this->isSelf($u), 'isNew' => false]);
    }

    public function update(): void
    {
        Csrf::verifyOrFail();
        $u = $this->findOrAbort();
        [$errors, $d] = $this->validate($u, false);
        if (!$errors) {
            // Admins cannot change their own role (avoids locking everyone out)
            $role = $this->isSelf($u) ? $u['role'] : $d['role'];
            $hash = $d['password'] !== '' ? password_hash($d['password'], PASSWORD_DEFAULT) : null;
            try {
                User::update((int) $u['user_id'], $d['username'], $d['email'], $role, $hash);
                if ($this->isSelf($u)) { $_SESSION['user']['username'] = $d['username']; }
                flash('success', 'User updated.');
                redirect('users');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') { throw $ex; }
                $errors['username'] = 'Username or email already exists.';
            }
        }
        render('users/form', ['title' => 'Edit user', 'action' => url('user_edit', ['id' => $u['user_id']]),
            'errors' => $errors, 'user' => $d, 'isSelf' => $this->isSelf($u), 'isNew' => false]);
    }

    public function delete(): void
    {
        Csrf::verifyOrFail();
        $u = User::find((int) ($_POST['id'] ?? 0));
        if (!$u) { abort(404, 'User not found'); }
        if ($this->isSelf($u)) {
            flash('error', 'You cannot delete your own account.');
            redirect('users');
        }
        User::delete((int) $u['user_id']);
        flash('success', 'User deleted.');
        redirect('users');
    }

    private function isSelf(array $u): bool
    {
        return (int) $u['user_id'] === (int) Auth::user()['user_id'];
    }

    private function findOrAbort(): array
    {
        $u = User::find(intParam('id'));
        if (!$u) { abort(404, 'User not found'); }
        return $u;
    }

    private function validate(?array $existing, bool $isNew): array
    {
        $d = ['username' => input('username'), 'email' => input('email'),
              'role' => input('role'), 'password' => (string) ($_POST['password'] ?? '')];
        if ($existing && (int) $existing['user_id'] === (int) Auth::user()['user_id']) {
            $d['role'] = $existing['role']; // own role cannot be changed
        }
        $e = [];
        if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $d['username'])) {
            $e['username'] = 'Username must be 3-50 characters: letters, numbers, underscore.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || strlen($d['email']) > 120) {
            $e['email'] = 'Enter a valid email address.';
        }
        if (!in_array($d['role'], User::ROLES, true)) {
            $e['role'] = 'Choose a valid role.';
        }
        if (($isNew || $d['password'] !== '') && strlen($d['password']) < 8) {
            $e['password'] = 'Password must be at least 8 characters.';
        }
        $uid = $existing ? (int) $existing['user_id'] : 0;
        $byName = !isset($e['username']) ? User::findByUsername($d['username']) : null;
        if ($byName && (int) $byName['user_id'] !== $uid) { $e['username'] = 'That username is already taken.'; }
        $byMail = !isset($e['email']) ? User::findByEmail($d['email']) : null;
        if ($byMail && (int) $byMail['user_id'] !== $uid) { $e['email'] = 'That email is already registered.'; }
        return [$e, $d];
    }
}
