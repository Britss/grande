<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\PasswordResetService;
use App\Services\SignupVerificationService;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Session;
use App\Support\Validator;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly SignupVerificationService $signupVerification = new SignupVerificationService(),
        private readonly PasswordResetService $passwordReset = new PasswordResetService(),
    ) {
    }

    public function login(): string
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        return $this->render('pages.auth.login', [
            'pageTitle' => 'Login',
            'metaDescription' => 'Log in to your Grande account to manage orders and reservations.',
            'bodyClass' => 'auth-page',
        ]);
    }

    public function signup(): string
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        return $this->render('pages.auth.signup', [
            'pageTitle' => 'Sign Up',
            'metaDescription' => 'Create your Grande account for orders, reservations, and account access.',
            'bodyClass' => 'auth-page',
        ]);
    }

    public function forgotPassword(): string
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        return $this->render('pages.auth.forgot-password', [
            'pageTitle' => 'Forgot Password',
            'metaDescription' => 'Request a secure password reset link for your Grande account.',
            'bodyClass' => 'auth-page',
        ]);
    }

    public function resetPassword(): string
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        $token = trim((string) request_input('token'));

        if ($this->passwordReset->validateToken($token) === null) {
            Session::flash('error', 'This password reset link is invalid or expired. Request a new link to continue.');
            redirect('/password/forgot');
        }

        return $this->render('pages.auth.reset-password', [
            'pageTitle' => 'Reset Password',
            'metaDescription' => 'Set a new password for your Grande account.',
            'bodyClass' => 'auth-page',
            'token' => $token,
        ]);
    }

    public function verifySignup(): string
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        $email = strtolower(trim((string) request_input('email', old('email'))));

        return $this->render('pages.auth.verify-signup', [
            'pageTitle' => 'Verify Email',
            'metaDescription' => 'Verify your email before creating your Grande account.',
            'bodyClass' => 'auth-page',
            'email' => $email,
        ]);
    }

    public function authenticate(): never
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            Session::flashInput($_POST);
            redirect('/login');
        }

        $credentials = [
            'email' => trim((string) request_input('email')),
            'password' => (string) request_input('password'),
        ];

        $validator = Validator::make($credentials)
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->required('password', 'Password');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($credentials, ['password']);
            redirect('/login');
        }

        $result = $this->auth->attemptLogin($credentials['email'], $credentials['password']);

        if (($result['success'] ?? false) !== true) {
            Session::flash('error', $result['message'] ?? 'Login failed.');
            Session::flashInput($credentials, ['password']);
            redirect('/login');
        }

        Auth::login($result['user']);
        Session::flash('status', sprintf('Welcome back, %s.', $result['user']['first_name'] ?? 'customer'));
        redirect(Auth::dashboardPathForCurrentUser());
    }

    public function register(): never
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            Session::flashInput($_POST);
            redirect('/signup');
        }

        $input = [
            'first_name' => trim((string) request_input('first_name')),
            'last_name' => trim((string) request_input('last_name')),
            'email' => strtolower(trim((string) request_input('email'))),
            'phone' => preg_replace('/\D+/', '', trim((string) request_input('phone'))) ?: '',
            'password' => (string) request_input('password'),
            'confirm_password' => (string) request_input('confirm_password'),
            'terms' => request_input('terms'),
        ];

        $validator = Validator::make($input)
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
            ->accepted('terms', 'Terms & Conditions and Privacy Policy');

        if (!$validator->fails()) {
            if ($this->auth->emailExists($input['email'])) {
                $validator->addError('email', 'This email address is already registered.');
            }

            if ($this->auth->phoneExists($input['phone'])) {
                $validator->addError('phone', 'This phone number is already registered.');
            }
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($input);
            redirect('/signup');
        }

        $delivery = $this->signupVerification->begin($input);
        Session::flash('status', 'Verification code sent. Enter it below to finish creating your account.');

        if (in_array(($delivery['channel'] ?? ''), ['log', 'local_preview'], true) && isset($delivery['path'])) {
            Session::flash('info', 'Email delivery is not fully configured. Please check your mail settings and resend the code.');
        }

        $this->redirectToVerificationPage($input['email']);
    }

    public function sendPasswordReset(): never
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            Session::flashInput($_POST);
            redirect('/password/forgot');
        }

        $email = strtolower(trim((string) request_input('email')));
        $validator = Validator::make(['email' => $email])
            ->required('email', 'Email')
            ->email('email', 'Email');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput(['email' => $email]);
            redirect('/password/forgot');
        }

        $result = $this->passwordReset->request($email);

        Session::flash('status', 'If that email is registered, a password reset link has been sent.');

        $delivery = $result['delivery'] ?? null;

        if (is_array($delivery) && in_array(($delivery['channel'] ?? ''), ['log', 'local_preview'], true) && isset($delivery['path'])) {
            Session::flash('info', 'Email delivery is not fully configured. Please check your mail settings and request a new reset link.');
        }

        redirect('/password/forgot');
    }

    public function updatePassword(): never
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        $token = trim((string) request_input('token'));

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            $this->redirectToPasswordResetPage($token);
        }

        $input = [
            'token' => $token,
            'password' => (string) request_input('password'),
            'confirm_password' => (string) request_input('confirm_password'),
        ];

        $validator = Validator::make($input)
            ->required('token', 'Reset token')
            ->required('password', 'Password')
            ->min('password', 8, 'Password')
            ->regex('password', '/[A-Z]/', 'Password must include at least one uppercase letter.')
            ->regex('password', '/[a-z]/', 'Password must include at least one lowercase letter.')
            ->regex('password', '/\d/', 'Password must include at least one number.')
            ->required('confirm_password', 'Confirm password')
            ->same('confirm_password', 'password', 'Confirm password', 'Password');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            $this->redirectToPasswordResetPage($token);
        }

        $result = $this->passwordReset->reset($token, $input['password']);

        if (($result['success'] ?? false) !== true) {
            Session::flash('error', $result['message'] ?? 'Password reset failed.');
            redirect('/password/forgot');
        }

        Session::flash('status', 'Password updated. You can now log in with your new password.');
        redirect('/login');
    }

    public function confirmSignupVerification(): never
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            $this->redirectToVerificationPage((string) request_input('email'));
        }

        $email = strtolower(trim((string) request_input('email')));
        $code = preg_replace('/\D+/', '', (string) request_input('verification_code')) ?: '';

        $validator = Validator::make([
            'email' => $email,
            'verification_code' => $code,
        ])
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->required('verification_code', 'Verification code')
            ->regex('verification_code', '/^\d{6}$/', 'Verification code must contain 6 digits.');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput([
                'email' => $email,
                'verification_code' => $code,
            ], ['_token']);
            $this->redirectToVerificationPage($email);
        }

        $result = $this->signupVerification->verify($email, $code);

        if (($result['success'] ?? false) !== true) {
            Session::flash('error', $result['message'] ?? 'Verification failed.');
            Session::flashInput([
                'email' => $email,
                'verification_code' => $code,
            ], ['_token']);
            $this->redirectToVerificationPage($email);
        }

        Session::flash('status', 'Email verified. Your account has been created and is ready for login.');
        redirect('/login');
    }

    public function resendSignupVerification(): never
    {
        if (Auth::check()) {
            redirect(Auth::dashboardPathForCurrentUser());
        }

        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try again.');
            $this->redirectToVerificationPage((string) request_input('email'));
        }

        $email = strtolower(trim((string) request_input('email')));

        if ($email === '') {
            Session::flash('error', 'Email is required before a new verification code can be sent.');
            redirect('/signup');
        }

        $delivery = $this->signupVerification->resend($email);

        if ($delivery === null) {
            Session::flash('error', 'No pending signup was found for that email. Start from the signup form again.');
            redirect('/signup');
        }

        Session::flash('status', 'A new verification code was sent.');

        if (in_array(($delivery['channel'] ?? ''), ['log', 'local_preview'], true) && isset($delivery['path'])) {
            Session::flash('info', 'Email delivery is not fully configured. Please check your mail settings and resend the code.');
        }

        $this->redirectToVerificationPage($email);
    }

    public function logout(): never
    {
        if (Csrf::validate((string) request_input('_token')) && Auth::check()) {
            Auth::logout();
            Session::flash('status', 'You have been logged out.');
        }

        redirect('/');
    }

    private function redirectToVerificationPage(string $email): never
    {
        header('Location: ' . url('signup/verify') . '?email=' . urlencode(strtolower(trim($email))), true, 302);
        exit;
    }

    private function redirectToPasswordResetPage(string $token): never
    {
        header('Location: ' . url('password/reset') . '?token=' . urlencode($token), true, 302);
        exit;
    }
}
