<?php
declare(strict_types=1);

class User
{
    public const ROLES = ['admin', 'staff'];

    public static function all(): array
    {
        return Database::connect()
            ->query('SELECT user_id, username, email, role, created_at FROM users ORDER BY user_id')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM users WHERE user_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByUsername(string $username): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $username]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e' => $email]);
        return $stmt->fetch() ?: null;
    }

    public static function countAll(): int
    {
        return (int) Database::connect()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public static function create(string $username, string $email, string $hash, string $role): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO users (username, email, password_hash, role) VALUES (:u, :e, :p, :r)'
        );
        $stmt->execute([':u' => $username, ':e' => $email, ':p' => $hash, ':r' => $role]);
    }

    // $hash = null keeps the old password
    public static function update(int $id, string $username, string $email, string $role, ?string $hash): void
    {
        if ($hash !== null) {
            $stmt = Database::connect()->prepare(
                'UPDATE users SET username = :u, email = :e, role = :r, password_hash = :p WHERE user_id = :id'
            );
            $stmt->execute([':u' => $username, ':e' => $email, ':r' => $role, ':p' => $hash, ':id' => $id]);
        } else {
            $stmt = Database::connect()->prepare(
                'UPDATE users SET username = :u, email = :e, role = :r WHERE user_id = :id'
            );
            $stmt->execute([':u' => $username, ':e' => $email, ':r' => $role, ':id' => $id]);
        }
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM users WHERE user_id = :id');
        $stmt->execute([':id' => $id]);
    }
}
