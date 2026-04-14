<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class CustomerNotificationRepository
{
    public function createOrderStatusNotification(int $userId, int $orderId, string $previousStatus, string $newStatus, string $orderNumber): void
    {
        if ($userId <= 0 || $orderId <= 0 || $previousStatus === $newStatus) {
            return;
        }

        $message = 'Order #' . $orderNumber . ' changed from ' . ucfirst($previousStatus) . ' to ' . ucfirst($newStatus) . '.';
        $statement = Database::connection()->prepare(
            'INSERT INTO customer_order_notifications (user_id, order_id, previous_status, new_status, message)
             VALUES (:user_id, :order_id, :previous_status, :new_status, :message)'
        );
        $statement->execute([
            'user_id' => $userId,
            'order_id' => $orderId,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'message' => $message,
        ]);
    }

    public function createReservationStatusNotification(int $userId, int $reservationId, string $previousStatus, string $newStatus): void
    {
        if ($userId <= 0 || $reservationId <= 0 || $previousStatus === $newStatus) {
            return;
        }

        $message = 'Reservation #' . $reservationId . ' changed from ' . ucfirst($previousStatus) . ' to ' . ucfirst($newStatus) . '.';
        $statement = Database::connection()->prepare(
            'INSERT INTO customer_reservation_notifications (user_id, reservation_id, previous_status, new_status, message)
             VALUES (:user_id, :reservation_id, :previous_status, :new_status, :message)'
        );
        $statement->execute([
            'user_id' => $userId,
            'reservation_id' => $reservationId,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'message' => $message,
        ]);
    }

    public function unreadForUser(int $userId, int $limit = 5): array
    {
        $limit = max(1, $limit);
        $orders = $this->unreadOrderNotifications($userId, $limit);
        $reservations = $this->unreadReservationNotifications($userId, $limit);
        $notifications = array_merge($orders, $reservations);

        usort($notifications, static function (array $left, array $right): int {
            $leftTime = strtotime((string) ($left['created_at'] ?? '')) ?: 0;
            $rightTime = strtotime((string) ($right['created_at'] ?? '')) ?: 0;

            return $rightTime <=> $leftTime;
        });

        return array_slice($notifications, 0, $limit);
    }

    public function markReadForUser(int $userId, array $notifications): void
    {
        $orderIds = [];
        $reservationIds = [];

        foreach ($notifications as $notification) {
            $id = (int) ($notification['id'] ?? 0);

            if ($id <= 0) {
                continue;
            }

            if (($notification['type'] ?? '') === 'reservation') {
                $reservationIds[] = $id;
            } else {
                $orderIds[] = $id;
            }
        }

        $this->markOrderNotificationsRead($userId, $orderIds);
        $this->markReservationNotificationsRead($userId, $reservationIds);
    }

    private function unreadOrderNotifications(int $userId, int $limit): array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, order_id, previous_status, new_status, message, created_at
             FROM customer_order_notifications
             WHERE user_id = :user_id AND is_read = 0
             ORDER BY created_at DESC, id DESC
             LIMIT :limit'
        );
        $statement->bindValue('user_id', $userId, \PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return array_map(static function (array $notification): array {
            $notification['type'] = 'order';

            return $notification;
        }, $statement->fetchAll() ?: []);
    }

    private function unreadReservationNotifications(int $userId, int $limit): array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, reservation_id, previous_status, new_status, message, created_at
             FROM customer_reservation_notifications
             WHERE user_id = :user_id AND is_read = 0
             ORDER BY created_at DESC, id DESC
             LIMIT :limit'
        );
        $statement->bindValue('user_id', $userId, \PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return array_map(static function (array $notification): array {
            $notification['type'] = 'reservation';
            $notification['order_id'] = null;

            return $notification;
        }, $statement->fetchAll() ?: []);
    }

    private function markOrderNotificationsRead(int $userId, array $ids): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if ($ids === []) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = Database::connection()->prepare(
            "UPDATE customer_order_notifications SET is_read = 1 WHERE user_id = ? AND id IN ({$placeholders})"
        );
        $statement->execute(array_merge([$userId], $ids));
    }

    private function markReservationNotificationsRead(int $userId, array $ids): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if ($ids === []) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = Database::connection()->prepare(
            "UPDATE customer_reservation_notifications SET is_read = 1 WHERE user_id = ? AND id IN ({$placeholders})"
        );
        $statement->execute(array_merge([$userId], $ids));
    }
}
