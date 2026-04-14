<?php
declare(strict_types=1);

if (PHP_SAPI === 'cli') {
    $sessionPath = __DIR__ . '/../storage/sessions';

    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0775, true);
    }

    session_save_path($sessionPath);
}

require_once __DIR__ . '/../app/bootstrap.php';

use App\Support\Config;
use App\Support\Database;

$connection = Database::connection();

$migrations = [
    '2026_04_10_000001_create_users.sql',
    '2026_04_10_000003_create_menu_items.sql',
    '2026_04_10_000004_create_menu_item_sizes.sql',
    '2026_04_11_000006_create_reservations.sql',
    '2026_04_11_000007_create_orders.sql',
    '2026_04_11_000008_create_order_items.sql',
    '2026_04_11_000009_create_feedback.sql',
    '2026_04_14_000012_create_customer_notifications.sql',
];

foreach ($migrations as $migration) {
    $path = __DIR__ . '/../database/migrations/' . $migration;

    if (!is_file($path)) {
        fwrite(STDERR, "Missing migration: {$path}\n");
        exit(1);
    }

    $connection->exec((string) file_get_contents($path));
}

$password = (string) (getenv('QA_SEED_PASSWORD') ?: 'GrandeQA#2026');
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$upsertUser = $connection->prepare(
    'INSERT INTO users (first_name, last_name, email, phone, password, role, is_active)
     VALUES (:first_name, :last_name, :email, :phone, :password, :role, 1)
     ON DUPLICATE KEY UPDATE
        first_name = VALUES(first_name),
        last_name = VALUES(last_name),
        phone = VALUES(phone),
        password = VALUES(password),
        role = VALUES(role),
        is_active = 1'
);

$qaUsers = [
    [
        'first_name' => 'QA',
        'last_name' => 'Customer',
        'email' => 'qa.customer@grande.local',
        'phone' => '09170000001',
        'role' => 'customer',
    ],
    [
        'first_name' => 'QA',
        'last_name' => 'Employee',
        'email' => 'qa.employee@grande.local',
        'phone' => '09170000002',
        'role' => 'employee',
    ],
    [
        'first_name' => 'QA',
        'last_name' => 'Admin',
        'email' => 'qa.admin@grande.local',
        'phone' => '09170000003',
        'role' => 'admin',
    ],
];

foreach ($qaUsers as $user) {
    $upsertUser->execute([
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'password' => $passwordHash,
        'role' => $user['role'],
    ]);
}

$customerId = (int) $connection
    ->query("SELECT id FROM users WHERE email = 'qa.customer@grande.local' LIMIT 1")
    ->fetchColumn();

if ($customerId <= 0) {
    fwrite(STDERR, "Unable to locate the QA customer account.\n");
    exit(1);
}

$menuItems = [
    [
        'name' => 'QA Americano',
        'category' => 'Coffee',
        'description' => 'QA dashboard seed coffee item.',
        'image_url' => 'images/menu-items/americano.png',
        'sizes' => [
            ['label' => 'Regular', 'price' => '95.00', 'default' => 1],
            ['label' => 'Large', 'price' => '115.00', 'default' => 0],
        ],
    ],
    [
        'name' => 'QA Classic Ensaymada',
        'category' => 'Bread',
        'description' => 'QA dashboard seed pastry item.',
        'image_url' => 'images/menu-items/classic_ensaymada.png',
        'sizes' => [
            ['label' => 'Default', 'price' => '75.00', 'default' => 1],
        ],
    ],
];

$selectMenuItem = $connection->prepare('SELECT id FROM menu_items WHERE name = :name AND category = :category LIMIT 1');
$insertMenuItem = $connection->prepare(
    'INSERT INTO menu_items (name, category, description, image_url, is_available)
     VALUES (:name, :category, :description, :image_url, 1)'
);
$updateMenuItem = $connection->prepare(
    'UPDATE menu_items
     SET description = :description, image_url = :image_url, is_available = 1
     WHERE id = :id'
);
$selectSize = $connection->prepare(
    'SELECT id FROM menu_item_sizes WHERE menu_item_id = :menu_item_id AND size_label = :size_label LIMIT 1'
);
$insertSize = $connection->prepare(
    'INSERT INTO menu_item_sizes (menu_item_id, size_label, price, is_default, sort_order, is_available)
     VALUES (:menu_item_id, :size_label, :price, :is_default, :sort_order, 1)'
);
$updateSize = $connection->prepare(
    'UPDATE menu_item_sizes
     SET price = :price, is_default = :is_default, sort_order = :sort_order, is_available = 1
     WHERE id = :id'
);

