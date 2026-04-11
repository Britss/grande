<?php
declare(strict_types=1);

namespace App\Support;

final class Session
{
    public static function bootstrap(): void
    {
        $flash = $_SESSION['_flash'] ?? null;

        if (!is_array($flash)) {
            $_SESSION['_flash'] = [
                'current' => [],
                'next' => [],
            ];

            return;
        }

        $_SESSION['_flash'] = [
            'current' => is_array($flash['next'] ?? null) ? $flash['next'] : [],
            'next' => [],
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::read($_SESSION, $key, $default);
    }

    public static function put(string $key, mixed $value): void
    {
        self::write($_SESSION, $key, $value);
    }

    public static function has(string $key): bool
    {
        return self::read($_SESSION, $key, null) !== null;
    }

    public static function forget(string $key): void
    {
        self::remove($_SESSION, $key);
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function flash(string $key, mixed $value): void
    {
        self::write($_SESSION['_flash']['next'], $key, $value);
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        return self::read($_SESSION['_flash']['current'] ?? [], $key, $default);
    }

    public static function flashInput(array $input, array $except = ['_token', 'password', 'confirm_password']): void
    {
        foreach ($except as $key) {
            unset($input[$key]);
        }

        self::flash('old', $input);
    }

    public static function flashErrors(array $errors): void
    {
        self::flash('errors', $errors);
    }

    private static function read(array $source, string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $source;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    private static function write(array &$target, string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $cursor = &$target;

        foreach ($segments as $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                $cursor[$segment] ??= [];
            }

            $cursor = &$cursor[$segment];
        }

        $cursor = $value;
    }

    private static function remove(array &$target, string $key): void
    {
        $segments = explode('.', $key);
        $lastSegment = array_pop($segments);

        if ($lastSegment === null) {
            return;
        }

        $cursor = &$target;

        foreach ($segments as $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                return;
            }

            $cursor = &$cursor[$segment];
        }

        unset($cursor[$lastSegment]);
    }
}
