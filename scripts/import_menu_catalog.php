<?php
declare(strict_types=1);

use App\Support\Config;
use App\Support\Database;

$app = require __DIR__ . '/../app/bootstrap.php';
unset($app);

$host = (string) Config::get('database.host', 'localhost');
$port = (int) Config::get('database.port', 3306);
$charset = (string) Config::get('database.charset', 'utf8mb4');
$username = (string) Config::get('database.username', 'root');
$password = (string) Config::get('database.password', '');
$targetDatabase = (string) Config::get('database.name', 'grande');
$sourceDatabase = 'grandego_db';

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $sourceDatabase, $charset);
$source = new \PDO($dsn, $username, $password, [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
]);

$target = Database::connection();

$menuItems = $source->query(
    'SELECT id, name, category, description, image_url, is_available, created_at
     FROM menu_items
     ORDER BY id ASC'
)->fetchAll();

$menuItemSizes = $source->query(
    'SELECT id, menu_item_id, size_label, price, is_default, sort_order, is_available, created_at, updated_at
     FROM menu_item_sizes
     ORDER BY id ASC'
)->fetchAll();

$applySqlFile = static function (\PDO $connection, string $path): void {
    $sql = file_get_contents($path);

    if ($sql === false) {
        throw new RuntimeException(sprintf('Unable to read migration file: %s', $path));
    }

    $connection->exec($sql);
};

try {
    $applySqlFile($target, __DIR__ . '/../database/migrations/2026_04_10_000003_create_menu_items.sql');
    $applySqlFile($target, __DIR__ . '/../database/migrations/2026_04_10_000004_create_menu_item_sizes.sql');

    $target->beginTransaction();
    $target->exec('SET FOREIGN_KEY_CHECKS = 0');
    $target->exec('DELETE FROM menu_item_sizes');
    $target->exec('DELETE FROM menu_items');
    $target->exec('SET FOREIGN_KEY_CHECKS = 1');

    $itemStatement = $target->prepare(
        'INSERT INTO menu_items (id, name, category, description, image_url, is_available, created_at)
         VALUES (:id, :name, :category, :description, :image_url, :is_available, :created_at)'
    );

    foreach ($menuItems as $item) {
        $itemStatement->execute([
            'id' => (int) $item['id'],
            'name' => (string) $item['name'],
            'category' => (string) $item['category'],
            'description' => $item['description'],
            'image_url' => $item['image_url'],
            'is_available' => (int) $item['is_available'],
            'created_at' => (string) $item['created_at'],
        ]);
    }

    $sizeStatement = $target->prepare(
        'INSERT INTO menu_item_sizes (id, menu_item_id, size_label, price, is_default, sort_order, is_available, created_at, updated_at)
         VALUES (:id, :menu_item_id, :size_label, :price, :is_default, :sort_order, :is_available, :created_at, :updated_at)'
    );

    foreach ($menuItemSizes as $size) {
        $sizeStatement->execute([
            'id' => (int) $size['id'],
            'menu_item_id' => (int) $size['menu_item_id'],
            'size_label' => (string) $size['size_label'],
            'price' => $size['price'],
            'is_default' => (int) $size['is_default'],
            'sort_order' => (int) $size['sort_order'],
            'is_available' => (int) $size['is_available'],
            'created_at' => (string) $size['created_at'],
            'updated_at' => (string) $size['updated_at'],
        ]);
    }

    $nextMenuItemId = empty($menuItems) ? 1 : (max(array_column($menuItems, 'id')) + 1);
    $nextMenuItemSizeId = empty($menuItemSizes) ? 1 : (max(array_column($menuItemSizes, 'id')) + 1);
    $target->commit();
    $target->exec(sprintf('ALTER TABLE menu_items AUTO_INCREMENT = %d', $nextMenuItemId));
    $target->exec(sprintf('ALTER TABLE menu_item_sizes AUTO_INCREMENT = %d', $nextMenuItemSizeId));

    echo sprintf(
        "Imported %d menu items and %d menu item sizes from %s into %s",
        count($menuItems),
        count($menuItemSizes),
        $sourceDatabase,
        $targetDatabase
    );
    echo PHP_EOL;
} catch (\Throwable $exception) {
    if ($target->inTransaction()) {
        $target->rollBack();
    }

    throw $exception;
}