$seedItems = [];

foreach ($menuItems as $item) {
    $selectMenuItem->execute([
        'name' => $item['name'],
        'category' => $item['category'],
    ]);

    $menuItemId = (int) $selectMenuItem->fetchColumn();

    if ($menuItemId > 0) {
        $updateMenuItem->execute([
            'description' => $item['description'],
            'image_url' => $item['image_url'],
            'id' => $menuItemId,
        ]);
    } else {
        $insertMenuItem->execute([
            'name' => $item['name'],
            'category' => $item['category'],
            'description' => $item['description'],
            'image_url' => $item['image_url'],
        ]);
        $menuItemId = (int) $connection->lastInsertId();
    }

    foreach ($item['sizes'] as $index => $size) {
        $selectSize->execute([
            'menu_item_id' => $menuItemId,
            'size_label' => $size['label'],
        ]);

        $sizeId = (int) $selectSize->fetchColumn();
        $sizePayload = [
            'price' => $size['price'],
            'is_default' => $size['default'],
            'sort_order' => $index + 1,
        ];

        if ($sizeId > 0) {
            $updateSize->execute($sizePayload + ['id' => $sizeId]);
        } else {
            $insertSize->execute($sizePayload + [
                'menu_item_id' => $menuItemId,
                'size_label' => $size['label'],
            ]);
        }
    }

    $seedItems[] = [
        'id' => $menuItemId,
        'name' => $item['name'],
        'size' => $item['sizes'][0]['label'],
        'price' => (float) $item['sizes'][0]['price'],
    ];
}

$connection->beginTransaction();

