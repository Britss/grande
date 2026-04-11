<?php
declare(strict_types=1);

use App\Support\Config;
use App\Support\Router;
use App\Support\Session;

date_default_timezone_set('Asia/Manila');
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

spl_autoload_register(static function (string $className): void {
    $prefix = 'App\\';

    if (strncmp($className, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($className, strlen($prefix));
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
    $fullPath = __DIR__ . DIRECTORY_SEPARATOR . $relativePath;

    if (is_file($fullPath)) {
        require_once $fullPath;
    }
});

require_once __DIR__ . '/Support/helpers.php';

Session::bootstrap();

$scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$basePath = $scriptDirectory === '/' || $scriptDirectory === '.' ? '' : rtrim($scriptDirectory, '/');

Config::set([
    'app' => [
        'name' => 'Grande.',
        'tagline' => 'Pandesal + Coffee',
        'timezone' => 'Asia/Manila',
        'base_path' => $basePath,
        'site_email' => 'grande.pandesalcoffee.main@gmail.com',
        'phone' => '+63 954 247 8073',
        'address' => "Beside Puregold, In front of St. Anthony's Drug Store, Sindalan, San Fernando, Pampanga",
    ],
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'grande',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    'mail' => [
        'smtp_enabled' => filter_var(getenv('SMTP_ENABLED') ?: 'false', FILTER_VALIDATE_BOOL),
        'use_php_mail' => filter_var(getenv('MAIL_USE_PHP_MAIL') ?: 'false', FILTER_VALIDATE_BOOL),
        'host' => getenv('SMTP_HOST') ?: '',
        'port' => (int) (getenv('SMTP_PORT') ?: 587),
        'encryption' => strtolower((string) (getenv('SMTP_ENCRYPTION') ?: 'tls')),
        'username' => getenv('SMTP_USERNAME') ?: '',
        'password' => getenv('SMTP_PASSWORD') ?: '',
        'from_email' => getenv('SMTP_FROM_EMAIL') ?: 'grande.pandesalcoffee.main@gmail.com',
        'from_name' => getenv('SMTP_FROM_NAME') ?: 'Grande. Pandesal + Coffee',
        'log_path' => __DIR__ . '/../storage/logs/mail.log',
    ],
    'request' => [
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
        'path' => '/',
    ],
]);

return [
    'router' => new Router(),
];
