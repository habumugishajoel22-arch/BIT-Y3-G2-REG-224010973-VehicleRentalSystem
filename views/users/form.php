<section class="card narrow">
    <h1><?= e($title) ?></h1>
    <form method="post" action="<?= e($action) ?>" novalidate>
        <?= Csrf::field() ?>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($user['username']) ?>" required>
        <small class="error"><?= e($errors['username'] ?? '') ?></small>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($user['email']) ?>" required>
        <small class="error"><?= e($errors['email'] ?? '') ?></small>

        <label for="role">Role</label>
        <select id="role" name="role" <?= $isSelf ? 'disabled' : '' ?>>
            <?php foreach (User::ROLES as $r): ?>
                <option value="<?= e($r) ?>" <?= $user['role'] === $r ? 'selected' : '' ?>><?= e(ucfirst($r)) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($isSelf): ?><small class="muted">You cannot change your own role.</small><?php endif; ?>
        <small class="error"><?= e($errors['role'] ?? '') ?></small>

        <label for="password"><?= $isNew ? 'Password (min 8 characters)' : 'New password (leave empty to keep the current one)' ?></label>
        <input type="password" id="password" name="password" <?= $isNew ? 'required' : '' ?> autocomplete="new-password">
        <small class="error"><?= e($errors['password'] ?? '') ?></small>

        <button type="submit" class="btn">Save</button>
        <a class="btn btn-light" href="<?= e(url('users')) ?>">Cancel</a>
    </form>
</section>
