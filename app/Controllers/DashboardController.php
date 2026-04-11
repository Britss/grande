<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\AuditLogRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\MenuRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReportRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\UserRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\MenuImageUploader;
use App\Support\Session;
use App\Support\Validator;

final class DashboardController extends Controller
{
    public function customer(): string
    {
        $user = $this->requireRole('customer');
        $freshUser = (new UserRepository())->findById((int) ($user['id'] ?? 0));

        if (is_array($freshUser) && ($freshUser['role'] ?? null) === 'customer') {
            $user = $freshUser;
        }

        $orders = new OrderRepository();
        $reservations = new ReservationRepository();

        return $this->render('pages.dashboards.customer', [
            'pageTitle' => 'Customer Dashboard',
            'metaDescription' => 'Customer dashboard for the Grande rewrite.',
            'bodyClass' => 'dashboard-body dashboard-body--customer',
            'user' => $user,
            'orderStats' => $orders->getCustomerOrderStats((int) ($user['id'] ?? 0)),
            'recentOrders' => $orders->getCustomerOrders((int) ($user['id'] ?? 0)),
            'reservationStats' => $reservations->getCustomerReservationStats((int) ($user['id'] ?? 0)),
            'recentReservations' => $reservations->getCustomerReservations((int) ($user['id'] ?? 0)),
        ]);
    }

