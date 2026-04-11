<?php
declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class View
{
    public static function make(string $view, array $data = [], string $layout = 'layouts.app'): string
    {
        $viewFile = self::resolve($view);
        $layoutFile = self::resolve($layout);

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        ob_start();
        require $layoutFile;

        return (string) ob_get_clean();
    }

    private static function resolve(string $view): string
    {
        $path = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($path)) {
            throw new RuntimeException(sprintf('View "%s" was not found.', $view));
        }

        return $path;
    }
}
