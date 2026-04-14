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

$password = (string) (getenv('QA_SEED_PASSWORD') ?: 'GrandeQA#2026');

if (strlen($password) < 8) {
    fwrite(STDERR, "QA_SEED_PASSWORD must be at least 8 characters.\n");
    exit(1);
}

$usersMigration = __DIR__ . '/../database/migrations/2026_04_10_000001_create_users.sql';

if (!is_file($usersMigration)) {
    fwrite(STDERR, "Missing users migration: {$usersMigration}\n");
    exit(1);
}

$connection = Database::connection();
$connection->exec((string) file_get_contents($usersMigration));

$accounts = [
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

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$statement = $connection->prepare(
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

foreach ($accounts as $account) {
    $statement->execute([
        'first_name' => $account['first_name'],
        'last_name' => $account['last_name'],
        'email' => $account['email'],
        'phone' => $account['phone'],
        'password' => $passwordHash,
        'role' => $account['role'],
    ]);
}

$appName = (string) Config::get('app.name', 'Grande.');

echo "{$appName} dashboard QA accounts are ready.\n";
echo "Password: {$password}\n";

foreach ($accounts as $account) {
    echo "- {$account['role']}: {$account['email']}\n";
}
