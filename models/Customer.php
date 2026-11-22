<?php
declare(strict_types=1);

class Customer
{
    public static function all(string $q = ''): array
    {
        $sql = 'SELECT * FROM customers';
        $params = [];
        if ($q !== '') {
            $sql .= ' WHERE full_name LIKE :q1 OR license_no LIKE :q2 OR phone LIKE :q3 OR email LIKE :q4';
            $params = [':q1' => likeTerm($q), ':q2' => likeTerm($q), ':q3' => likeTerm($q), ':q4' => likeTerm($q)];
        }
        $stmt = Database::connect()->prepare($sql . ' ORDER BY customer_id DESC');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM customers WHERE customer_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $d): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO customers (full_name, license_no, phone, email) VALUES (:n, :l, :p, :e)'
        );
        $stmt->execute([':n' => $d['full_name'], ':l' => $d['license_no'], ':p' => $d['phone'], ':e' => $d['email']]);
    }

    public static function update(int $id, array $d): void
    {
        $stmt = Database::connect()->prepare(
            'UPDATE customers SET full_name = :n, license_no = :l, phone = :p, email = :e WHERE customer_id = :id'
        );
        $stmt->execute([':n' => $d['full_name'], ':l' => $d['license_no'], ':p' => $d['phone'],
                        ':e' => $d['email'], ':id' => $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM customers WHERE customer_id = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function count(): int
    {
        return (int) Database::connect()->query('SELECT COUNT(*) FROM customers')->fetchColumn();
    }
}
