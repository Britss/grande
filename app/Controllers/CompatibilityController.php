<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\FeedbackRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReportRepository;
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

    public function salesChartData(): never
    {
        $this->requireRole('admin');
        $period = (string) request_input('period', '7days');
        $reports = new ReportRepository();

        if ($period === 'today') {
            $series = $reports->salesByHourToday();
        } elseif ($period === '30days') {
            $series = $reports->dailySales(30);
        } else {
            $period = '7days';
            $series = $reports->dailySales(7);
        }

        $barChart = $this->chartSeriesPayload($series);
        $categoryRows = $reports->salesByCategory();
        $pieChart = [
            'labels' => array_map(static fn (array $row): string => (string) ($row['category'] ?? 'Uncategorized'), $categoryRows),
            'data' => array_map(static fn (array $row): float => (float) ($row['total_revenue'] ?? 0), $categoryRows),
        ];

        if ($barChart['data'] === [] || array_sum($barChart['data']) <= 0) {
            $barChart = ['labels' => ['No Data'], 'data' => [0]];
        }

        if ($pieChart['data'] === []) {
            $pieChart = ['labels' => ['No Sales Yet'], 'data' => [1]];
        }

        $this->json([
            'success' => true,
            'period' => $period,
            'bar_chart' => $barChart,
            'pie_chart' => $pieChart,
        ]);
    }

    public function salesReport(): never
    {
        $this->requireRole('admin');
        $startDate = (string) request_input('start_date', date('Y-m-01'));
        $endDate = (string) request_input('end_date', date('Y-m-d'));

        if (!$this->isDateInput($startDate) || !$this->isDateInput($endDate)) {
            $this->json(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD'], 400);
        }

        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $reports = new ReportRepository();
        $summary = $reports->rangeSummary($startDate, $endDate);
        $dailySales = $reports->dailySalesForRange($startDate, $endDate);
        $orderVolume = $reports->orderVolumeForRange($startDate, $endDate);

        $this->json([
            'success' => true,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => [
                'total_orders' => (int) ($summary['total_orders'] ?? 0),
                'total_revenue' => (float) ($summary['verified_revenue'] ?? 0),
                'average_order_value' => $this->averageOrderValue($summary),
            ],
            'by_status' => $reports->orderStatusBreakdownForRange($startDate, $endDate),
            'daily_sales' => $this->dailySalesReportPayload($dailySales, $orderVolume),
            'top_items' => $this->topItemsReportPayload($reports->topSellingMenuItemsForRange($startDate, $endDate, 10)),
            'by_category' => $this->categoryReportPayload($reports->salesByCategory($startDate, $endDate)),
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

    private function chartSeriesPayload(array $series): array
    {
        return [
            'labels' => array_map(static fn (array $row): string => (string) ($row['label'] ?? ''), $series),
            'data' => array_map(static fn (array $row): float => (float) ($row['total'] ?? 0), $series),
        ];
    }

    private function isDateInput(string $date): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsed instanceof \DateTimeImmutable && $parsed->format('Y-m-d') === $date;
    }

    private function averageOrderValue(array $summary): float
    {
        $orders = (int) ($summary['verified_orders'] ?? 0);

        if ($orders <= 0) {
            return 0.0;
        }

        return round((float) ($summary['verified_revenue'] ?? 0) / $orders, 2);
    }

    private function dailySalesReportPayload(array $salesSeries, array $orderSeries): array
    {
        $ordersByDate = [];

        foreach ($orderSeries as $row) {
            $ordersByDate[(string) ($row['date'] ?? '')] = (int) ($row['total'] ?? 0);
        }

        return array_map(static function (array $row) use ($ordersByDate): array {
            $date = (string) ($row['date'] ?? '');

            return [
                'date' => $date,
                'orders' => $ordersByDate[$date] ?? 0,
                'revenue' => (float) ($row['total'] ?? 0),
            ];
        }, $salesSeries);
    }

    private function topItemsReportPayload(array $rows): array
    {
        return array_map(static fn (array $row): array => [
            'name' => (string) ($row['name'] ?? ''),
            'category' => (string) ($row['category'] ?? ''),
            'quantity' => (int) ($row['total_quantity'] ?? 0),
            'revenue' => (float) ($row['total_revenue'] ?? 0),
        ], $rows);
    }

    private function categoryReportPayload(array $rows): array
    {
        return array_map(static fn (array $row): array => [
            'category' => (string) ($row['category'] ?? ''),
            'quantity' => (int) ($row['total_quantity'] ?? 0),
            'revenue' => (float) ($row['total_revenue'] ?? 0),
        ], $rows);
    }
}
