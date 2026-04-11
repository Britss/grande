<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class ReservationRepository
{
    private const MANAGEABLE_RESERVATION_STATUSES = [
        'pending',
        'confirmed',
        'completed',
        'cancelled',
    ];

    public function create(int $userId, array $reservation): array
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO reservations (user_id, first_name, last_name, email, phone, date, time, guests, status)
             VALUES (:user_id, :first_name, :last_name, :email, :phone, :date, :time, :guests, :status)'
        );
        $statement->execute([
            'user_id' => $userId,
            'first_name' => $reservation['first_name'],
            'last_name' => $reservation['last_name'],
            'email' => $reservation['email'],
            'phone' => $reservation['phone'],
            'date' => $reservation['date'],
            'time' => $reservation['time'],
            'guests' => $reservation['guests'],
            'status' => 'pending',
        ]);

        return [
            'id' => (int) Database::connection()->lastInsertId(),
            'first_name' => $reservation['first_name'],
            'last_name' => $reservation['last_name'],
            'email' => $reservation['email'],
            'phone' => $reservation['phone'],
            'date' => $reservation['date'],
            'time' => $reservation['time'],
            'guests' => $reservation['guests'],
        ];
    }

    public function getCustomerReservationStats(int $userId): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                COUNT(*) AS total_reservations,
                SUM(CASE WHEN status IN ('pending', 'confirmed') THEN 1 ELSE 0 END) AS upcoming_reservations,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_reservations,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_reservations
             FROM reservations
             WHERE user_id = :user_id"
        );
        $statement->execute(['user_id' => $userId]);
        $row = $statement->fetch() ?: [];

        return [
            'total_reservations' => (int) ($row['total_reservations'] ?? 0),
            'upcoming_reservations' => (int) ($row['upcoming_reservations'] ?? 0),
            'pending_reservations' => (int) ($row['pending_reservations'] ?? 0),
            'completed_reservations' => (int) ($row['completed_reservations'] ?? 0),
        ];
    }

    public function getCustomerReservations(int $userId, int $limit = 6): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                r.id,
                r.first_name,
                r.last_name,
                r.email,
                r.phone,
                r.date,
                r.time,
                r.guests,
                r.status,
                r.created_at,
                COUNT(o.id) AS order_count
             FROM reservations r
             LEFT JOIN orders o ON o.reservation_id = r.id
             WHERE r.user_id = :user_id
             GROUP BY r.id, r.first_name, r.last_name, r.email, r.phone, r.date, r.time, r.guests, r.status, r.created_at
             ORDER BY r.date DESC, r.time DESC
             LIMIT :limit"
        );
        $statement->bindValue('user_id', $userId, \PDO::PARAM_INT);
        $statement->bindValue('limit', max(1, $limit), \PDO::PARAM_INT);
        $statement->execute();

        $reservations = $statement->fetchAll();

        return $this->attachReservationOrders(is_array($reservations) ? $reservations : []);
    }

    public function getManagementStats(): array
    {
        $statement = Database::connection()->query(
            "SELECT
                COUNT(*) AS total_reservations,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_reservations,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_reservations,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_reservations,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_reservations
             FROM reservations"
        );
        $row = $statement->fetch() ?: [];

        return [
            'total_reservations' => (int) ($row['total_reservations'] ?? 0),
            'pending_reservations' => (int) ($row['pending_reservations'] ?? 0),
            'confirmed_reservations' => (int) ($row['confirmed_reservations'] ?? 0),
            'completed_reservations' => (int) ($row['completed_reservations'] ?? 0),
            'cancelled_reservations' => (int) ($row['cancelled_reservations'] ?? 0),
        ];
    }

    public function getManageableReservations(int $limit = 12): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                r.id,
                r.first_name,
                r.last_name,
                r.email,
                r.phone,
                r.date,
                r.time,
                r.guests,
                r.status,
                r.created_at,
                COUNT(o.id) AS order_count,
                SUM(CASE WHEN o.payment_status = 'verified' THEN 1 ELSE 0 END) AS verified_order_count
             FROM reservations r
             LEFT JOIN orders o ON o.reservation_id = r.id
             GROUP BY
                r.id, r.first_name, r.last_name, r.email, r.phone, r.date, r.time, r.guests, r.status, r.created_at
             ORDER BY
                FIELD(r.status, 'pending', 'confirmed', 'completed', 'cancelled'),
                r.date ASC,
                r.time ASC
             LIMIT :limit"
        );
        $statement->bindValue('limit', max(1, $limit), \PDO::PARAM_INT);
        $statement->execute();

        $reservations = $statement->fetchAll();

        return $this->attachReservationOrders(is_array($reservations) ? $reservations : []);
    }

    public function updateStatus(int $reservationId, string $status): array
    {
        if (!in_array($status, self::MANAGEABLE_RESERVATION_STATUSES, true)) {
            throw new \RuntimeException('Invalid reservation status.');
        }

        $connection = Database::connection();
        $statement = $connection->prepare(
            'SELECT id, status
             FROM reservations
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $reservationId]);
        $reservation = $statement->fetch();

        if (!is_array($reservation)) {
            throw new \RuntimeException('Reservation not found.');
        }

        $currentStatus = (string) ($reservation['status'] ?? 'pending');

        if ($currentStatus === $status) {
            return [
                'id' => (int) $reservation['id'],
                'status' => $currentStatus,
            ];
        }

        if (in_array($currentStatus, ['completed', 'cancelled'], true)) {
            throw new \RuntimeException('Finalized reservations cannot be updated anymore.');
        }

        $paymentCheck = $connection->prepare(
            "SELECT
                COUNT(*) AS order_count,
                SUM(CASE WHEN payment_status = 'verified' THEN 1 ELSE 0 END) AS verified_count
             FROM orders
             WHERE reservation_id = :reservation_id"
        );
        $paymentCheck->execute(['reservation_id' => $reservationId]);
        $counts = $paymentCheck->fetch() ?: [];
        $orderCount = (int) ($counts['order_count'] ?? 0);
        $verifiedCount = (int) ($counts['verified_count'] ?? 0);

        if ($orderCount === 0 || $verifiedCount !== $orderCount) {
            throw new \RuntimeException('Reservation status cannot be updated until payment is verified.');
        }

        $update = $connection->prepare(
            'UPDATE reservations
             SET status = :status
             WHERE id = :id'
        );
        $update->execute([
            'status' => $status,
            'id' => $reservationId,
        ]);

        return [
            'id' => (int) $reservation['id'],
            'status' => $status,
        ];
    }

    private function attachReservationOrders(array $reservations): array
    {
        if ($reservations === []) {
            return [];
        }

        $reservationIds = array_values(array_filter(array_map(
            static fn (array $reservation): int => (int) ($reservation['id'] ?? 0),
            $reservations
        )));

        if ($reservationIds === []) {
            return $reservations;
        }

        $placeholders = implode(',', array_fill(0, count($reservationIds), '?'));
        $ordersStatement = Database::connection()->prepare(
            "SELECT
                o.id,
                o.reservation_id,
                o.order_number,
                o.total_amount,
                o.status,
                o.payment_status,
                o.created_at,
                COUNT(oi.id) AS item_count
             FROM orders o
             LEFT JOIN order_items oi ON oi.order_id = o.id
             WHERE o.reservation_id IN ({$placeholders})
             GROUP BY o.id, o.reservation_id, o.order_number, o.total_amount, o.status, o.payment_status, o.created_at
             ORDER BY o.created_at DESC"
        );
        $ordersStatement->execute($reservationIds);
        $orders = $ordersStatement->fetchAll();

        if (!is_array($orders) || $orders === []) {
            foreach ($reservations as &$reservation) {
                $reservation['orders'] = [];
            }
            unset($reservation);

            return $reservations;
        }

        $orderIds = array_values(array_filter(array_map(
            static fn (array $order): int => (int) ($order['id'] ?? 0),
            $orders
        )));
        $orderPlaceholders = implode(',', array_fill(0, count($orderIds), '?'));
        $itemsStatement = Database::connection()->prepare(
            "SELECT
                oi.order_id,
                oi.quantity,
                oi.size,
                oi.subtotal,
                mi.name AS menu_item_name
             FROM order_items oi
             INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
             WHERE oi.order_id IN ({$orderPlaceholders})
             ORDER BY oi.order_id ASC, oi.id ASC"
        );
        $itemsStatement->execute($orderIds);

        $itemsByOrder = [];
        foreach ($itemsStatement->fetchAll() ?: [] as $item) {
            $itemsByOrder[(int) ($item['order_id'] ?? 0)][] = $item;
        }

        $ordersByReservation = [];
        foreach ($orders as $order) {
            $order['items'] = $itemsByOrder[(int) ($order['id'] ?? 0)] ?? [];
            $ordersByReservation[(int) ($order['reservation_id'] ?? 0)][] = $order;
        }

        foreach ($reservations as &$reservation) {
            $reservation['orders'] = $ordersByReservation[(int) ($reservation['id'] ?? 0)] ?? [];
        }
        unset($reservation);

        return $reservations;
    }
}
