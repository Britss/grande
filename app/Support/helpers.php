<?php
declare(strict_types=1);

use App\Support\Auth;
use App\Support\Config;
use App\Support\Csrf;
use App\Support\Session;

function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

function url(string $path = ''): string
{
    $basePath = rtrim((string) config('app.base_path', ''), '/');
    $trimmedPath = ltrim($path, '/');

    if ($trimmedPath === '') {
        return $basePath === '' ? '/' : $basePath . '/';
    }

    if ($basePath === '') {
        return '/' . $trimmedPath;
    }

    return $basePath . '/' . $trimmedPath;
}

function asset(string $path): string
{
    return url('public/assets/' . ltrim($path, '/'));
}

function e(string|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function request_path(): string
{
    return (string) config('request.path', '/');
}

function request_method(): string
{
    return strtoupper((string) config('request.method', $_SERVER['REQUEST_METHOD'] ?? 'GET'));
}

function request_input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function route_is(string $path): bool
{
    $normalizedPath = '/' . trim($path, '/');
    $normalizedPath = $normalizedPath === '//' ? '/' : (rtrim($normalizedPath, '/') ?: '/');

    return request_path() === $normalizedPath;
}

function route_starts_with(string $path): bool
{
    $normalizedPath = '/' . trim($path, '/');

    return str_starts_with(request_path(), $normalizedPath);
}

function old(string $key, mixed $default = ''): mixed
{
    return Session::getFlash('old.' . $key, $default);
}

function errors(?string $key = null): array
{
    $errors = Session::getFlash('errors', []);

    if (!is_array($errors)) {
        return [];
    }

    if ($key === null) {
        return $errors;
    }

    $fieldErrors = $errors[$key] ?? [];

    return is_array($fieldErrors) ? $fieldErrors : [];
}

function field_error(string $key): ?string
{
    $errors = errors($key);

    if ($errors === []) {
        return null;
    }

    return (string) $errors[0];
}

function has_error(string $key): bool
{
    return field_error($key) !== null;
}

function flash(string $key, mixed $default = null): mixed
{
    return Session::getFlash($key, $default);
}

function csrf_token(): string
{
    return Csrf::token();
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function auth_check(): bool
{
    return Auth::check();
}

function auth_user(): ?array
{
    return Auth::user();
}

function auth_dashboard_path(): string
{
    return Auth::dashboardPathForCurrentUser();
}

function redirect(string $path = '', int $status = 302): never
{
    header('Location: ' . url($path), true, $status);
    exit;
}
