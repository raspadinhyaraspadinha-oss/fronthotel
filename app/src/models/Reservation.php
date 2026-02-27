<?php

class Reservation
{
    public static function generateCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $db = getDatabase();

        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $stmt = $db->prepare("SELECT 1 FROM reservations WHERE code = ?");
            $stmt->execute([$code]);
        } while ($stmt->fetch());

        return $code;
    }

    public static function create(array $data): int
    {
        $db = getDatabase();
        $code = !empty($data['code']) ? strtoupper($data['code']) : self::generateCode();

        $stmt = $db->prepare("
            INSERT INTO reservations (code, confirmation_number, guest_name, checkin, checkout, rate_total, card_last4, email, phone, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");

        $stmt->execute([
            $code,
            $data['confirmation_number'] ?? null,
            $data['guest_name'],
            $data['checkin'],
            $data['checkout'],
            $data['rate_total'],
            $data['card_last4'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
        ]);

        return (int)$db->lastInsertId();
    }

    public static function findByCode(string $code): ?array
    {
        $db = getDatabase();
        $stmt = $db->prepare("SELECT * FROM reservations WHERE code = ?");
        $stmt->execute([strtoupper(trim($code))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByPixTransactionId(string $txId): ?array
    {
        $db = getDatabase();
        $stmt = $db->prepare("SELECT * FROM reservations WHERE pix_transaction_id = ?");
        $stmt->execute([$txId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findAll(string $status = '', string $search = ''): array
    {
        $db = getDatabase();
        $where = [];
        $params = [];

        if ($status !== '') {
            $where[] = "status = ?";
            $params[] = $status;
        }

        if ($search !== '') {
            $search = '%' . $search . '%';
            $where[] = "(guest_name LIKE ? OR code LIKE ? OR confirmation_number LIKE ?)";
            array_push($params, $search, $search, $search);
        }

        $sql = "SELECT * FROM reservations";
        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function updateByCode(string $code, array $data): bool
    {
        $db = getDatabase();
        $sets = [];
        $params = [];

        foreach ($data as $key => $value) {
            $sets[] = "$key = ?";
            $params[] = $value;
        }

        $sets[] = "updated_at = datetime('now')";
        $params[] = strtoupper(trim($code));

        $stmt = $db->prepare("UPDATE reservations SET " . implode(', ', $sets) . " WHERE code = ?");
        return $stmt->execute($params);
    }

    public static function updateByPixTransactionId(string $txId, array $data): bool
    {
        $db = getDatabase();
        $sets = [];
        $params = [];

        foreach ($data as $key => $value) {
            $sets[] = "$key = ?";
            $params[] = $value;
        }

        $sets[] = "updated_at = datetime('now')";
        $params[] = $txId;

        $stmt = $db->prepare("UPDATE reservations SET " . implode(', ', $sets) . " WHERE pix_transaction_id = ?");
        return $stmt->execute($params);
    }

    public static function getStats(): array
    {
        $db = getDatabase();

        $row = $db->query("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid,
                SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired,
                COALESCE(SUM(CASE WHEN status = 'pending' THEN rate_total ELSE 0 END), 0) as total_value,
                COALESCE(SUM(CASE WHEN status = 'paid' THEN paid_amount ELSE 0 END), 0) as recovered_value
            FROM reservations
        ")->fetch();

        return [
            'total'           => (int)$row['total'],
            'pending'         => (int)$row['pending'],
            'paid'            => (int)$row['paid'],
            'expired'         => (int)$row['expired'],
            'total_value'     => (float)$row['total_value'],
            'recovered_value' => (float)$row['recovered_value'],
        ];
    }

    public static function deleteAll(): bool
    {
        return getDatabase()->exec("DELETE FROM reservations") !== false;
    }

    public static function countPaidToday(): int
    {
        $stmt = getDatabase()->query("SELECT COUNT(*) FROM reservations WHERE status = 'paid' AND DATE(paid_at) = DATE('now')");
        return (int)$stmt->fetchColumn();
    }
}
