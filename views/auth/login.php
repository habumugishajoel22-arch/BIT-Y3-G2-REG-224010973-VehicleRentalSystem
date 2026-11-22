<section class="card narrow">
    <h1>Login</h1>
    <?php if (!empty($errors['form'])): ?>
        <div class="alert alert-error"><?= e($errors['form']) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= e(url('login')) ?>" novalidate>
        <?= Csrf::field() ?>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($old['username'] ?? '') ?>" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn">Login</button>
    </form>
    <p class="muted">No account? <a href="<?= e(url('register')) ?>">Register here</a></p>
</section>
