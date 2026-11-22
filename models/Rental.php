<?php
declare(strict_types=1);

class Rental
{
    public const STATUSES = ['active', 'returned', 'cancelled'];

    // Number of days = difference between dates, minimum 1 day
    public static function calculateCost(float $dailyRate, string $start, string $end): float
    {
        $days = (new DateTime($start))->diff(new DateTime($end))->days;
        return round($dailyRate * max(1, (int) $days), 2);
    }

    public static function all(string $q = '', string $status = ''): array
    {
        $sql = 'SELECT r.*, v.plate_no, v.model, c.full_name
                FROM rentals r
                JOIN vehicles v  ON v.vehicle_id  = r.vehicle_id
                JOIN customers c ON c.customer_id = r.customer_id
                WHERE 1=1';
        $params = [];
        if ($q !== '') {
            $sql .= ' AND (v.plate_no LIKE :q1 OR v.model LIKE :q2 OR c.full_name LIKE :q3)';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = likeTerm($q);
        }
        if (in_array($status, self::STATUSES, true)) {
            $sql .= ' AND r.status = :s';
            $params[':s'] = $status;
        }
        $stmt = Database::connect()->prepare($sql . ' ORDER BY r.rental_id DESC');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare(
            'SELECT r.*, v.plate_no, v.model, v.daily_rate, c.full_name
             FROM rentals r
             JOIN vehicles v  ON v.vehicle_id  = r.vehicle_id
             JOIN customers c ON c.customer_id = r.customer_id
             WHERE r.rental_id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    // Create rental + mark vehicle as rented, all-or-nothing (transaction)
    // Returns an error message, or null on success.
    public static function create(int $vehicleId, int $customerId, string $start, string $end): ?string
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT daily_rate, status FROM vehicles WHERE vehicle_id = :id FOR UPDATE');
            $stmt->execute([':id' => $vehicleId]);
            $vehicle = $stmt->fetch();
            if (!$vehicle || $vehicle['status'] !== 'available') {
                $pdo->rollBack();
                return 'That vehicle is not available.';
            }
            $cost = self::calculateCost((float) $vehicle['daily_rate'], $start, $end);

            $pdo->prepare(
                "INSERT INTO rentals (vehicle_id, customer_id, start_date, end_date, total_cost, status)
                 VALUES (:v, :c, :s, :e, :t, 'active')"
            )->execute([':v' => $vehicleId, ':c' => $customerId, ':s' => $start, ':e' => $end, ':t' => $cost]);

            $pdo->prepare("UPDATE vehicles SET status = 'rented' WHERE vehicle_id = :id")
                ->execute([':id' => $vehicleId]);

            $pdo->commit();
            return null;
        } catch (Throwable $ex) {
            $pdo->rollBack();
            throw $ex;
        }
    }

    // Edit customer and dates of an ACTIVE rental; cost is recalculated
    public static function update(array $rental, int $customerId, string $start, string $end): void
    {
        $cost = self::calculateCost((float) $rental['daily_rate'], $start, $end);
        $stmt = Database::connect()->prepare(
            'UPDATE rentals SET customer_id = :c, start_date = :s, end_date = :e, total_cost = :t
             WHERE rental_id = :id'
        );
        $stmt->execute([':c' => $customerId, ':s' => $start, ':e' => $end, ':t' => $cost,
                        ':id' => $rental['rental_id']]);
    }

    // Close an active rental ('returned' or 'cancelled') and free the vehicle
    public static function close(array $rental, string $newStatus): void
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE rentals SET status = :s WHERE rental_id = :id')
                ->execute([':s' => $newStatus, ':id' => $rental['rental_id']]);
            $pdo->prepare("UPDATE vehicles SET status = 'available'
                           WHERE vehicle_id = :id AND status = 'rented'")
                ->execute([':id' => $rental['vehicle_id']]);
            $pdo->commit();
        } catch (Throwable $ex) {
            $pdo->rollBack();
            throw $ex;
        }
    }

    public static function delete(array $rental): void
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('DELETE FROM rentals WHERE rental_id = :id')->execute([':id' => $rental['rental_id']]);
            if ($rental['status'] === 'active') {
                $pdo->prepare("UPDATE vehicles SET status = 'available'
                               WHERE vehicle_id = :id AND status = 'rented'")
                    ->execute([':id' => $rental['vehicle_id']]);
            }
            $pdo->commit();
        } catch (Throwable $ex) {
            $pdo->rollBack();
            throw $ex;
        }
    }

    public static function countByStatus(): array
    {
        $rows = Database::connect()->query('SELECT status, COUNT(*) n FROM rentals GROUP BY status')->fetchAll();
        $out = ['active' => 0, 'returned' => 0, 'cancelled' => 0];
        foreach ($rows as $r) { $out[$r['status']] = (int) $r['n']; }
        return $out;
    }

    public static function recent(int $limit = 5): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT r.*, v.plate_no, v.model, c.full_name
             FROM rentals r
             JOIN vehicles v  ON v.vehicle_id  = r.vehicle_id
             JOIN customers c ON c.customer_id = r.customer_id
             ORDER BY r.rental_id DESC LIMIT :n'
        );
        $stmt->bindValue(':n', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
