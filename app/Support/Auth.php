<?php
declare(strict_types=1);

namespace App\Support;

final class Auth
{
    public static function check(): bool
    {
        return is_array(Session::get('auth.user'));
    }

    public static function user(): ?array
    {
        $user = Session::get('auth.user');

        return is_array($user) ? $user : null;
    }

    public static function role(): ?string
    {
        $role = self::user()['role'] ?? null;

        return is_string($role) ? $role : null;
    }

    public static function login(array $user): void
    {
        Session::put('auth.user', [
            'id' => (int) ($user['id'] ?? 0),
            'first_name' => (string) ($user['first_name'] ?? ''),
            'last_name' => (string) ($user['last_name'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'phone' => (string) ($user['phone'] ?? ''),
            'role' => (string) ($user['role'] ?? 'customer'),
        ]);

        Session::regenerate();
    }

    public static function logout(): void
    {
        Session::forget('auth');
        Session::regenerate();
    }

    public static function dashboardPathForRole(string $role): string
    {
        return match ($role) {
            'admin' => '/dashboard/admin',
            'employee' => '/dashboard/employee',
            default => '/dashboard/customer',
        };
    }

    public static function dashboardPathForCurrentUser(): string
    {
        $role = self::role();

        return $role === null ? '/login' : self::dashboardPathForRole($role);
    }
}