    public function updateCustomerProfile(): never
    {
        $user = $this->requireRole('customer');

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'The form expired. Please update your profile again.');
            redirect('/dashboard/customer?section=profile');
        }

        $users = new UserRepository();

        try {
            $input = $this->validatedCustomerProfileData((int) ($user['id'] ?? 0));
            $updatedUser = $users->updateCustomerProfile((int) ($user['id'] ?? 0), $input);
            Auth::login($updatedUser);
            (new AuditLogRepository())->log((int) ($updatedUser['id'] ?? 0), 'customer_profile_updated', 'user', (int) ($updatedUser['id'] ?? 0), [
                'email' => $updatedUser['email'] ?? '',
                'phone' => $updatedUser['phone'] ?? '',
            ]);
            Session::flash('status', 'Your profile was updated successfully.');
        } catch (\Throwable $exception) {
            Session::flash('error', $exception->getMessage());
        }

        redirect('/dashboard/customer?section=profile');
    }

    public function admin(): string
    {
        $user = $this->requireRole('admin');

        return $this->render('pages.dashboards.admin', array_merge(
            $this->staffDashboardData($user),
            [
                'pageTitle' => 'Admin Dashboard',
                'metaDescription' => 'Admin dashboard for the Grande rewrite.',
                'bodyClass' => 'dashboard-body dashboard-body--staff',
                'menuCatalog' => (new MenuRepository())->managementCatalog(),
                'menuCategoryOptions' => (new MenuRepository())->categories(),
                'userManagementStats' => (new UserRepository())->getManagementStats(),
                'manageableUsers' => (new UserRepository())->listForManagement(),
            ]
        ));
    }

    public function employee(): string
    {
        $user = $this->requireRole('employee');

        return $this->render('pages.dashboards.employee', array_merge(
            $this->staffDashboardData($user),
            [
                'pageTitle' => 'Employee Dashboard',
                'metaDescription' => 'Employee dashboard for the Grande rewrite.',
                'bodyClass' => 'dashboard-body dashboard-body--staff',
            ]
        ));
    }

    public function updateAdminPayment(): never
    {
        $this->updatePaymentForRole('admin');
    }

    public function updateEmployeePayment(): never
    {
        $this->updatePaymentForRole('employee');
    }

    public function updateAdminOrder(): never
    {
        $this->updateOrderForRole('admin');
    }

    public function updateEmployeeOrder(): never
    {
        $this->updateOrderForRole('employee');
    }

    public function updateAdminReservation(): never
    {
        $this->updateReservationForRole('admin');
    }

    public function updateEmployeeReservation(): never
    {
        $this->updateReservationForRole('employee');
    }

    public function updateAdminMenu(): never
    {
        $user = $this->requireRole('admin');
        $section = $this->requestedSection('menu');
        $this->validateDashboardCsrf('admin', $section, 'menu update');

        $menus = new MenuRepository();
        $audit = new AuditLogRepository();
        $action = (string) request_input('menu_action', '');

        try {
            switch ($action) {
                case 'create_item':
                    $itemData = $this->validatedMenuItemData();
                    $itemId = $menus->createItem($itemData);
                    $audit->log((int) ($user['id'] ?? 0), 'menu_item_created', 'menu_item', $itemId, [
                        'name' => $itemData['name'],
                        'category' => $itemData['category'],
                    ]);
                    Session::flash('status', 'Menu item created successfully.');
                    break;

                case 'update_item':
                    $itemId = (int) request_input('item_id', 0);
                    $itemData = $this->validatedMenuItemData();
                    $item = $menus->updateItem($itemId, $itemData);
                    $audit->log((int) ($user['id'] ?? 0), 'menu_item_updated', 'menu_item', (int) ($item['id'] ?? $itemId), [
                        'name' => $item['name'] ?? '',
                        'is_available' => (int) ($item['is_available'] ?? 0),
                    ]);
                    Session::flash('status', 'Menu item updated successfully.');
                    break;

                case 'create_size':
                    $itemId = (int) request_input('item_id', 0);
                    $sizeData = $this->validatedMenuSizeData();
                    $sizeId = $menus->createSize($itemId, $sizeData);
                    $audit->log((int) ($user['id'] ?? 0), 'menu_size_created', 'menu_item_size', $sizeId, [
                        'menu_item_id' => $itemId,
                        'label' => $sizeData['size_label'],
                    ]);
                    Session::flash('status', 'Menu size created successfully.');
                    break;

                case 'update_size':
                    $sizeId = (int) request_input('size_id', 0);
                    $sizeData = $this->validatedMenuSizeData();
                    $size = $menus->updateSize($sizeId, $sizeData);
                    $audit->log((int) ($user['id'] ?? 0), 'menu_size_updated', 'menu_item_size', (int) ($size['id'] ?? $sizeId), [
                        'menu_item_id' => (int) ($size['menu_item_id'] ?? 0),
                        'label' => $size['size_label'] ?? '',
                        'is_available' => (int) ($size['is_available'] ?? 0),
                    ]);
                    Session::flash('status', 'Menu size updated successfully.');
                    break;

                default:
                    throw new \RuntimeException('Unknown menu action.');
            }
        } catch (\Throwable $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->finishDashboardResponse('admin', $section);
    }

    public function updateAdminUsers(): never
    {
        $user = $this->requireRole('admin');
        $section = $this->requestedSection('users');
        $this->validateDashboardCsrf('admin', $section, 'user management update');

        $users = new UserRepository();
        $audit = new AuditLogRepository();
        $action = (string) request_input('user_action', '');

        try {
            switch ($action) {
                case 'create_staff':
                    $input = $this->validatedStaffCreateData();
                    $userId = $users->create([
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'email' => $input['email'],
                        'phone' => $input['phone'],
                        'password' => password_hash($input['password'], PASSWORD_DEFAULT),
                        'role' => $input['role'],
                        'is_active' => $input['is_active'],
                    ]);
                    $audit->log((int) ($user['id'] ?? 0), 'staff_user_created', 'user', $userId, [
                        'role' => $input['role'],
                        'email' => $input['email'],
                    ]);
                    Session::flash('status', 'Staff account created successfully.');
                    break;

                case 'update_user':
                    $targetUserId = (int) request_input('user_id', 0);
                    $existingUser = $users->findById($targetUserId);

                    if ($existingUser === null) {
                        throw new \RuntimeException('User not found.');
                    }

                    $input = $this->validatedUserUpdateData($targetUserId);

                    if ((int) ($user['id'] ?? 0) === $targetUserId
                        && ((int) $input['is_active'] !== 1 || $input['role'] !== 'admin')) {
                        throw new \RuntimeException('You cannot deactivate yourself or remove your own admin role here.');
                    }

                    $updatedUser = $users->updateManagement($targetUserId, $input);

                    if ((int) ($user['id'] ?? 0) === $targetUserId) {
                        Auth::login($updatedUser);
                    }

                    $audit->log((int) ($user['id'] ?? 0), 'user_updated', 'user', (int) ($updatedUser['id'] ?? $targetUserId), [
                        'role' => $updatedUser['role'] ?? '',
                        'is_active' => (int) ($updatedUser['is_active'] ?? 0),
                        'email' => $updatedUser['email'] ?? '',
                    ]);
                    Session::flash('status', 'User account updated successfully.');
                    break;

                default:
                    throw new \RuntimeException('Unknown user management action.');
            }
        } catch (\Throwable $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->finishDashboardResponse('admin', $section);
    }

    public function updateAdminFeedback(): never
    {
        $this->updateFeedbackForRole('admin');
    }

    public function updateEmployeeFeedback(): never
    {
        $this->updateFeedbackForRole('employee');
    }

    private function requireRole(string $role): array
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please log in to continue.');
            redirect('/login');
        }

        $user = Auth::user();

        if (!is_array($user)) {
            redirect('/login');
        }

        if (($user['role'] ?? null) !== $role) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        return $user;
    }

    private function staffDashboardData(array $user): array
    {
        $orders = new OrderRepository();
        $reservations = new ReservationRepository();
        $feedback = new FeedbackRepository();
        $reports = new ReportRepository();
        $audit = new AuditLogRepository();
        $reportRange = $this->reportDateRange();

        return [
            'user' => $user,
            'canViewBusinessReports' => ($user['role'] ?? '') === 'admin',
            'paymentStats' => $orders->getPaymentReviewStats(),
            'pendingPaymentOrders' => $orders->getPendingPaymentReviewOrders(),
            'reviewedPaymentOrders' => $orders->getReviewedPaymentOrders(),
            'fulfillmentStats' => $orders->getFulfillmentStats(),
            'fulfillmentOrders' => $orders->getFulfillmentOrders(),
            'reservationManagementStats' => $reservations->getManagementStats(),
            'manageableReservations' => $reservations->getManageableReservations(),
            'feedbackStats' => $feedback->getManagementStats(),
            'manageableFeedback' => $feedback->getManageableFeedback(),
            'reportOverview' => $reports->overview(),
            'orderStatusBreakdown' => $reports->orderStatusBreakdown(),
            'reservationStatusBreakdown' => $reports->reservationStatusBreakdown(),
            'reportRange' => $reportRange,
            'rangeSummary' => $reports->rangeSummary($reportRange['start'], $reportRange['end']),
            'dailySales' => $reports->dailySalesForRange($reportRange['start'], $reportRange['end']),
            'orderVolumeByDay' => $reports->orderVolumeForRange($reportRange['start'], $reportRange['end']),
            'reservationVolumeByDay' => $reports->reservationVolumeForRange($reportRange['start'], $reportRange['end']),
            'topSellingMenuItems' => $reports->topSellingMenuItemsForRange($reportRange['start'], $reportRange['end']),
            'orderDrilldown' => $reports->orderDrilldown($reportRange['start'], $reportRange['end']),
            'reservationDrilldown' => $reports->reservationDrilldown($reportRange['start'], $reportRange['end']),
            'recentAuditLogs' => $audit->latest(),
        ];
    }

    private function reportDateRange(): array
    {
        $today = new \DateTimeImmutable('today');
        $defaultStart = $today->modify('-6 days');
        $start = $this->dateInputOrDefault('report_start', $defaultStart);
        $end = $this->dateInputOrDefault('report_end', $today);

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $maxStart = $end->modify('-30 days');

        if ($start < $maxStart) {
            $start = $maxStart;
        }

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'label' => $start->format('M d, Y') . ' to ' . $end->format('M d, Y'),
        ];
    }

    private function dateInputOrDefault(string $key, \DateTimeImmutable $default): \DateTimeImmutable
    {
        $value = (string) request_input($key, '');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $default;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date instanceof \DateTimeImmutable ? $date : $default;
    }

    private function updatePaymentForRole(string $role): never
    {
        $user = $this->requireRole($role);
        $section = $this->requestedSection('payments');
        $this->validateDashboardCsrf($role, $section, 'payment review');

        $orderId = (int) request_input('order_id', 0);
        $action = (string) request_input('payment_action', '');
        $orders = new OrderRepository();

        try {
            $reviewedOrder = $orders->reviewPayment($orderId, $action);
            (new AuditLogRepository())->log((int) ($user['id'] ?? 0), 'payment_' . ($reviewedOrder['payment_status'] ?? 'pending'), 'order', (int) ($reviewedOrder['id'] ?? $orderId), [
                'order_number' => $reviewedOrder['order_number'] ?? '',
                'payment_status' => $reviewedOrder['payment_status'] ?? '',
            ]);
            $verb = ($reviewedOrder['payment_status'] ?? 'pending') === 'verified' ? 'verified' : 'rejected';
            Session::flash('status', 'Payment for order ' . ($reviewedOrder['order_number'] ?? '') . ' was ' . $verb . '.');
        } catch (\Throwable $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->finishDashboardResponse($role, $section);
    }

    private function updateOrderForRole(string $role): never
    {
        $user = $this->requireRole($role);
        $section = $this->requestedSection('orders');
        $this->validateDashboardCsrf($role, $section, 'order update');

        $orderId = (int) request_input('order_id', 0);
        $status = (string) request_input('status', '');
        $orders = new OrderRepository();

        try {
            $updatedOrder = $orders->updateStatus($orderId, $status);
            (new AuditLogRepository())->log((int) ($user['id'] ?? 0), 'order_status_updated', 'order', (int) ($updatedOrder['id'] ?? $orderId), [
                'order_number' => $updatedOrder['order_number'] ?? '',
                'status' => $updatedOrder['status'] ?? '',
            ]);
            Session::flash('status', 'Order ' . ($updatedOrder['order_number'] ?? '') . ' moved to ' . ($updatedOrder['status'] ?? 'pending') . '.');
        } catch (\Throwable $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->finishDashboardResponse($role, $section);
    }

    private function updateReservationForRole(string $role): never
    {
        $user = $this->requireRole($role);
        $section = $this->requestedSection('reservations');
        $this->validateDashboardCsrf($role, $section, 'reservation update');

        $reservationId = (int) request_input('reservation_id', 0);
        $status = (string) request_input('status', '');
        $reservations = new ReservationRepository();

        try {
            $updatedReservation = $reservations->updateStatus($reservationId, $status);
            (new AuditLogRepository())->log((int) ($user['id'] ?? 0), 'reservation_status_updated', 'reservation', (int) ($updatedReservation['id'] ?? $reservationId), [
                'status' => $updatedReservation['status'] ?? '',
            ]);
            Session::flash('status', 'Reservation #' . ($updatedReservation['id'] ?? 0) . ' moved to ' . ($updatedReservation['status'] ?? 'pending') . '.');
        } catch (\Throwable $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->finishDashboardResponse($role, $section);
    }

    private function updateFeedbackForRole(string $role): never
    {
        $user = $this->requireRole($role);
        $section = $this->requestedSection('feedback');
        $this->validateDashboardCsrf($role, $section, 'feedback update');

        $feedbackId = (int) request_input('feedback_id', 0);
        $status = (string) request_input('status', '');
        $feedback = new FeedbackRepository();

        try {
            $updatedFeedback = $feedback->updateStatus($feedbackId, $status);
            (new AuditLogRepository())->log((int) ($user['id'] ?? 0), 'feedback_status_updated', 'feedback', (int) ($updatedFeedback['id'] ?? $feedbackId), [
                'status' => $updatedFeedback['status'] ?? '',
                'category' => $updatedFeedback['category'] ?? '',
            ]);
            Session::flash('status', 'Feedback #' . ($updatedFeedback['id'] ?? 0) . ' moved to ' . ($updatedFeedback['status'] ?? 'new') . '.');
        } catch (\Throwable $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->finishDashboardResponse($role, $section);
    }

    private function validateDashboardCsrf(string $role, string $section, string $context): bool
    {
        if (Csrf::validate((string) request_input('_token'))) {
            return true;
        }

        Session::flash('error', 'The form expired. Please try the ' . $context . ' again.');
        $this->finishDashboardResponse($role, $section);
    }

    private function redirectToRoleDashboard(string $role, string $section = 'overview'): never
    {
        $path = Auth::dashboardPathForRole($role);
        $path .= '?section=' . urlencode($section);
        redirect($path);
    }

    private function finishDashboardResponse(string $role, string $section = 'overview'): never
    {
        if ($this->expectsDashboardJson()) {
            $this->sendDashboardJson($role, $section);
        }

        $this->redirectToRoleDashboard($role, $section);
    }

    private function expectsDashboardJson(): bool
    {
        $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
        $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));

        return $requestedWith === 'xmlhttprequest' || str_contains($accept, 'application/json');
    }

    private function sendDashboardJson(string $role, string $section): never
    {
        $user = Auth::user() ?? [];
        $status = Session::getFlash('status');
        $error = Session::getFlash('error');

        header('Content-Type: application/json');
        echo json_encode([
            'ok' => $error === null,
            'section' => $section,
            'status' => $status,
            'error' => $error,
            'panelHtml' => $this->renderDashboardPanel($role, $section, $user),
            'csrfToken' => Csrf::token(),
        ]);
        exit;
    }

    private function renderDashboardPanel(string $role, string $section, array $user): string
    {
        $data = $this->staffDashboardData($user);

        if ($role === 'admin') {
            $menuRepository = new MenuRepository();
            $userRepository = new UserRepository();
            $data = array_merge($data, [
                'menuCatalog' => $menuRepository->managementCatalog(),
                'menuCategoryOptions' => $menuRepository->categories(),
                'userManagementStats' => $userRepository->getManagementStats(),
                'manageableUsers' => $userRepository->listForManagement(),
            ]);
        }

        $partial = match ($section) {
            'payments' => 'payment-review-dashboard',
            'orders' => 'order-management-dashboard',
            'reservations' => 'reservation-management-dashboard',
            'menu' => $role === 'admin' ? 'menu-management-dashboard' : '',
            'users' => $role === 'admin' ? 'user-management-dashboard' : '',
            'feedback' => 'feedback-management-dashboard',
            'reports' => 'reporting-dashboard',
            default => '',
        };

        if ($partial === '') {
            return '';
        }

        if ($partial === 'payment-review-dashboard') {
            $data['dashboardEyebrow'] = $role === 'admin' ? 'Admin' : 'Employee';
            $data['dashboardTitle'] = $role === 'admin' ? 'Review uploaded payments.' : 'Work the payment queue.';
            $data['dashboardLead'] = $role === 'admin'
                ? 'Review uploaded GCash receipts, approve valid payments, or reject invalid ones before deeper order management is added.'
                : 'Employees can review uploaded checkout receipts, approve payments that look valid, and reject problem payments before orders move deeper into the workflow.';
            $data['reviewActionPath'] = '/dashboard/' . $role . '/payments';
        }

        if ($partial === 'order-management-dashboard') {
            $data['orderActionPath'] = '/dashboard/' . $role . '/orders';
        }

        if ($partial === 'reservation-management-dashboard') {
            $data['reservationActionPath'] = '/dashboard/' . $role . '/reservations';
        }

        if ($partial === 'feedback-management-dashboard') {
            $data['feedbackActionPath'] = '/dashboard/' . $role . '/feedback';
        }

        return $this->renderPartial($partial, $data);
    }

    private function renderPartial(string $partial, array $data): string
    {
        $path = __DIR__ . '/../Views/partials/' . $partial . '.php';

        if (!is_file($path)) {
            return '';
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $path;

        return (string) ob_get_clean();
    }

    private function requestedSection(string $default): string
    {
        $section = preg_replace('/[^a-z_-]+/i', '', (string) request_input('section', $default)) ?: $default;

        return strtolower($section);
    }

    private function validatedMenuItemData(): array
    {
        $uploadedImagePath = MenuImageUploader::storeOptional($_FILES['image_file'] ?? null);
        $data = [
            'name' => trim((string) request_input('name')),
            'category' => trim((string) request_input('category')),
            'description' => trim((string) request_input('description')),
            'image_url' => $uploadedImagePath ?? trim((string) request_input('image_url')),
            'is_available' => $this->checked('is_available') ? 1 : 0,
        ];

        $validator = Validator::make($data)
            ->required('name', 'Item name')
            ->max('name', 150, 'Item name')
            ->required('category', 'Category')
            ->required('description', 'Description')
            ->max('description', 500, 'Description')
            ->max('image_url', 255, 'Image path');

        if (!in_array($data['category'], (new MenuRepository())->categories(), true)) {
            $validator->addError('category', 'Select a valid menu category.');
        }

        if ($validator->fails()) {
            throw new \RuntimeException($this->firstValidationError($validator->errors()));
        }

        return $data;
    }

    private function validatedMenuSizeData(): array
    {
        $data = [
            'size_label' => trim((string) request_input('size_label')),
            'price' => trim((string) request_input('price')),
            'sort_order' => (string) request_input('sort_order', '0'),
            'is_default' => $this->checked('is_default') ? 1 : 0,
            'is_available' => $this->checked('size_is_available') ? 1 : 0,
        ];

        $validator = Validator::make($data)
            ->required('size_label', 'Size label')
            ->max('size_label', 50, 'Size label')
            ->required('price', 'Price')
            ->regex('price', '/^\d+(\.\d{1,2})?$/', 'Price must be a valid amount.')
            ->required('sort_order', 'Sort order')
            ->regex('sort_order', '/^\d+$/', 'Sort order must be a whole number.');

        if ($validator->fails()) {
            throw new \RuntimeException($this->firstValidationError($validator->errors()));
        }

        return [
            'size_label' => $data['size_label'],
            'price' => (float) $data['price'],
            'sort_order' => (int) $data['sort_order'],
            'is_default' => $data['is_default'],
            'is_available' => $data['is_available'],
        ];
    }

    private function validatedStaffCreateData(): array
    {
        $users = new UserRepository();
        $data = [
            'first_name' => trim((string) request_input('first_name')),
            'last_name' => trim((string) request_input('last_name')),
            'email' => strtolower(trim((string) request_input('email'))),
            'phone' => preg_replace('/\D+/', '', trim((string) request_input('phone'))) ?: '',
            'password' => (string) request_input('password'),
            'confirm_password' => (string) request_input('confirm_password'),
            'role' => trim((string) request_input('role')),
            'is_active' => $this->checked('is_active') ? 1 : 0,
        ];

        $validator = Validator::make($data)
            ->required('first_name', 'First name')
            ->min('first_name', 2, 'First name')
            ->max('first_name', 50, 'First name')
            ->regex('first_name', "/^[A-Za-z][A-Za-z' -]*$/", 'First name may only contain letters, spaces, apostrophes, and hyphens.')
            ->required('last_name', 'Last name')
            ->min('last_name', 2, 'Last name')
            ->max('last_name', 50, 'Last name')
            ->regex('last_name', "/^[A-Za-z][A-Za-z' -]*$/", 'Last name may only contain letters, spaces, apostrophes, and hyphens.')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->max('email', 100, 'Email')
            ->required('phone', 'Phone number')
            ->regex('phone', '/^09\d{9}$/', 'Phone number must start with 09 and contain 11 digits.')
            ->required('password', 'Password')
            ->min('password', 8, 'Password')
            ->regex('password', '/[A-Z]/', 'Password must include at least one uppercase letter.')
            ->regex('password', '/[a-z]/', 'Password must include at least one lowercase letter.')
            ->regex('password', '/\d/', 'Password must include at least one number.')
            ->required('confirm_password', 'Confirm password')
            ->same('confirm_password', 'password', 'Confirm password', 'Password')
            ->required('role', 'Role')
            ->regex('role', '/^(admin|employee)$/', 'Staff role must be admin or employee.');

        if (!$validator->fails()) {
            if ($users->emailExists($data['email'])) {
                $validator->addError('email', 'This email address is already registered.');
            }

            if ($users->phoneExists($data['phone'])) {
                $validator->addError('phone', 'This phone number is already registered.');
            }
        }

        if ($validator->fails()) {
            throw new \RuntimeException($this->firstValidationError($validator->errors()));
        }

        return $data;
    }

    private function validatedUserUpdateData(int $userId): array
    {
        $users = new UserRepository();
        $data = [
            'first_name' => trim((string) request_input('first_name')),
            'last_name' => trim((string) request_input('last_name')),
            'email' => strtolower(trim((string) request_input('email'))),
            'phone' => preg_replace('/\D+/', '', trim((string) request_input('phone'))) ?: '',
            'role' => trim((string) request_input('role')),
            'is_active' => $this->checked('is_active') ? 1 : 0,
        ];

        $validator = Validator::make($data)
            ->required('first_name', 'First name')
            ->min('first_name', 2, 'First name')
            ->max('first_name', 50, 'First name')
            ->regex('first_name', "/^[A-Za-z][A-Za-z' -]*$/", 'First name may only contain letters, spaces, apostrophes, and hyphens.')
            ->required('last_name', 'Last name')
            ->min('last_name', 2, 'Last name')
            ->max('last_name', 50, 'Last name')
            ->regex('last_name', "/^[A-Za-z][A-Za-z' -]*$/", 'Last name may only contain letters, spaces, apostrophes, and hyphens.')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->max('email', 100, 'Email')
            ->required('phone', 'Phone number')
            ->regex('phone', '/^09\d{9}$/', 'Phone number must start with 09 and contain 11 digits.')
            ->required('role', 'Role')
            ->regex('role', '/^(customer|admin|employee)$/', 'Role must be customer, admin, or employee.');

        if (!$validator->fails()) {
            if ($users->emailExistsExcept($data['email'], $userId)) {
                $validator->addError('email', 'This email address is already registered.');
            }

            if ($users->phoneExistsExcept($data['phone'], $userId)) {
                $validator->addError('phone', 'This phone number is already registered.');
            }
        }

        if ($validator->fails()) {
            throw new \RuntimeException($this->firstValidationError($validator->errors()));
        }

        return $data;
    }

    private function validatedCustomerProfileData(int $userId): array
    {
        $users = new UserRepository();
        $data = [
            'first_name' => trim((string) request_input('first_name')),
            'last_name' => trim((string) request_input('last_name')),
            'email' => strtolower(trim((string) request_input('email'))),
            'phone' => preg_replace('/\D+/', '', trim((string) request_input('phone'))) ?: '',
        ];

        $validator = Validator::make($data)
            ->required('first_name', 'First name')
            ->min('first_name', 2, 'First name')
            ->max('first_name', 50, 'First name')
            ->regex('first_name', "/^[A-Za-z][A-Za-z' -]*$/", 'First name may only contain letters, spaces, apostrophes, and hyphens.')
            ->required('last_name', 'Last name')
            ->min('last_name', 2, 'Last name')
            ->max('last_name', 50, 'Last name')
            ->regex('last_name', "/^[A-Za-z][A-Za-z' -]*$/", 'Last name may only contain letters, spaces, apostrophes, and hyphens.')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->max('email', 100, 'Email')
            ->required('phone', 'Phone number')
            ->regex('phone', '/^09\d{9}$/', 'Phone number must start with 09 and contain 11 digits.');

        if (!$validator->fails()) {
            if ($users->emailExistsExcept($data['email'], $userId)) {
                $validator->addError('email', 'This email address is already registered.');
            }

            if ($users->phoneExistsExcept($data['phone'], $userId)) {
                $validator->addError('phone', 'This phone number is already registered.');
            }
        }

        if ($validator->fails()) {
            throw new \RuntimeException($this->firstValidationError($validator->errors()));
        }

        return $data;
    }

    private function checked(string $field): bool
    {
        return in_array(request_input($field), ['1', 1, true, 'true', 'on', 'yes'], true);
    }

    private function firstValidationError(array $errors): string
    {
        foreach ($errors as $fieldErrors) {
            if (is_array($fieldErrors) && isset($fieldErrors[0])) {
                return (string) $fieldErrors[0];
            }
        }

        return 'Validation failed.';
    }
}
