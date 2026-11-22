<?php
declare(strict_types=1);

// Escape output to prevent XSS. Use e() on EVERY value printed in a view.
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $page, array $params = []): string
{
    $query = http_build_query(array_merge(['page' => $page], $params));
    return BASE_URL . '/index.php?' . $query;
}

function redirect(string $page, array $params = []): never
{
    header('Location: ' . url($page, $params));
    exit;
}

// One-time messages (shown once, then removed)
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// Load a view inside the shared layout
function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../views/layout/header.php';
    require __DIR__ . '/../views/' . $view . '.php';
    require __DIR__ . '/../views/layout/footer.php';
}

// ---- Extra helpers (stage 3+) ----
function input(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

function intParam(string $key): int
{
    $v = filter_var($_GET[$key] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $v === false ? 0 : (int) $v;
}

function validDate(string $d): bool
{
    $dt = DateTime::createFromFormat('Y-m-d', $d);
    return $dt !== false && $dt->format('Y-m-d') === $d;
}

function money($n): string
{
    return number_format((float) $n, 2);
}

function likeTerm(string $q): string
{
    return '%' . addcslashes($q, '%_\\') . '%';
}

function abort(int $code, string $message): never
{
    http_response_code($code);
    exit($code . ' - ' . $message);
}
