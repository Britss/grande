<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ReservationRepository;
use App\Support\Config;
use App\Support\Mailer;

final class StatusNotificationService
{
    private const ORDER_EMAIL_STATUSES = ['ready', 'completed', 'cancelled', 'rejected'];
    private const RESERVATION_EMAIL_STATUSES = ['confirmed', 'completed', 'cancelled'];

    public function __construct(
        private readonly Mailer $mailer = new Mailer(),
        private readonly OrderRepository $orders = new OrderRepository(),
        private readonly ReservationRepository $reservations = new ReservationRepository(),
    ) {
    }

    public function sendOrderStatusUpdate(int $orderId): void
    {
        $order = $this->orders->findForStatusEmail($orderId);

        if (!is_array($order) || !in_array((string) ($order['status'] ?? ''), self::ORDER_EMAIL_STATUSES, true)) {
            return;
        }

        $email = trim((string) ($order['email'] ?? ''));

        if ($email === '') {
            return;
        }

        $status = (string) $order['status'];
        $orderNumber = (string) ($order['order_number'] ?? ('#' . $orderId));
        $subject = 'Grande order ' . $orderNumber . ' is ' . $this->humanStatus($status);
        $plainText = $this->orderPlainText($order);
        $htmlBody = $this->wrapHtml(
            'Your order is ' . $this->humanStatus($status) . '.',
            nl2br(htmlspecialchars($plainText, ENT_QUOTES, 'UTF-8'))
        );

        $this->sendNonBlocking($email, $subject, $htmlBody, $plainText);
    }

    public function sendReservationStatusUpdate(int $reservationId): void
    {
        $reservation = $this->reservations->findForStatusEmail($reservationId);

        if (!is_array($reservation) || !in_array((string) ($reservation['status'] ?? ''), self::RESERVATION_EMAIL_STATUSES, true)) {
            return;
        }

        $email = trim((string) ($reservation['email'] ?? ''));

        if ($email === '') {
            return;
        }

        $status = (string) $reservation['status'];
        $subject = 'Grande reservation #' . $reservationId . ' is ' . $this->humanStatus($status);
        $plainText = $this->reservationPlainText($reservation);
        $htmlBody = $this->wrapHtml(
            'Your reservation is ' . $this->humanStatus($status) . '.',
            nl2br(htmlspecialchars($plainText, ENT_QUOTES, 'UTF-8'))
        );

        $this->sendNonBlocking($email, $subject, $htmlBody, $plainText);
    }

    private function sendNonBlocking(string $email, string $subject, string $htmlBody, string $plainText): void
    {
        try {
            $this->mailer->send($email, $subject, $htmlBody, $plainText);
        } catch (\Throwable $exception) {
            error_log('Status email failed: ' . $exception->getMessage());
        }
    }

    private function orderPlainText(array $order): string
    {
        $lines = [
            'Grande order update',
            '',
            'Order: ' . (string) ($order['order_number'] ?? ''),
            'Status: ' . $this->humanStatus((string) ($order['status'] ?? 'pending')),
            'Payment: ' . $this->humanStatus((string) ($order['payment_status'] ?? 'pending')),
            'Total: PHP ' . number_format((float) ($order['total_amount'] ?? 0), 2),
        ];

        if (!empty($order['reservation_date'])) {
            $lines[] = 'Reservation: ' . (string) $order['reservation_date'] . ' ' . (string) ($order['reservation_time'] ?? '');
        }

        $lines[] = '';
        $lines[] = 'Items:';

        foreach (($order['items'] ?? []) as $item) {
            $lines[] = sprintf(
                '- %sx %s%s - PHP %s',
                (int) ($item['quantity'] ?? 0),
                (string) ($item['menu_item_name'] ?? 'Menu item'),
                !empty($item['size']) ? ' (' . (string) $item['size'] . ')' : '',
                number_format((float) ($item['subtotal'] ?? 0), 2)
            );
        }

        if (($order['status'] ?? '') === 'completed') {
            $lines[] = '';
            $lines[] = 'Receipt summary: this completed order has been paid and fulfilled by Grande.';
        }

        $lines[] = '';
        $lines[] = 'Questions? Contact ' . (string) Config::get('app.site_email') . '.';

        return implode(PHP_EOL, $lines);
    }

    private function reservationPlainText(array $reservation): string
    {
        $lines = [
            'Grande reservation update',
            '',
            'Reservation: #' . (string) ($reservation['id'] ?? ''),
            'Status: ' . $this->humanStatus((string) ($reservation['status'] ?? 'pending')),
            'Guest: ' . trim((string) ($reservation['first_name'] ?? '') . ' ' . (string) ($reservation['last_name'] ?? '')),
            'Date: ' . (string) ($reservation['date'] ?? ''),
            'Time: ' . (string) ($reservation['time'] ?? ''),
            'Guests: ' . (string) ($reservation['guests'] ?? ''),
        ];

        $orders = $reservation['orders'] ?? [];

        if (is_array($orders) && $orders !== []) {
            $lines[] = '';
            $lines[] = 'Linked orders:';

            foreach ($orders as $order) {
                $lines[] = sprintf(
                    '- %s: %s, PHP %s',
                    (string) ($order['order_number'] ?? 'Order'),
                    $this->humanStatus((string) ($order['status'] ?? 'pending')),
                    number_format((float) ($order['total_amount'] ?? 0), 2)
                );
            }
        }

        if (($reservation['status'] ?? '') === 'completed') {
            $lines[] = '';
            $lines[] = 'Reservation summary: this completed reservation has been served by Grande.';
        }

        $lines[] = '';
        $lines[] = 'Questions? Contact ' . (string) Config::get('app.site_email') . '.';

        return implode(PHP_EOL, $lines);
    }

    private function wrapHtml(string $heading, string $body): string
    {
        return '<h1>' . htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') . '</h1><p>' . $body . '</p>';
    }

    private function humanStatus(string $status): string
    {
        return ucwords(str_replace('_', ' ', $status));
    }
}
