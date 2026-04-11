<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;
use DateTimeImmutable;
use RuntimeException;

final class OrderRepository
{
    private const MANAGEABLE_ORDER_STATUSES = [
        'pending',
        'preparing',
        'ready',
        'completed',
        'cancelled',
    ];

    public function createFromCart(int $userId, array $cartItems, ?int $reservationId = null, ?string $receiptFilename = null): array
    {
        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $totalAmount = 0.0;

            foreach ($cartItems as $item) {
                $totalAmount += (float) $item['item_price'] * (int) $item['quantity'];
            }

            $orderNumber = $this->generateOrderNumber();
            $orderStatement = $connection->prepare(
                'INSERT INTO orders (user_id, reservation_id, order_number, total_amount, status, payment_status, receipt_image)
                 VALUES (:user_id, :reservation_id, :order_number, :total_amount, :status, :payment_status, :receipt_image)'
            );
            $orderStatement->execute([
                'user_id' => $userId,
                'reservation_id' => $reservationId,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'receipt_image' => $receiptFilename,
            ]);

            $orderId = (int) $connection->lastInsertId();

            $itemStatement = $connection->prepare(
                'INSERT INTO order_items (order_id, menu_item_id, quantity, price, subtotal, size)
                 VALUES (:order_id, :menu_item_id, :quantity, :price, :subtotal, :size)'
            );

            foreach ($cartItems as $item) {
                $quantity = (int) $item['quantity'];
                $price = (float) $item['item_price'];

                $itemStatement->execute([
                    'order_id' => $orderId,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $price * $quantity,
                    'size' => $item['size'],
                ]);
            }

            $clearCartStatement = $connection->prepare('DELETE FROM cart_items WHERE user_id = :user_id');
            $clearCartStatement->execute(['user_id' => $userId]);

            $connection->commit();

            return [
                'id' => $orderId,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'receipt_image' => $receiptFilename,
            ];
        } catch (\Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    private function generateOrderNumber(): string
    {
        return 'GR-' . (new DateTimeImmutable())->format('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }

    public function getPaymentReviewStats(): array
    {
        $statement = Database::connection()->query(
            "SELECT
                COUNT(*) AS total_orders,
                SUM(CASE WHEN payment_status = 'pending' AND receipt_image IS NOT NULL AND receipt_image <> '' THEN 1 ELSE 0 END) AS pending_review,
                SUM(CASE WHEN payment_status = 'verified' THEN 1 ELSE 0 END) AS verified_payments,
                SUM(CASE WHEN payment_status = 'rejected' THEN 1 ELSE 0 END) AS rejected_payments,
                SUM(CASE WHEN reservation_id IS NOT NULL THEN 1 ELSE 0 END) AS reservation_orders
             FROM orders"
        );

        $row = $statement->fetch() ?: [];

        return [
            'total_orders' => (int) ($row['total_orders'] ?? 0),
            'pending_review' => (int) ($row['pending_review'] ?? 0),
            'verified_payments' => (int) ($row['verified_payments'] ?? 0),
            'rejected_payments' => (int) ($row['rejected_payments'] ?? 0),
            'reservation_orders' => (int) ($row['reservation_orders'] ?? 0),
        ];
    }

    public function getPendingPaymentReviewOrders(int $limit = 12): array
    {
        return $this->attachOrderItems($this->fetchPaymentReviewOrders(
            "o.payment_status = 'pending' AND o.receipt_image IS NOT NULL AND o.receipt_image <> ''",
            $limit
        ));
    }

    public function getReviewedPaymentOrders(int $limit = 12): array
    {
        return $this->attachOrderItems($this->fetchPaymentReviewOrders(
            "o.payment_status IN ('verified', 'rejected')",
            $limit
        ));
    }

    public function getCustomerOrderStats(int $userId): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                COUNT(*) AS total_orders,
                SUM(CASE WHEN status IN ('pending', 'preparing', 'ready') THEN 1 ELSE 0 END) AS active_orders,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) AS awaiting_payment_review
             FROM orders
             WHERE user_id = :user_id"
        );
        $statement->execute(['user_id' => $userId]);
        $row = $statement->fetch() ?: [];

        return [
            'total_orders' => (int) ($row['total_orders'] ?? 0),
            'active_orders' => (int) ($row['active_orders'] ?? 0),
            'completed_orders' => (int) ($row['completed_orders'] ?? 0),
            'awaiting_payment_review' => (int) ($row['awaiting_payment_review'] ?? 0),
        ];
    }

