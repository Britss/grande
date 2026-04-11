<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\PublicPageController;
use App\Support\Router;

$app = require dirname(__DIR__) . '/app/bootstrap.php';

/** @var Router $router */
$router = $app['router'];

$registerRoutes = require dirname(__DIR__) . '/routes/web.php';
$registerRoutes($router);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
