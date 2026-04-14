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

$cssPath = __DIR__ . '/../public/assets/css/app.css';
$css = file_exists($cssPath) ? (string) file_get_contents($cssPath) : '';

$accounts = [
    'customer' => [
        'email' => 'qa.customer@grande.local',
        'route' => '/dashboard/customer',
        'panels' => ['overview', 'profile', 'reservations', 'orders', 'feedback'],
        'requiredResponsiveMarkers' => [
            'dashboard-sidebar__nav',
            'overflow-x: auto',
            'dashboard-highlights--customer',
            'dashboard-modal__dialog',
        ],
    ],
    'employee' => [
        'email' => 'qa.employee@grande.local',
        'route' => '/dashboard/employee',
        'panels' => ['overview', 'payments', 'orders', 'reservations', 'feedback', 'reports', 'account'],
        'requiredResponsiveMarkers' => [
            'dashboard-sidebar__nav',
            'overflow-x: auto',
            'dashboard-highlights--staff',
            'dashboard-filter-bar',
            'dashboard-modal__dialog',
        ],
    ],
    'admin' => [
        'email' => 'qa.admin@grande.local',
        'route' => '/dashboard/admin',
        'panels' => ['overview', 'payments', 'orders', 'reservations', 'menu', 'users', 'feedback', 'reports', 'account'],
        'requiredResponsiveMarkers' => [
            'dashboard-sidebar__nav',
            'overflow-x: auto',
            'dashboard-highlights--staff',
            'dashboard-filter-bar',
            'report-table-wrap',
            'dashboard-modal__dialog',
        ],
    ],
];

$globalCssMarkers = [
    '@media (max-width: 900px)',
    '@media (max-width: 640px)',
    '@media (max-width: 600px)',
    '.dashboard-main',
    'overflow: hidden',
    '.report-table-wrap',
    'overflow-x: auto',
    '.dashboard-modal__dialog',
    'max-height: calc(100vh - 3rem)',
];

$users = new UserRepository();
$controller = new DashboardController();
$failures = [];
$passes = [];

foreach ($globalCssMarkers as $marker) {
    if (!str_contains($css, $marker)) {
        $failures[] = "css: missing responsive marker `{$marker}`.";
    }
}

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

    foreach ($contract['panels'] as $panel) {
        if (!str_contains($html, 'data-dashboard-target="' . $panel . '"')) {
            $failures[] = "{$role}: missing dashboard target `{$panel}`.";
        }

        if (!str_contains($html, 'data-dashboard-panel="' . $panel . '"')) {
            $failures[] = "{$role}: missing dashboard panel `{$panel}`.";
        }
    }

    foreach ($contract['requiredResponsiveMarkers'] as $marker) {
        if (!str_contains($css, $marker)) {
            $failures[] = "{$role}: missing CSS support marker `{$marker}`.";
        }
    }

    if (str_contains($html, '<table') && !str_contains($html, 'report-table-wrap')) {
        $failures[] = "{$role}: rendered tables without the horizontal scroll wrapper marker.";
    }

    if (!str_contains($html, 'dashboard-modal__dialog')) {
        $failures[] = "{$role}: missing dashboard modal dialog markup.";
    }

    if (str_contains($html, 'grande-assistant')) {
        $failures[] = "{$role}: public assistant widget is present on a dashboard page.";
    }

    $passes[] = "{$role}: viewport readiness markers present for {$contract['email']}.";
    Session::forget('auth');
}

foreach ($passes as $pass) {
    echo "{$pass}\n";
}

if ($failures !== []) {
    fwrite(STDERR, "\nDashboard viewport readiness audit failed:\n");

    foreach ($failures as $failure) {
        fwrite(STDERR, "- {$failure}\n");
    }

    exit(1);
}

echo "\nDashboard viewport readiness audit passed for customer, employee, and admin dashboard shells.\n";