try {
    $qaOrderIds = $connection
        ->query("SELECT id FROM orders WHERE order_number LIKE 'QA-%'")
        ->fetchAll(PDO::FETCH_COLUMN);

    if ($qaOrderIds !== []) {
        $placeholders = implode(',', array_fill(0, count($qaOrderIds), '?'));
        $connection->prepare("DELETE FROM customer_order_notifications WHERE order_id IN ({$placeholders})")->execute($qaOrderIds);
        $connection->prepare("DELETE FROM order_items WHERE order_id IN ({$placeholders})")->execute($qaOrderIds);
        $connection->prepare("DELETE FROM orders WHERE id IN ({$placeholders})")->execute($qaOrderIds);
    }

    $qaReservationIds = $connection
        ->query("SELECT id FROM reservations WHERE email = 'qa.customer@grande.local' AND first_name = 'QA' AND last_name = 'Customer'")
        ->fetchAll(PDO::FETCH_COLUMN);

    if ($qaReservationIds !== []) {
        $placeholders = implode(',', array_fill(0, count($qaReservationIds), '?'));
        $connection->prepare("DELETE FROM customer_reservation_notifications WHERE reservation_id IN ({$placeholders})")->execute($qaReservationIds);
        $connection->prepare("UPDATE orders SET reservation_id = NULL WHERE reservation_id IN ({$placeholders})")->execute($qaReservationIds);
        $connection->prepare("DELETE FROM reservations WHERE id IN ({$placeholders})")->execute($qaReservationIds);
    }

    $connection
        ->prepare("DELETE FROM feedback WHERE email = :email AND message LIKE '[QA]%'")
        ->execute(['email' => 'qa.customer@grande.local']);

    $insertReservation = $connection->prepare(
        'INSERT INTO reservations (user_id, first_name, last_name, email, phone, date, time, guests, status, created_at)
         VALUES (:user_id, :first_name, :last_name, :email, :phone, :date, :time, :guests, :status, :created_at)'
    );

    $reservationSeeds = [
        ['date' => date('Y-m-d', strtotime('+2 days')), 'time' => '15:00:00', 'guests' => 4, 'status' => 'pending', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))],
        ['date' => date('Y-m-d', strtotime('+5 days')), 'time' => '10:30:00', 'guests' => 2, 'status' => 'confirmed', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
        ['date' => date('Y-m-d', strtotime('-3 days')), 'time' => '14:00:00', 'guests' => 3, 'status' => 'completed', 'created_at' => date('Y-m-d H:i:s', strtotime('-6 days'))],
    ];

    $reservationIds = [];

    foreach ($reservationSeeds as $seed) {
        $insertReservation->execute([
            'user_id' => $customerId,
            'first_name' => 'QA',
            'last_name' => 'Customer',
            'email' => 'qa.customer@grande.local',
            'phone' => '09170000001',
            'date' => $seed['date'],
            'time' => $seed['time'],
            'guests' => $seed['guests'],
            'status' => $seed['status'],
            'created_at' => $seed['created_at'],
        ]);

        $reservationIds[] = (int) $connection->lastInsertId();
    }

    $insertOrder = $connection->prepare(
        'INSERT INTO orders (user_id, reservation_id, order_number, total_amount, status, payment_status, receipt_image, created_at)
         VALUES (:user_id, :reservation_id, :order_number, :total_amount, :status, :payment_status, :receipt_image, :created_at)'
    );
    $insertOrderItem = $connection->prepare(
        'INSERT INTO order_items (order_id, menu_item_id, quantity, price, subtotal, size)
         VALUES (:order_id, :menu_item_id, :quantity, :price, :subtotal, :size)'
    );

    $orderSeeds = [
        ['number' => 'QA-PENDING-PAYMENT', 'reservation_id' => null, 'status' => 'pending', 'payment_status' => 'pending', 'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')), 'items' => [[0, 2], [1, 1]]],
        ['number' => 'QA-PREPARING', 'reservation_id' => null, 'status' => 'preparing', 'payment_status' => 'verified', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')), 'items' => [[0, 1], [1, 2]]],
        ['number' => 'QA-COMPLETED', 'reservation_id' => null, 'status' => 'completed', 'payment_status' => 'verified', 'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')), 'items' => [[0, 1]]],
        ['number' => 'QA-RESERVATION-LINKED', 'reservation_id' => $reservationIds[1] ?? null, 'status' => 'pending', 'payment_status' => 'verified', 'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours')), 'items' => [[1, 3]]],
    ];

    foreach ($orderSeeds as $seed) {
        $total = 0.0;

        foreach ($seed['items'] as [$itemIndex, $quantity]) {
            $total += $seedItems[$itemIndex]['price'] * $quantity;
        }

        $insertOrder->execute([
            'user_id' => $customerId,
            'reservation_id' => $seed['reservation_id'],
            'order_number' => $seed['number'],
            'total_amount' => number_format($total, 2, '.', ''),
            'status' => $seed['status'],
            'payment_status' => $seed['payment_status'],
            'receipt_image' => $seed['payment_status'] === 'pending' ? 'uploads/receipts/qa-receipt.png' : null,
            'created_at' => $seed['created_at'],
        ]);

        $orderId = (int) $connection->lastInsertId();

        foreach ($seed['items'] as [$itemIndex, $quantity]) {
            $item = $seedItems[$itemIndex];
            $subtotal = $item['price'] * $quantity;

            $insertOrderItem->execute([
                'order_id' => $orderId,
                'menu_item_id' => $item['id'],
                'quantity' => $quantity,
                'price' => number_format($item['price'], 2, '.', ''),
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'size' => $item['size'],
            ]);
        }
    }

    $insertFeedback = $connection->prepare(
        'INSERT INTO feedback (user_id, name, email, rating, category, message, status, created_at)
         VALUES (:user_id, :name, :email, :rating, :category, :message, :status, :created_at)'
    );

    foreach ([
        ['rating' => 5, 'category' => 'service', 'message' => '[QA] Friendly staff and quick counter flow.', 'status' => 'new', 'created_at' => date('Y-m-d H:i:s', strtotime('-45 minutes'))],
        ['rating' => 4, 'category' => 'food', 'message' => '[QA] Ensaymada and coffee pairing looked good in the dashboard.', 'status' => 'in_review', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))],
        ['rating' => 5, 'category' => 'experience', 'message' => '[QA] Resolved feedback sample for history panels.', 'status' => 'resolved', 'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))],
    ] as $seed) {
        $insertFeedback->execute([
            'user_id' => $customerId,
            'name' => 'QA Customer',
            'email' => 'qa.customer@grande.local',
            'rating' => $seed['rating'],
            'category' => $seed['category'],
            'message' => $seed['message'],
            'status' => $seed['status'],
            'created_at' => $seed['created_at'],
        ]);
    }

    $connection->commit();
} catch (Throwable $exception) {
    $connection->rollBack();
    fwrite(STDERR, "Unable to seed dashboard QA workflow data: {$exception->getMessage()}\n");
    exit(1);
}

$appName = (string) Config::get('app.name', 'Grande.');

echo "{$appName} dashboard QA workflow data is ready.\n";
echo "Seeded customer: qa.customer@grande.local\n";
echo "Password: {$password}\n";
echo "- Menu items: " . count($seedItems) . "\n";
echo "- Orders: 4\n";
echo "- Reservations: 3\n";
echo "- Feedback records: 3\n";
