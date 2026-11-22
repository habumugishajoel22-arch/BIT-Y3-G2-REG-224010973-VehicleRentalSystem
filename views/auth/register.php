<section class="card narrow">
    <h1>Create account</h1>
    <form method="post" action="<?= e(url('register')) ?>" id="register-form" novalidate>
        <?= Csrf::field() ?>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($old['username'] ?? '') ?>" required>
        <small class="error" data-error-for="username"><?= e($errors['username'] ?? '') ?></small>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
        <small class="error" data-error-for="email"><?= e($errors['email'] ?? '') ?></small>

        <label for="password">Password (min 8 characters)</label>
        <input type="password" id="password" name="password" required>
        <small class="error" data-error-for="password"><?= e($errors['password'] ?? '') ?></small>

        <label for="confirm_password">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <small class="error" data-error-for="confirm_password"><?= e($errors['confirm_password'] ?? '') ?></small>

        <button type="submit" class="btn">Register</button>
    </form>
    <p class="muted">Already registered? <a href="<?= e(url('login')) ?>">Login</a></p>
</section>
