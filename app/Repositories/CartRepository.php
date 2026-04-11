<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class CartRepository
{
    public function itemsForUser(int $userId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, user_id, menu_item_id, item_name, item_price, size, quantity, created_at, updated_at
             FROM cart_items
             WHERE user_id = :user_id
             ORDER BY created_at ASC, id ASC'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function countForUser(int $userId): int
    {
        $statement = Database::connection()->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }

    public function totalsForUser(int $userId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT COALESCE(SUM(quantity), 0) AS item_count, COALESCE(SUM(item_price * quantity), 0) AS subtotal
             FROM cart_items
             WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);
        $totals = $statement->fetch();

        return [
            'item_count' => (int) ($totals['item_count'] ?? 0),
            'subtotal' => (float) ($totals['subtotal'] ?? 0),
        ];
    }

    public function addOrIncrement(int $userId, array $catalogSize, int $quantity): void
    {
        $existing = $this->findMatchingItem(
            $userId,
            (int) $catalogSize['menu_item_id'],
            (string) $catalogSize['size_label']
        );

        if ($existing !== null) {
            $statement = Database::connection()->prepare(
                'UPDATE cart_items
                 SET quantity = quantity + :quantity,
                     item_price = :item_price,
                     item_name = :item_name
                 WHERE id = :id AND user_id = :user_id'
            );
            $statement->execute([
                'quantity' => $quantity,
                'item_price' => $catalogSize['price'],
                'item_name' => $catalogSize['item_name'],
                'id' => $existing['id'],
                'user_id' => $userId,
            ]);

            return;
        }

        $statement = Database::connection()->prepare(
            'INSERT INTO cart_items (user_id, menu_item_id, item_name, item_price, size, quantity)
             VALUES (:user_id, :menu_item_id, :item_name, :item_price, :size, :quantity)'
        );
        $statement->execute([
            'user_id' => $userId,
            'menu_item_id' => $catalogSize['menu_item_id'],
            'item_name' => $catalogSize['item_name'],
            'item_price' => $catalogSize['price'],
            'size' => $catalogSize['size_label'],
            'quantity' => $quantity,
        ]);
    }

    public function updateQuantity(int $userId, int $cartItemId, int $quantity): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE cart_items
             SET quantity = :quantity
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'quantity' => $quantity,
            'id' => $cartItemId,
            'user_id' => $userId,
        ]);
    }

    public function remove(int $userId, int $cartItemId): void
    {
        $statement = Database::connection()->prepare('DELETE FROM cart_items WHERE id = :id AND user_id = :user_id');
        $statement->execute([
            'id' => $cartItemId,
            'user_id' => $userId,
        ]);
    }

    public function clearForUser(int $userId): void
    {
        $statement = Database::connection()->prepare('DELETE FROM cart_items WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);
    }

    private function findMatchingItem(int $userId, int $menuItemId, string $sizeLabel): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, quantity
             FROM cart_items
             WHERE user_id = :user_id
               AND menu_item_id = :menu_item_id
               AND size = :size
             LIMIT 1'
        );
        $statement->execute([
            'user_id' => $userId,
            'menu_item_id' => $menuItemId,
            'size' => $sizeLabel,
        ]);
        $item = $statement->fetch();

        return is_array($item) ? $item : null;
    }
}
