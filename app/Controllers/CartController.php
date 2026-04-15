<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CartRepository;
use App\Repositories\MenuRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Session;
use App\Support\Validator;

final class CartController extends Controller
{
    public function __construct(
        private readonly CartRepository $cartRepository = new CartRepository(),
        private readonly MenuRepository $menuRepository = new MenuRepository(),
    ) {
    }

    public function show(): string
    {
        $user = $this->requireCustomer();
        $fromReservation = request_input('from') === 'reservation';

        return $this->render('pages.cart', [
            'pageTitle' => 'Your Cart',
            'metaDescription' => 'Review your selected items before checkout.',
            'bodyClass' => 'cart-page',
            'user' => $user,
            'fromReservation' => $fromReservation,
            'cartItems' => $this->cartRepository->itemsForUser((int) $user['id']),
            'cartTotals' => $this->cartRepository->totalsForUser((int) $user['id']),
        ]);
    }

    public function add(): never
    {
        $user = $this->requireCustomer();

        if (!Csrf::validate((string) request_input('_token'))) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Your session expired. Please try again.',
                    'csrfToken' => Csrf::token(),
                ], 419);
            }

            Session::flash('error', 'Your session expired. Please try again.');
            redirect('/menu');
        }

        $input = [
            'size_id' => (string) request_input('size_id'),
            'quantity' => (string) request_input('quantity', '1'),
        ];

        $validator = Validator::make($input)
            ->required('size_id', 'Menu size')
            ->regex('size_id', '/^\d+$/', 'Choose a valid menu size.')
            ->required('quantity', 'Quantity')
            ->regex('quantity', '/^\d+$/', 'Quantity must be a whole number.');

        if ($validator->fails()) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Choose a menu size and quantity before adding to your cart.',
                    'csrfToken' => Csrf::token(),
                ], 422);
            }

            Session::flash('error', 'Choose a menu size and quantity before adding to your cart.');
            redirect('/menu');
        }

        $quantity = max(1, min(20, (int) $input['quantity']));
        $size = $this->menuRepository->findAvailableSizeById((int) $input['size_id']);

        if ($size === null) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'That menu option is no longer available.',
                    'csrfToken' => Csrf::token(),
                ], 404);
            }

            Session::flash('error', 'That menu option is no longer available.');
            redirect('/menu');
        }

        $this->cartRepository->addOrIncrement((int) $user['id'], $size, $quantity);
        $message = sprintf('%s (%s) added to your cart.', $size['item_name'], $size['size_label']);

        if ($this->isAjaxRequest()) {
            $this->jsonResponse([
                'success' => true,
                'message' => $message,
                'cartTotals' => $this->cartRepository->totalsForUser((int) $user['id']),
                'csrfToken' => Csrf::token(),
            ]);
        }

        Session::flash('status', $message);
        redirect('/menu');
    }

    public function update(): never
    {
        $user = $this->requireCustomer();
        $redirectTo = $this->normalizeRedirectTarget((string) request_input('redirect_to', '/cart'));

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            redirect($redirectTo);
        }

        $cartItemId = (int) request_input('cart_item_id', 0);
        $quantity = (int) request_input('quantity', 1);

        if ($cartItemId <= 0) {
            Session::flash('error', 'Cart item could not be updated.');
            redirect($redirectTo);
        }

        if ($quantity <= 0) {
            $this->cartRepository->remove((int) $user['id'], $cartItemId);
            Session::flash('status', 'Item removed from your cart.');
            redirect($redirectTo);
        }

        $this->cartRepository->updateQuantity((int) $user['id'], $cartItemId, min($quantity, 20));
        Session::flash('status', 'Cart quantity updated.');
        redirect($redirectTo);
    }

    public function remove(): never
    {
        $user = $this->requireCustomer();
        $redirectTo = $this->normalizeRedirectTarget((string) request_input('redirect_to', '/cart'));

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            redirect($redirectTo);
        }

        $cartItemId = (int) request_input('cart_item_id', 0);

        if ($cartItemId > 0) {
            $this->cartRepository->remove((int) $user['id'], $cartItemId);
            Session::flash('status', 'Item removed from your cart.');
        }

        redirect($redirectTo);
    }

    private function normalizeRedirectTarget(string $target): string
    {
        $target = trim($target);

        if ($target === '' || $target[0] !== '/') {
            return '/cart';
        }

        foreach (['/cart', '/reserve', '/menu', '/checkout', '/reservation-checkout'] as $allowedPrefix) {
            if (str_starts_with($target, $allowedPrefix)) {
                return $target;
            }
        }

        return '/cart';
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

    private function isAjaxRequest(): bool
    {
        $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
        $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));

        return $requestedWith === 'xmlhttprequest' || str_contains($accept, 'application/json');
    }

    private function jsonResponse(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }
}
