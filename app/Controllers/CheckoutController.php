<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\ReceiptUploader;
use App\Support\Session;
use App\Support\Validator;

final class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartRepository $cartRepository = new CartRepository(),
        private readonly OrderRepository $orderRepository = new OrderRepository(),
    ) {
    }

    public function show(): string
    {
        $user = $this->requireCustomer();
        $cartItems = $this->cartRepository->itemsForUser((int) $user['id']);

        if ($cartItems === []) {
            Session::flash('error', 'Your cart is empty.');
            redirect('/menu');
        }

        return $this->render('pages.checkout', [
            'pageTitle' => 'Checkout',
            'metaDescription' => 'Complete your order at Grande. Pandesal + Coffee.',
            'bodyClass' => 'checkout-page',
            'user' => $user,
            'cartItems' => $cartItems,
            'cartTotals' => $this->cartRepository->totalsForUser((int) $user['id']),
        ]);
    }

    public function store(): never
    {
        $user = $this->requireCustomer();

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            Session::flashInput($_POST);
            redirect('/checkout');
        }

        $cartItems = $this->cartRepository->itemsForUser((int) $user['id']);

        if ($cartItems === []) {
            Session::flash('error', 'Your cart is empty.');
            redirect('/menu');
        }

        $input = [
            'first_name' => trim((string) request_input('first_name')),
            'last_name' => trim((string) request_input('last_name')),
            'email' => strtolower(trim((string) request_input('email'))),
            'phone' => preg_replace('/\D+/', '', trim((string) request_input('phone'))) ?: '',
            'order_type' => trim((string) request_input('order_type', 'togo')),
            'ready_time' => trim((string) request_input('ready_time')),
            'guest_count' => trim((string) request_input('guest_count')),
            'payment_method' => trim((string) request_input('payment_method', 'gcash')),
        ];

        $validator = $this->baseCheckoutValidator($input);

        if (!in_array($input['order_type'], ['togo', 'dinein'], true)) {
            $validator->addError('order_type', 'Choose a valid order type.');
        }

        if ($input['payment_method'] !== 'gcash') {
            $validator->addError('payment_method', 'Choose a valid payment method.');
        }

        if ($input['order_type'] === 'togo') {
            if ($input['ready_time'] === '' || preg_match('/^\d{2}:\d{2}$/', $input['ready_time']) !== 1) {
                $validator->addError('ready_time', 'Choose a ready time for your to-go order.');
            }
        }

        if ($input['order_type'] === 'dinein') {
            if ($input['guest_count'] === '' || preg_match('/^\d+$/', $input['guest_count']) !== 1) {
                $validator->addError('guest_count', 'Enter the number of guests for dine-in.');
            } elseif ((int) $input['guest_count'] < 1 || (int) $input['guest_count'] > 20) {
                $validator->addError('guest_count', 'Guest count must be between 1 and 20.');
            }
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($input);
            redirect('/checkout');
        }

        $receiptFilename = null;

        try {
            $receiptFilename = ReceiptUploader::validateAndStore($_FILES['receipt_image'] ?? null);
            $order = $this->orderRepository->createFromCart((int) $user['id'], $cartItems, null, $receiptFilename);
        } catch (\Throwable $exception) {
            ReceiptUploader::deleteIfExists($receiptFilename);
            Session::flash('error', $exception->getMessage());
            Session::flashInput($input);
            redirect('/checkout');
        }

        Session::flash(
            'status',
            sprintf('Order %s placed successfully. Total: PHP %s.', $order['order_number'], number_format((float) $order['total_amount'], 2))
        );
        redirect('/dashboard/customer');
    }

    public function showReservation(): string
    {
        $user = $this->requireCustomer();
        $reservation = Session::get('reservation.pending_checkout');

        if (!is_array($reservation)) {
            Session::flash('error', 'Create a reservation first before checking out.');
            redirect('/reserve');
        }

        $cartItems = $this->cartRepository->itemsForUser((int) $user['id']);

        if ($cartItems === []) {
            Session::flash('error', 'Your cart is empty.');
            redirect('/menu');
        }

        return $this->render('pages.reservation-checkout', [
            'pageTitle' => 'Reservation Checkout',
            'metaDescription' => 'Complete your reservation order at Grande. Pandesal + Coffee.',
            'bodyClass' => 'checkout-page reservation-checkout-page',
            'user' => $user,
            'reservation' => $reservation,
            'cartItems' => $cartItems,
            'cartTotals' => $this->cartRepository->totalsForUser((int) $user['id']),
        ]);
    }

    public function storeReservation(): never
    {
        $user = $this->requireCustomer();

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            redirect('/reservation-checkout');
        }

        $reservation = Session::get('reservation.pending_checkout');

        if (!is_array($reservation)) {
            Session::flash('error', 'Create a reservation first before checking out.');
            redirect('/reserve');
        }

        $cartItems = $this->cartRepository->itemsForUser((int) $user['id']);

        if ($cartItems === []) {
            Session::flash('error', 'Your cart is empty.');
            redirect('/menu');
        }

        $receiptFilename = null;

        try {
            $receiptFilename = ReceiptUploader::validateAndStore($_FILES['receipt_image'] ?? null);
            $order = $this->orderRepository->createReservationOrderFromCart(
                (int) $user['id'],
                $reservation,
                $cartItems,
                $receiptFilename
            );
        } catch (\Throwable $exception) {
            ReceiptUploader::deleteIfExists($receiptFilename);
            Session::flash('error', $exception->getMessage());
            redirect('/reservation-checkout');
        }

        Session::forget('reservation.pending_checkout');

        Session::flash(
            'status',
            sprintf(
                'Reservation order %s placed successfully. Total: PHP %s.',
                $order['order_number'],
                number_format((float) $order['total_amount'], 2)
            )
        );
        redirect('/dashboard/customer');
    }

    private function baseCheckoutValidator(array $input): Validator
    {
        return Validator::make($input)
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
            ->required('phone', 'Phone number')
            ->regex('phone', '/^09\d{9}$/', 'Phone number must start with 09 and contain 11 digits.');
    }

    private function requireCustomer(): array
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please log in to continue.');
            redirect('/login');
        }

        $user = Auth::user();

        if (!is_array($user)) {
            redirect('/login');
        }

        if (($user['role'] ?? 'customer') !== 'customer') {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        return $user;
    }
}
