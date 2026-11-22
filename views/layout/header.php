<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
    <title><?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= e(url('dashboard')) ?>"><?= e(APP_NAME) ?></a>
    <nav>
        <?php if (Auth::check()): ?>
            <a href="<?= e(url('dashboard')) ?>">Dashboard</a>
            <a href="<?= e(url('vehicles')) ?>">Vehicles</a>
            <a href="<?= e(url('customers')) ?>">Customers</a>
            <a href="<?= e(url('rentals')) ?>">Rentals</a>
            <?php if (Auth::user()['role'] === 'admin'): ?>
                <a href="<?= e(url('users')) ?>">Users</a>
            <?php endif; ?>
            <span class="who"><?= e(Auth::user()['username']) ?> (<?= e(Auth::user()['role']) ?>)</span>
            <form method="post" action="<?= e(url('logout')) ?>" class="inline">
                <?= Csrf::field() ?>
                <button type="submit" class="link-btn">Logout</button>
            </form>
        <?php else: ?>
            <a href="<?= e(url('login')) ?>">Login</a>
            <a href="<?= e(url('register')) ?>">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
<?php if ($flash = getFlash()): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
