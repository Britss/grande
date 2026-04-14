<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

$sessionPath = __DIR__ . '/../storage/sessions';

if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0775, true);
}

session_save_path($sessionPath);
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';

require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\DashboardController;
use App\Repositories\UserRepository;
use App\Support\Auth;
use App\Support\Config;
use App\Support\Session;

$accounts = [
    'customer' => [
        'email' => 'qa.customer@grande.local',
        'route' => '/dashboard/customer',
        'required' => [
            'data-dashboard-target="overview"',
            'data-dashboard-target="profile"',
            'data-dashboard-target="reservations"',
            'data-dashboard-target="orders"',
            'data-dashboard-target="feedback"',
            'data-customer-notifications-url=',
            'Profile Picture',
            'Password Access',
            'Order Again',
        ],
        'forbidden' => [
            'data-dashboard-target="payments"',
            'data-dashboard-target="menu"',
            'data-dashboard-target="users"',
            'data-dashboard-target="account"',
            'grande-assistant',
        ],
    ],
    'employee' => [
        'email' => 'qa.employee@grande.local',
        'route' => '/dashboard/employee',
        'required' => [
            'data-dashboard-target="overview"',
            'data-dashboard-target="payments"',
            'data-dashboard-target="orders"',
            'data-dashboard-target="reservations"',
            'data-dashboard-target="feedback"',
            'data-dashboard-target="reports"',
            'data-dashboard-target="account"',
            'Priority Queue',
            'Current Password',
        ],
        'forbidden' => [
            'data-dashboard-target="menu"',
            'data-dashboard-target="users"',
            'Menu Management',
            'User Management',
            'grande-assistant',
        ],
    ],
    'admin' => [
        'email' => 'qa.admin@grande.local',
        'route' => '/dashboard/admin',
        'required' => [
            'data-dashboard-target="overview"',
            'data-dashboard-target="payments"',
            'data-dashboard-target="orders"',
            'data-dashboard-target="reservations"',
            'data-dashboard-target="menu"',
            'data-dashboard-target="users"',
            'data-dashboard-target="feedback"',
            'data-dashboard-target="reports"',
            'data-dashboard-target="account"',
            'Needs Attention',
            'Current Password',
        ],
        'forbidden' => [
            'grande-assistant',
        ],
    ],
];

$users = new UserRepository();
$controller = new DashboardController();
$failures = [];
$passes = [];

foreach ($accounts as $role => $contract) {
    $_SESSION = [
        '_flash' => [
            'current' => [],
            'next' => [],
        ],
    ];

    $_GET = [];
    $_POST = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    Config::setValue('request.method', 'GET');
    Config::setValue('request.path', $contract['route']);

    $user = $users->findByEmail($contract['email']);

    if ($user === null) {
        $failures[] = "{$role}: missing QA account {$contract['email']}. Run scripts/seed_dashboard_qa_accounts.php first.";
        continue;
    }

    Auth::login($user);

    try {
        $html = match ($role) {
            'customer' => $controller->customer(),
            'employee' => $controller->employee(),
            'admin' => $controller->admin(),
        };
    } catch (Throwable $exception) {
        $failures[] = "{$role}: render failed: {$exception->getMessage()}";
        continue;
    }

    foreach ($contract['required'] as $needle) {
        if (!str_contains($html, $needle)) {
            $failures[] = "{$role}: missing expected marker `{$needle}`.";
        }
    }

    foreach ($contract['forbidden'] as $needle) {
        if (str_contains($html, $needle)) {
            $failures[] = "{$role}: found forbidden marker `{$needle}`.";
        }
    }

    $passes[] = "{$role}: dashboard contract rendered for {$contract['email']}.";
    Session::forget('auth');
}

foreach ($passes as $pass) {
    echo "{$pass}\n";
}

if ($failures !== []) {
    fwrite(STDERR, "\nDashboard parity smoke failed:\n");

    foreach ($failures as $failure) {
        fwrite(STDERR, "- {$failure}\n");
    }

    exit(1);
}

echo "\nDashboard parity smoke passed for customer, employee, and admin QA accounts.\n";
