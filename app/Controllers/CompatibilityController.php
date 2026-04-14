<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\FeedbackRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\UserRepository;
use App\Support\Auth;

final class CompatibilityController extends Controller
{
    public function orders(): never
    {
        $this->requireRole('admin');
        $this->json((new OrderRepository())->directOrdersForJson(50));
    }

    public function orderItems(): never
    {
        $user = $this->requireAuthenticatedUser();
        $orderId = (int) request_input('order_id', 0);

        if ($orderId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid order ID'], 400);
        }

        $userId = in_array((string) ($user['role'] ?? ''), ['admin', 'employee'], true)
            ? null
            : (int) ($user['id'] ?? 0);

        $payload = (new OrderRepository())->findForJson($orderId, $userId);

        if ($payload === null) {
            $status = $userId === null ? 404 : 403;
            $this->json(['success' => false, 'message' => $status === 404 ? 'Order not found' : 'Access denied'], $status);
        }

        $this->json([
            'success' => true,
            'order' => $payload['order'],
            'items' => $payload['items'],
        ]);
    }

    public function reservationOrders(): never
    {
        $this->requireAnyRole(['admin', 'employee']);
        $reservationId = (int) request_input('reservation_id', 0);

        if ($reservationId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid reservation ID'], 400);
        }

        $this->json([
            'success' => true,
            'orders' => (new OrderRepository())->reservationOrdersForJson($reservationId),
        ]);
    }

    public function customerReservationOrders(): never
    {
        $user = $this->requireAuthenticatedUser();
        $reservationId = (int) request_input('reservation_id', 0);

        if ($reservationId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid reservation ID'], 400);
        }

        if (!in_array((string) ($user['role'] ?? ''), ['admin', 'employee'], true)
            && !(new ReservationRepository())->customerOwnsReservation($reservationId, (int) ($user['id'] ?? 0))) {
            $this->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        $userId = in_array((string) ($user['role'] ?? ''), ['admin', 'employee'], true)
            ? null
            : (int) ($user['id'] ?? 0);

        $this->json([
            'success' => true,
            'orders' => (new OrderRepository())->reservationOrdersForJson($reservationId, $userId),
        ]);
    }

    public function feedback(): never
    {
        $this->requireRole('admin');
        $this->json((new FeedbackRepository())->listForJson(20));
    }

    public function customers(): never
    {
        $this->requireRole('admin');
        $status = (string) request_input('status', 'active');

        if (!in_array($status, ['active', 'inactive', 'all'], true)) {
            $status = 'active';
        }

        $this->json([
            'success' => true,
            'customers' => (new UserRepository())->customersForJson($status),
        ]);
    }

    private function requireAuthenticatedUser(): array
    {
        if (!Auth::check()) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return Auth::user() ?? [];
    }

    private function requireRole(string $role): array
    {
        return $this->requireAnyRole([$role]);
    }

    private function requireAnyRole(array $roles): array
    {
        $user = $this->requireAuthenticatedUser();

        if (!in_array((string) ($user['role'] ?? ''), $roles, true)) {
            $this->json(['success' => false, 'error' => 'Unauthorized', 'message' => 'Unauthorized'], 403);
        }

        return $user;
    }

    private function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }
}
