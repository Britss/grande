<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class MenuRepository
{
    public function managementCatalog(): array
    {
        $statement = Database::connection()->query(
            'SELECT
                mi.id,
                mi.name,
                mi.category,
                mi.description,
                mi.image_url,
                mi.is_available,
                mis.id AS size_id,
                mis.size_label,
                mis.price,
                mis.is_default,
                mis.sort_order,
                mis.is_available AS size_is_available
             FROM menu_items mi
             LEFT JOIN menu_item_sizes mis ON mis.menu_item_id = mi.id
             ORDER BY
                FIELD(mi.category, \'COFFEE.\', \'NON COFFEE.\', \'TEA BASED.\', \'QUENCHERS.\', \'PANDESAL.\', \'ENSAYMADA.\', \'LOAF.\', \'SANDWICHES.\'),
                mi.name ASC,
                mis.sort_order ASC,
                mis.id ASC'
        );

        return $this->groupManagementRows($statement->fetchAll());
    }

    public function groupedCatalog(): array
    {
        $statement = Database::connection()->query(
            'SELECT
                mi.id,
                mi.name,
                mi.category,
                mi.description,
                mi.image_url,
                mi.is_available,
                mis.id AS size_id,
                mis.size_label,
                mis.price,
                mis.is_default,
                mis.sort_order,
                mis.is_available AS size_is_available
             FROM menu_items mi
             LEFT JOIN menu_item_sizes mis ON mis.menu_item_id = mi.id
             WHERE mi.is_available = 1
             ORDER BY
                FIELD(mi.category, \'COFFEE.\', \'NON COFFEE.\', \'TEA BASED.\', \'QUENCHERS.\', \'PANDESAL.\', \'ENSAYMADA.\', \'LOAF.\', \'SANDWICHES.\'),
                mi.name ASC,
                mis.sort_order ASC,
                mis.id ASC'
        );

        $rows = $statement->fetchAll();

        return $this->groupRowsByCategory($rows);
    }

    public function activeItemsForJson(): array
    {
        $categories = $this->groupedCatalog();
        $items = [];

        foreach ($categories as $category) {
            foreach ($category['items'] as $item) {
                $items[] = [
                    'id' => (int) ($item['id'] ?? 0),
                    'name' => (string) ($item['name'] ?? ''),
                    'category' => (string) ($item['category'] ?? $category['key'] ?? ''),
                    'category_name' => (string) ($category['name'] ?? ''),
                    'description' => (string) ($item['description'] ?? ''),
                    'image_url' => (string) ($item['image_url'] ?? ''),
                    'sizes' => array_values($item['sizes'] ?? []),
                ];
            }
        }

        return $items;
    }

    public function createItem(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO menu_items (name, category, description, image_url, is_available)
             VALUES (:name, :category, :description, :image_url, :is_available)'
        );
        $statement->execute([
            'name' => $data['name'],
            'category' => $data['category'],
            'description' => $data['description'],
            'image_url' => $data['image_url'],
            'is_available' => $data['is_available'],
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function updateItem(int $itemId, array $data): array
    {
        $statement = Database::connection()->prepare(
            'UPDATE menu_items
             SET
                name = :name,
                category = :category,
                description = :description,
                image_url = :image_url,
                is_available = :is_available
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute([
            'name' => $data['name'],
            'category' => $data['category'],
            'description' => $data['description'],
            'image_url' => $data['image_url'],
            'is_available' => $data['is_available'],
            'id' => $itemId,
        ]);

        $item = $this->findItemById($itemId);

        if ($item === null) {
            throw new \RuntimeException('Menu item not found.');
        }

        return $item;
    }

    public function createSize(int $itemId, array $data): int
    {
        if ((int) ($data['is_default'] ?? 0) === 1) {
            $this->clearDefaultSizesForItem($itemId);
        }

        $statement = Database::connection()->prepare(
            'INSERT INTO menu_item_sizes (menu_item_id, size_label, price, is_default, sort_order, is_available)
             VALUES (:menu_item_id, :size_label, :price, :is_default, :sort_order, :is_available)'
        );
        $statement->execute([
            'menu_item_id' => $itemId,
            'size_label' => $data['size_label'],
            'price' => $data['price'],
            'is_default' => $data['is_default'],
            'sort_order' => $data['sort_order'],
            'is_available' => $data['is_available'],
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function updateSize(int $sizeId, array $data): array
    {
        $existingSize = $this->findSizeById($sizeId);

        if ($existingSize === null) {
            throw new \RuntimeException('Menu size not found.');
        }

        if ((int) ($data['is_default'] ?? 0) === 1) {
            $this->clearDefaultSizesForItem((int) ($existingSize['menu_item_id'] ?? 0));
        }

        $statement = Database::connection()->prepare(
            'UPDATE menu_item_sizes
             SET
                size_label = :size_label,
                price = :price,
                is_default = :is_default,
                sort_order = :sort_order,
                is_available = :is_available
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute([
            'size_label' => $data['size_label'],
            'price' => $data['price'],
            'is_default' => $data['is_default'],
            'sort_order' => $data['sort_order'],
            'is_available' => $data['is_available'],
            'id' => $sizeId,
        ]);

        $size = $this->findSizeById($sizeId);

        return $size;
    }

    public function findAvailableSizeById(int $sizeId): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT
                mi.id AS menu_item_id,
                mi.name AS item_name,
                mi.category,
                mi.description,
                mis.id AS size_id,
                mis.size_label,
                mis.price,
                mis.is_default
             FROM menu_items mi
             INNER JOIN menu_item_sizes mis ON mis.menu_item_id = mi.id
             WHERE mi.is_available = 1
               AND mis.is_available = 1
               AND mis.id = :size_id
             LIMIT 1'
        );
        $statement->execute(['size_id' => $sizeId]);
        $size = $statement->fetch();

        return is_array($size) ? $size : null;
    }

    public function findItemById(int $itemId): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, name, category, description, image_url, is_available
             FROM menu_items
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $itemId]);
        $item = $statement->fetch();

        return is_array($item) ? $item : null;
    }

    public function findSizeById(int $sizeId): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, menu_item_id, size_label, price, is_default, sort_order, is_available
             FROM menu_item_sizes
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $sizeId]);
        $size = $statement->fetch();

        return is_array($size) ? $size : null;
    }

    public function categories(): array
    {
        return [
            'COFFEE.',
            'NON COFFEE.',
            'TEA BASED.',
            'QUENCHERS.',
            'PANDESAL.',
            'ENSAYMADA.',
            'LOAF.',
            'SANDWICHES.',
        ];
    }

    private function clearDefaultSizesForItem(int $itemId): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE menu_item_sizes
             SET is_default = 0
             WHERE menu_item_id = :menu_item_id'
        );
        $statement->execute(['menu_item_id' => $itemId]);
    }

    private function groupRowsByCategory(array $rows): array
    {
        $categories = [];

        foreach ($rows as $row) {
            $categoryKey = (string) $row['category'];

            if (!isset($categories[$categoryKey])) {
                $categories[$categoryKey] = [
                    'key' => $categoryKey,
                    'name' => $this->formatCategoryName($categoryKey),
                    'description' => $this->categoryDescription($categoryKey),
                    'items' => [],
                ];
            }

            $itemId = (int) $row['id'];

            if (!isset($categories[$categoryKey]['items'][$itemId])) {
                $categories[$categoryKey]['items'][$itemId] = [
                    'id' => $itemId,
                    'name' => (string) $row['name'],
                    'category' => $categoryKey,
                    'description' => $this->normalizeDescription($row['description']),
                    'image_url' => $row['image_url'],
                    'sizes' => [],
                ];
            }

            if ($row['size_id'] === null || (int) $row['size_is_available'] !== 1) {
                continue;
            }

            $categories[$categoryKey]['items'][$itemId]['sizes'][] = [
                'id' => (int) $row['size_id'],
                'label' => (string) $row['size_label'],
                'price' => (float) $row['price'],
                'is_default' => (int) $row['is_default'] === 1,
            ];
        }

        $normalized = [];

        foreach ($categories as $category) {
            $category['items'] = array_values($category['items']);
            $normalized[] = $category;
        }

        return $normalized;
    }

    private function groupManagementRows(array $rows): array
    {
        $categories = [];

        foreach ($rows as $row) {
            $categoryKey = (string) $row['category'];

            if (!isset($categories[$categoryKey])) {
                $categories[$categoryKey] = [
                    'key' => $categoryKey,
                    'name' => $this->formatCategoryName($categoryKey),
                    'items' => [],
                ];
            }

            $itemId = (int) $row['id'];

            if (!isset($categories[$categoryKey]['items'][$itemId])) {
                $categories[$categoryKey]['items'][$itemId] = [
                    'id' => $itemId,
                    'name' => (string) $row['name'],
                    'category' => $categoryKey,
                    'description' => (string) $row['description'],
                    'image_url' => (string) ($row['image_url'] ?? ''),
                    'is_available' => (int) ($row['is_available'] ?? 0) === 1,
                    'sizes' => [],
                ];
            }

            if ($row['size_id'] === null) {
                continue;
            }

            $categories[$categoryKey]['items'][$itemId]['sizes'][] = [
                'id' => (int) $row['size_id'],
                'label' => (string) $row['size_label'],
                'price' => (float) $row['price'],
                'is_default' => (int) $row['is_default'] === 1,
                'sort_order' => (int) $row['sort_order'],
                'is_available' => (int) $row['size_is_available'] === 1,
            ];
        }

        $normalized = [];

        foreach ($categories as $category) {
            $category['items'] = array_values($category['items']);
            $normalized[] = $category;
        }

        return $normalized;
    }

    private function formatCategoryName(string $category): string
    {
        $category = rtrim($category, '.');

        return match ($category) {
            'NON COFFEE' => 'Non Coffee',
            'TEA BASED' => 'Tea Based',
            default => ucwords(strtolower($category)),
        };
    }

    private function categoryDescription(string $category): string
    {
        return match (rtrim($category, '.')) {
            'COFFEE' => 'Coffee-based drinks for morning starts, merienda breaks, and late-night runs.',
            'NON COFFEE' => 'Creamy, chocolatey, and milk-based favorites for a softer cafe order.',
            'TEA BASED' => 'Tea-led drinks for lighter sips and familiar comfort.',
            'QUENCHERS' => 'Cold refreshers and fruit-forward drinks for warmer days.',
            'PANDESAL' => 'Everyday pandesal favorites for quick bread runs and takeout bundles.',
            'ENSAYMADA' => 'Sweet and savory ensaymada selections for breakfast or merienda.',
            'LOAF' => 'Shareable loaf breads for home, office, or family stops.',
            'SANDWICHES' => 'Savory picks for a more filling bread-and-coffee order.',
            default => 'Grande menu favorites for everyday comfort and convenience.',
        };
    }

    private function normalizeDescription(mixed $description): string
    {
        $description = trim((string) $description);

        if ($description !== '') {
            return $description;
        }

        return 'Available in the listed sizes while supplies last.';
    }
}
