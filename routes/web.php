<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\PublicPageController;
use App\Controllers\ReservationController;
use App\Support\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);

    $router->get('/about', [PublicPageController::class, 'about']);
    $router->get('/menu', [PublicPageController::class, 'menu']);
    $router->post('/menu/cart', [CartController::class, 'add']);
    $router->get('/cart', [CartController::class, 'show']);
    $router->post('/cart/update', [CartController::class, 'update']);
    $router->post('/cart/remove', [CartController::class, 'remove']);
    $router->get('/reserve', [ReservationController::class, 'show']);
    $router->post('/reserve', [ReservationController::class, 'store']);
    $router->get('/checkout', [CheckoutController::class, 'show']);
    $router->post('/checkout', [CheckoutController::class, 'store']);
    $router->get('/reservation-checkout', [CheckoutController::class, 'showReservation']);
    $router->post('/reservation-checkout', [CheckoutController::class, 'storeReservation']);
    $router->get('/feedback', [PublicPageController::class, 'feedback']);
    $router->post('/feedback', [PublicPageController::class, 'storeFeedback']);

    $router->get('/login', [AuthController::class, 'login']);
    $router->post('/login', [AuthController::class, 'authenticate']);
    $router->get('/password/forgot', [AuthController::class, 'forgotPassword']);
    $router->post('/password/forgot', [AuthController::class, 'sendPasswordReset']);
    $router->get('/password/reset', [AuthController::class, 'resetPassword']);
    $router->post('/password/reset', [AuthController::class, 'updatePassword']);
    $router->get('/signup', [AuthController::class, 'signup']);
    $router->post('/signup', [AuthController::class, 'register']);
    $router->get('/signup/verify', [AuthController::class, 'verifySignup']);
    $router->post('/signup/verify', [AuthController::class, 'confirmSignupVerification']);
    $router->post('/signup/verify/resend', [AuthController::class, 'resendSignupVerification']);
    $router->post('/logout', [AuthController::class, 'logout']);

    $router->get('/dashboard/customer', [DashboardController::class, 'customer']);
    $router->post('/dashboard/customer/profile', [DashboardController::class, 'updateCustomerProfile']);
    $router->get('/dashboard/admin', [DashboardController::class, 'admin']);
    $router->post('/dashboard/admin/payments', [DashboardController::class, 'updateAdminPayment']);
    $router->post('/dashboard/admin/orders', [DashboardController::class, 'updateAdminOrder']);
    $router->post('/dashboard/admin/reservations', [DashboardController::class, 'updateAdminReservation']);
    $router->post('/dashboard/admin/menu', [DashboardController::class, 'updateAdminMenu']);
    $router->post('/dashboard/admin/users', [DashboardController::class, 'updateAdminUsers']);
    $router->post('/dashboard/admin/feedback', [DashboardController::class, 'updateAdminFeedback']);
    $router->get('/dashboard/employee', [DashboardController::class, 'employee']);
    $router->post('/dashboard/employee/payments', [DashboardController::class, 'updateEmployeePayment']);
    $router->post('/dashboard/employee/orders', [DashboardController::class, 'updateEmployeeOrder']);
    $router->post('/dashboard/employee/reservations', [DashboardController::class, 'updateEmployeeReservation']);
    $router->post('/dashboard/employee/feedback', [DashboardController::class, 'updateEmployeeFeedback']);
};