    public function getCustomerOrders(int $userId, int $limit = 8): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                o.id,
                o.order_number,
                o.total_amount,
                o.status,
                o.payment_status,
                o.receipt_image,
                o.created_at,
                o.reservation_id,
                COUNT(oi.id) AS item_count
             FROM orders o
             LEFT JOIN order_items oi ON oi.order_id = o.id
             WHERE o.user_id = :user_id
             GROUP BY o.id, o.order_number, o.total_amount, o.status, o.payment_status, o.receipt_image, o.created_at, o.reservation_id
             ORDER BY o.created_at DESC
             LIMIT :limit"
        );
        $statement->bindValue('user_id', $userId, \PDO::PARAM_INT);
        $statement->bindValue('limit', max(1, $limit), \PDO::PARAM_INT);
        $statement->execute();

        $orders = $statement->fetchAll();

        return $this->attachOrderItems(is_array($orders) ? $orders : []);
    }

    public function getFulfillmentStats(): array
    {
        $statement = Database::connection()->query(
            "SELECT
                COUNT(*) AS total_verified_orders,
                SUM(CASE WHEN payment_status = 'verified' AND status IN ('pending', 'preparing', 'ready') THEN 1 ELSE 0 END) AS active_fulfillment,
                SUM(CASE WHEN payment_status = 'verified' AND status = 'ready' THEN 1 ELSE 0 END) AS ready_orders,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders
             FROM orders"
        );
        $row = $statement->fetch() ?: [];

        return [
            'total_verified_orders' => (int) ($row['total_verified_orders'] ?? 0),
            'active_fulfillment' => (int) ($row['active_fulfillment'] ?? 0),
            'ready_orders' => (int) ($row['ready_orders'] ?? 0),
            'completed_orders' => (int) ($row['completed_orders'] ?? 0),
            'cancelled_orders' => (int) ($row['cancelled_orders'] ?? 0),
        ];
    }

    public function getFulfillmentOrders(int $limit = 12): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                o.id,
                o.order_number,
                o.total_amount,
                o.status,
                o.payment_status,
                o.receipt_image,
                o.created_at,
                o.reservation_id,
                u.first_name,
                u.last_name,
                u.email,
                r.date AS reservation_date,
                r.time AS reservation_time,
                COUNT(oi.id) AS item_count
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             LEFT JOIN reservations r ON r.id = o.reservation_id
             LEFT JOIN order_items oi ON oi.order_id = o.id
             WHERE o.payment_status = 'verified'
             GROUP BY
                o.id, o.order_number, o.total_amount, o.status, o.payment_status, o.receipt_image, o.created_at,
                o.reservation_id, u.first_name, u.last_name, u.email, r.date, r.time
             ORDER BY
                FIELD(o.status, 'pending', 'preparing', 'ready', 'completed', 'cancelled', 'rejected'),
                o.created_at DESC
             LIMIT :limit"
        );
        $statement->bindValue('limit', max(1, $limit), \PDO::PARAM_INT);
        $statement->execute();

        $orders = $statement->fetchAll();

        return $this->attachOrderItems(is_array($orders) ? $orders : []);
    }

    public function reviewPayment(int $orderId, string $action): array
    {
        if (!in_array($action, ['verify', 'reject'], true)) {
            throw new RuntimeException('Invalid payment action.');
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $orderStatement = $connection->prepare(
                'SELECT id, order_number, reservation_id, payment_status, receipt_image
                 FROM orders
                 WHERE id = :id
                 LIMIT 1'
            );
            $orderStatement->execute(['id' => $orderId]);
            $order = $orderStatement->fetch();

            if (!is_array($order)) {
                throw new RuntimeException('Order not found.');
            }

            if (($order['payment_status'] ?? 'pending') !== 'pending') {
                throw new RuntimeException('This payment has already been reviewed.');
            }

            if (!is_string($order['receipt_image'] ?? null) || trim((string) $order['receipt_image']) === '') {
                throw new RuntimeException('This order has no uploaded receipt to review.');
            }

            if ($action === 'reject') {
                $updateOrderStatement = $connection->prepare(
                    "UPDATE orders
                     SET payment_status = 'rejected', status = 'cancelled'
                     WHERE id = :id"
                );
                $updateOrderStatement->execute(['id' => $orderId]);

                $reservationId = (int) ($order['reservation_id'] ?? 0);

                if ($reservationId > 0) {
                    $reservationStatement = $connection->prepare(
                        "UPDATE reservations
                         SET status = 'cancelled'
                         WHERE id = :id"
                    );
                    $reservationStatement->execute(['id' => $reservationId]);
                }
            } else {
                $updateOrderStatement = $connection->prepare(
                    "UPDATE orders
                     SET payment_status = 'verified'
                     WHERE id = :id"
                );
                $updateOrderStatement->execute(['id' => $orderId]);
            }

            $connection->commit();

            return [
                'id' => (int) $order['id'],
                'order_number' => (string) ($order['order_number'] ?? ''),
                'payment_status' => $action === 'verify' ? 'verified' : 'rejected',
            ];
        } catch (\Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    public function updateStatus(int $orderId, string $status): array
    {
        if (!in_array($status, self::MANAGEABLE_ORDER_STATUSES, true)) {
            throw new RuntimeException('Invalid order status.');
        }

        $connection = Database::connection();
        $statement = $connection->prepare(
            'SELECT id, order_number, status, payment_status
             FROM orders
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $orderId]);
        $order = $statement->fetch();

        if (!is_array($order)) {
            throw new RuntimeException('Order not found.');
        }

        $currentStatus = (string) ($order['status'] ?? 'pending');
        $paymentStatus = (string) ($order['payment_status'] ?? 'pending');

        if ($currentStatus === $status) {
            return [
                'id' => (int) $order['id'],
                'order_number' => (string) ($order['order_number'] ?? ''),
                'status' => $currentStatus,
            ];
        }

        if (in_array($currentStatus, ['completed', 'cancelled', 'rejected'], true)) {
            throw new RuntimeException('Finalized orders cannot be updated anymore.');
        }

        if ($paymentStatus !== 'verified' && !in_array($status, ['pending', 'cancelled'], true)) {
            throw new RuntimeException('Verify payment before moving this order into fulfillment.');
        }

        $updateStatement = $connection->prepare(
            'UPDATE orders
             SET status = :status
             WHERE id = :id'
        );
        $updateStatement->execute([
            'status' => $status,
            'id' => $orderId,
        ]);

        return [
            'id' => (int) $order['id'],
            'order_number' => (string) ($order['order_number'] ?? ''),
            'status' => $status,
        ];
    }

    public function cancelPendingDirectOrderForCustomer(int $orderId, int $userId): array
    {
        $connection = Database::connection();
        $statement = $connection->prepare(
            'SELECT id, order_number, status, payment_status, reservation_id
             FROM orders
             WHERE id = :id AND user_id = :user_id
             LIMIT 1'
        );
        $statement->execute([
            'id' => $orderId,
            'user_id' => $userId,
        ]);
        $order = $statement->fetch();

        if (!is_array($order)) {
            throw new RuntimeException('Order not found.');
        }

        if ((int) ($order['reservation_id'] ?? 0) > 0) {
            throw new RuntimeException('Reservation-linked orders must be managed from the reservation record.');
        }

        if (($order['status'] ?? 'pending') !== 'pending') {
            throw new RuntimeException('Only pending direct orders can be cancelled.');
        }

        $update = $connection->prepare(
            "UPDATE orders
             SET status = 'cancelled'
             WHERE id = :id AND user_id = :user_id AND status = 'pending' AND reservation_id IS NULL"
        );
        $update->execute([
            'id' => $orderId,
            'user_id' => $userId,
        ]);

        if ($update->rowCount() !== 1) {
            throw new RuntimeException('This order can no longer be cancelled.');
        }

        return [
            'id' => (int) $order['id'],
            'order_number' => (string) ($order['order_number'] ?? ''),
            'previous_status' => (string) ($order['status'] ?? 'pending'),
            'status' => 'cancelled',
            'payment_status' => (string) ($order['payment_status'] ?? 'pending'),
        ];
    }

    private function fetchPaymentReviewOrders(string $whereClause, int $limit): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                o.id,
                o.order_number,
                o.total_amount,
                o.status,
                o.payment_status,
                o.receipt_image,
                o.created_at,
                o.reservation_id,
                u.first_name,
                u.last_name,
                u.email,
                r.date AS reservation_date,
                r.time AS reservation_time,
                r.status AS reservation_status,
                COUNT(oi.id) AS item_count
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             LEFT JOIN reservations r ON r.id = o.reservation_id
             LEFT JOIN order_items oi ON oi.order_id = o.id
             WHERE {$whereClause}
             GROUP BY
                o.id, o.order_number, o.total_amount, o.status, o.payment_status, o.receipt_image, o.created_at,
                o.reservation_id, u.first_name, u.last_name, u.email, r.date, r.time, r.status
             ORDER BY o.created_at DESC
             LIMIT :limit"
        );
        $statement->bindValue('limit', max(1, $limit), \PDO::PARAM_INT);
        $statement->execute();

        $orders = $statement->fetchAll();

        return is_array($orders) ? $orders : [];
    }

    private function attachOrderItems(array $orders): array
    {
        if ($orders === []) {
            return [];
        }

        $orderIds = array_values(array_filter(array_map(
            static fn (array $order): int => (int) ($order['id'] ?? 0),
            $orders
        )));

        if ($orderIds === []) {
            return $orders;
        }

        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $statement = Database::connection()->prepare(
            "SELECT
                oi.order_id,
                oi.quantity,
                oi.size,
                oi.subtotal,
                mi.name AS menu_item_name
             FROM order_items oi
             INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
             WHERE oi.order_id IN ({$placeholders})
             ORDER BY oi.order_id ASC, oi.id ASC"
        );
        $statement->execute($orderIds);

        $itemsByOrder = [];

        foreach ($statement->fetchAll() ?: [] as $item) {
            $orderId = (int) ($item['order_id'] ?? 0);
            $itemsByOrder[$orderId][] = $item;
        }

        foreach ($orders as &$order) {
            $order['items'] = $itemsByOrder[(int) ($order['id'] ?? 0)] ?? [];
        }
        unset($order);

        return $orders;
    }
}
