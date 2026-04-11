<?php
declare(strict_types=1);

namespace App\Support;

final class Config
{
    private static array $items = [];

    public static function set(array $items): void
    {
        self::$items = $items;
    }

    public static function setValue(string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $target = &self::$items;

        foreach ($segments as $segment) {
            if (!is_array($target)) {
                $target = [];
            }

            if (!array_key_exists($segment, $target) || !is_array($target[$segment])) {
                $target[$segment] ??= [];
            }

            $target = &$target[$segment];
        }

        $target = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = self::$items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}
