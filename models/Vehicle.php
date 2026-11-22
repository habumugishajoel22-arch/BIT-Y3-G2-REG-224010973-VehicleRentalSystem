<?php
declare(strict_types=1);

class Vehicle
{
    public const TYPES    = ['car', 'suv', 'van', 'bus', 'motorbike'];
    public const STATUSES = ['available', 'rented', 'maintenance'];

    public static function all(string $q = '', string $status = ''): array
    {
        $sql = 'SELECT * FROM vehicles WHERE 1=1';
        $params = [];
        if ($q !== '') {
            $sql .= ' AND (plate_no LIKE :q1 OR model LIKE :q2 OR type LIKE :q3)';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = likeTerm($q);
        }
        if (in_array($status, self::STATUSES, true)) {
            $sql .= ' AND status = :s';
            $params[':s'] = $status;
        }
        $stmt = Database::connect()->prepare($sql . ' ORDER BY vehicle_id DESC');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function available(): array
    {
        return Database::connect()
            ->query("SELECT * FROM vehicles WHERE status = 'available' ORDER BY model")
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM vehicles WHERE vehicle_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $d): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO vehicles (plate_no, model, type, daily_rate, status)
             VALUES (:plate, :model, :type, :rate, :status)'
        );
        $stmt->execute([':plate' => $d['plate_no'], ':model' => $d['model'], ':type' => $d['type'],
                        ':rate' => $d['daily_rate'], ':status' => $d['status']]);
    }

    public static function update(int $id, array $d): void
    {
        $stmt = Database::connect()->prepare(
            'UPDATE vehicles SET plate_no = :plate, model = :model, type = :type,
                    daily_rate = :rate, status = :status WHERE vehicle_id = :id'
        );
        $stmt->execute([':plate' => $d['plate_no'], ':model' => $d['model'], ':type' => $d['type'],
                        ':rate' => $d['daily_rate'], ':status' => $d['status'], ':id' => $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM vehicles WHERE vehicle_id = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function countByStatus(): array
    {
        $rows = Database::connect()->query('SELECT status, COUNT(*) n FROM vehicles GROUP BY status')->fetchAll();
        $out = ['available' => 0, 'rented' => 0, 'maintenance' => 0];
        foreach ($rows as $r) { $out[$r['status']] = (int) $r['n']; }
        return $out;
    }
}
