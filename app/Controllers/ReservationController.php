<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CartRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\PublicContent;
use App\Support\Session;
use App\Support\Validator;

final class ReservationController extends Controller
{
    public function __construct(
        private readonly CartRepository $cartRepository = new CartRepository(),
    ) {
    }

    public function show(): string
    {
        $page = PublicContent::reserve();
        $user = Auth::user();
        $cartItems = [];
        $cartTotals = ['item_count' => 0, 'subtotal' => 0.0];

        if (is_array($user) && ($user['role'] ?? 'customer') === 'customer') {
            $cartItems = $this->cartRepository->itemsForUser((int) $user['id']);
            $cartTotals = $this->cartRepository->totalsForUser((int) $user['id']);
        }

        return $this->render('pages.reserve', [
            'pageTitle' => $page['title'],
            'metaDescription' => $page['description'],
            'bodyClass' => 'reserve-page',
            'page' => $page,
            'cartItems' => $cartItems,
            'cartTotals' => $cartTotals,
            'user' => $user,
        ]);
    }

    public function store(): never
    {
        $user = $this->requireCustomer();

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            Session::flashInput($_POST);
            redirect('/reserve');
        }

        $cartItems = $this->cartRepository->itemsForUser((int) $user['id']);

        if ($cartItems === []) {
            Session::flash('error', 'Add at least one item to your pre-order before reserving.');
            redirect('/menu');
        }

        $input = [
            'date' => trim((string) request_input('date')),
            'time' => trim((string) request_input('time')),
            'guests' => trim((string) request_input('guests', '1')),
            'first_name' => trim((string) request_input('first_name')),
            'last_name' => trim((string) request_input('last_name')),
            'email' => strtolower(trim((string) request_input('email'))),
            'phone' => preg_replace('/\D+/', '', trim((string) request_input('phone'))) ?: '',
        ];

        $validator = Validator::make($input)
            ->required('date', 'Date')
            ->regex('date', '/^\d{4}-\d{2}-\d{2}$/', 'Choose a valid reservation date.')
            ->required('time', 'Time')
            ->regex('time', '/^\d{2}:\d{2}$/', 'Choose a valid reservation time.')
            ->required('guests', 'Number of guests')
            ->regex('guests', '/^\d+$/', 'Number of guests must be a whole number.')
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

        $guests = (int) $input['guests'];

        if ($guests < 1 || $guests > 20) {
            $validator->addError('guests', 'Number of guests must be between 1 and 20.');
        }

        $reservationTimestamp = strtotime($input['date'] . ' ' . $input['time']);

        if ($reservationTimestamp === false) {
            $validator->addError('time', 'Choose a valid reservation date and time.');
        } elseif ($reservationTimestamp <= time()) {
            $validator->addError('time', 'Please choose a reservation time later than the current time.');
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($input);
            redirect('/reserve');
        }

        $reservation = [
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'name' => trim($input['first_name'] . ' ' . $input['last_name']),
            'email' => $input['email'],
            'phone' => $input['phone'],
            'date' => $input['date'],
            'time' => $input['time'],
            'guests' => $guests,
        ];

        Session::put('reservation.pending_checkout', $reservation);

        Session::flash('status', 'Continue to reservation checkout to place your reservation order.');
        redirect('/reservation-checkout');
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
