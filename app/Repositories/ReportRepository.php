<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class ReportRepository
{
    private const DEFAULT_DAYS = 7;

    public function overview(): array
    {
        $statement = Database::connection()->query(
            'SELECT
                (SELECT COUNT(*) FROM orders) AS total_orders,
                (SELECT COUNT(*) FROM orders WHERE payment_status = \'verified\') AS verified_orders,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = \'verified\') AS verified_revenue,
                (SELECT COUNT(*) FROM reservations) AS total_reservations,
                (SELECT COUNT(*) FROM users WHERE role = \'customer\') AS total_customers,
                (SELECT COUNT(*) FROM users WHERE role IN (\'admin\', \'employee\') AND is_active = 1) AS active_staff,
                (SELECT COUNT(*) FROM menu_items WHERE is_available = 1) AS live_menu_items,
                (SELECT COUNT(*) FROM menu_items WHERE is_available = 0) AS archived_menu_items,
                (SELECT COUNT(*) FROM feedback WHERE status IN (\'new\', \'in_review\')) AS open_feedback'
        );

        $overview = $statement->fetch();

        return is_array($overview) ? $overview : [];
    }

    public function orderStatusBreakdown(): array
    {
        $statement = Database::connection()->query(
            'SELECT status, COUNT(*) AS total
             FROM orders
             GROUP BY status
             ORDER BY FIELD(status, \'pending\', \'preparing\', \'ready\', \'completed\', \'cancelled\', \'rejected\')'
        );

        return $statement->fetchAll();
    }

    public function reservationStatusBreakdown(): array
    {
        $statement = Database::connection()->query(
            'SELECT status, COUNT(*) AS total
             FROM reservations
             GROUP BY status
             ORDER BY FIELD(status, \'pending\', \'confirmed\', \'completed\', \'cancelled\')'
        );

        return $statement->fetchAll();
    }

    public function dailySales(int $days = self::DEFAULT_DAYS): array
    {
        $days = $this->normalizedDayWindow($days);
        $statement = Database::connection()->prepare(
            "SELECT DATE(created_at) AS report_date, COALESCE(SUM(total_amount), 0) AS total
             FROM orders
             WHERE payment_status = 'verified'
               AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL :offset DAY)
             GROUP BY DATE(created_at)
             ORDER BY report_date ASC"
        );
        $statement->bindValue('offset', $days - 1, \PDO::PARAM_INT);
        $statement->execute();

        return $this->fillDailySeries($statement->fetchAll() ?: [], $days, 'total');
    }

    public function dailySalesForRange(string $startDate, string $endDate): array
    {
        $statement = Database::connection()->prepare(
            "SELECT DATE(created_at) AS report_date, COALESCE(SUM(total_amount), 0) AS total
             FROM orders
             WHERE payment_status = 'verified'
               AND DATE(created_at) BETWEEN :start_date AND :end_date
             GROUP BY DATE(created_at)
             ORDER BY report_date ASC"
        );
        $statement->execute([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return $this->fillDateRangeSeries($statement->fetchAll() ?: [], $startDate, $endDate, 'total');
    }

    public function orderVolumeByDay(int $days = self::DEFAULT_DAYS): array
    {
        $days = $this->normalizedDayWindow($days);
        $statement = Database::connection()->prepare(
            "SELECT DATE(created_at) AS report_date, COUNT(*) AS total
             FROM orders
             WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL :offset DAY)
             GROUP BY DATE(created_at)
             ORDER BY report_date ASC"
        );
        $statement->bindValue('offset', $days - 1, \PDO::PARAM_INT);
        $statement->execute();

        return $this->fillDailySeries($statement->fetchAll() ?: [], $days, 'total');
    }

    public function orderVolumeForRange(string $startDate, string $endDate): array
    {
        $statement = Database::connection()->prepare(
            "SELECT DATE(created_at) AS report_date, COUNT(*) AS total
             FROM orders
             WHERE DATE(created_at) BETWEEN :start_date AND :end_date
             GROUP BY DATE(created_at)
             ORDER BY report_date ASC"
        );
        $statement->execute([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return $this->fillDateRangeSeries($statement->fetchAll() ?: [], $startDate, $endDate, 'total');
    }

    public function reservationVolumeByDay(int $days = self::DEFAULT_DAYS): array
    {
        $days = $this->normalizedDayWindow($days);
        $statement = Database::connection()->prepare(
            "SELECT DATE(date) AS report_date, COUNT(*) AS total
             FROM reservations
             WHERE DATE(date) >= DATE_SUB(CURDATE(), INTERVAL :offset DAY)
             GROUP BY DATE(date)
             ORDER BY report_date ASC"
        );
        $statement->bindValue('offset', $days - 1, \PDO::PARAM_INT);
        $statement->execute();

        return $this->fillDailySeries($statement->fetchAll() ?: [], $days, 'total');
    }

    public function reservationVolumeForRange(string $startDate, string $endDate): array
    {
        $statement = Database::connection()->prepare(
            "SELECT DATE(date) AS report_date, COUNT(*) AS total
             FROM reservations
             WHERE DATE(date) BETWEEN :start_date AND :end_date
             GROUP BY DATE(date)
             ORDER BY report_date ASC"
        );
        $statement->execute([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return $this->fillDateRangeSeries($statement->fetchAll() ?: [], $startDate, $endDate, 'total');
    }

    public function topSellingMenuItems(int $limit = 8): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                mi.name,
                mi.category,
                SUM(oi.quantity) AS total_quantity,
                SUM(oi.subtotal) AS total_revenue
             FROM order_items oi
             INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
             INNER JOIN orders o ON o.id = oi.order_id
             WHERE o.status NOT IN ('cancelled', 'rejected')
               AND o.payment_status = 'verified'
             GROUP BY mi.id, mi.name, mi.category
             ORDER BY total_quantity DESC, total_revenue DESC
             LIMIT :limit"
        );
        $statement->bindValue('limit', max(1, $limit), \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function topSellingMenuItemsForRange(string $startDate, string $endDate, int $limit = 8): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                mi.name,
                mi.category,
                SUM(oi.quantity) AS total_quantity,
                SUM(oi.subtotal) AS total_revenue
             FROM order_items oi
             INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
             INNER JOIN orders o ON o.id = oi.order_id
             WHERE o.status NOT IN ('cancelled', 'rejected')
               AND o.payment_status = 'verified'
               AND DATE(o.created_at) BETWEEN :start_date AND :end_date
             GROUP BY mi.id, mi.name, mi.category
             ORDER BY total_quantity DESC, total_revenue DESC
             LIMIT :limit"
        );
        $statement->bindValue('start_date', $startDate);
        $statement->bindValue('end_date', $endDate);
        $statement->bindValue('limit', max(1, $limit), \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function rangeSummary(string $startDate, string $endDate): array
    {
        $statement = Database::connection()->prepare(
            "SELECT
                (SELECT COUNT(*) FROM orders WHERE DATE(created_at) BETWEEN :start_a AND :end_a) AS total_orders,
                (SELECT COUNT(*) FROM orders WHERE payment_status = 'verified' AND DATE(created_at) BETWEEN :start_b AND :end_b) AS verified_orders,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'verified' AND DATE(created_at) BETWEEN :start_c AND :end_c) AS verified_revenue,
                (SELECT COUNT(*) FROM reservations WHERE DATE(date) BETWEEN :start_d AND :end_d) AS total_reservations,
                (SELECT COUNT(*) FROM feedback WHERE DATE(created_at) BETWEEN :start_e AND :end_e) AS feedback_received"
        );
        $statement->execute([
            'start_a' => $startDate,
            'end_a' => $endDate,
            'start_b' => $startDate,
            'end_b' => $endDate,
            'start_c' => $startDate,
            'end_c' => $endDate,
            'start_d' => $startDate,
            'end_d' => $endDate,
            'start_e' => $startDate,
            'end_e' => $endDate,
        ]);

        $summary = $statement->fetch();

        return is_array($summary) ? $summary : [];
    }

    public function orderDrilldown(string $startDate, string $endDate, int $limit = 20): array
    {
        $statement = Database::connection()->prepare(
            "SELECT id, order_number, total_amount, status, payment_status, created_at
             FROM orders
             WHERE DATE(created_at) BETWEEN :start_date AND :end_date
             ORDER BY created_at DESC
             LIMIT :limit"
        );
        $statement->bindValue('start_date', $startDate);
        $statement->bindValue('end_date', $endDate);
        $statement->bindValue('limit', max(1, min(100, $limit)), \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function reservationDrilldown(string $startDate, string $endDate, int $limit = 20): array
    {
        $statement = Database::connection()->prepare(
            "SELECT id, first_name, last_name, guests, status, date, time
             FROM reservations
             WHERE DATE(date) BETWEEN :start_date AND :end_date
             ORDER BY date DESC, time DESC
             LIMIT :limit"
        );
        $statement->bindValue('start_date', $startDate);
        $statement->bindValue('end_date', $endDate);
        $statement->bindValue('limit', max(1, min(100, $limit)), \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    private function normalizedDayWindow(int $days): int
    {
        return max(1, min(31, $days));
    }

    private function fillDailySeries(array $rows, int $days, string $valueKey): array
    {
        $valuesByDate = [];

        foreach ($rows as $row) {
            $date = (string) ($row['report_date'] ?? '');

            if ($date !== '') {
                $valuesByDate[$date] = (float) ($row[$valueKey] ?? 0);
            }
        }

        $series = [];
        $start = new \DateTimeImmutable('-' . ($days - 1) . ' days');

        for ($index = 0; $index < $days; $index++) {
            $date = $start->modify('+' . $index . ' days')->format('Y-m-d');
            $series[] = [
                'date' => $date,
                'label' => date('M d', strtotime($date)),
                'total' => $valuesByDate[$date] ?? 0,
            ];
        }

        return $series;
    }

    private function fillDateRangeSeries(array $rows, string $startDate, string $endDate, string $valueKey): array
    {
        $valuesByDate = [];

        foreach ($rows as $row) {
            $date = (string) ($row['report_date'] ?? '');

            if ($date !== '') {
                $valuesByDate[$date] = (float) ($row[$valueKey] ?? 0);
            }
        }

        $start = new \DateTimeImmutable($startDate);
        $end = new \DateTimeImmutable($endDate);
        $series = [];

        for ($date = $start; $date <= $end; $date = $date->modify('+1 day')) {
            $key = $date->format('Y-m-d');
            $series[] = [
                'date' => $key,
                'label' => $date->format('M d'),
                'total' => $valuesByDate[$key] ?? 0,
            ];
        }

        return $series;
    }
}
