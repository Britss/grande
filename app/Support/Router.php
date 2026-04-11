<?php
declare(strict_types=1);

namespace App\Support;

final class Router
{
    /**
     * @var array<string, array<string, callable|array{0: class-string, 1: string}>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, callable|array $action): void
    {
        $this->register('GET', $path, $action);
    }

    public function post(string $path, callable|array $action): void
    {
        $this->register('POST', $path, $action);
    }

    public function dispatch(string $method, string $uri): void
    {
        $normalizedMethod = strtoupper($method);
        $path = $this->normalizeRequestPath($uri);

        Config::setValue('request.method', $normalizedMethod);
        Config::setValue('request.path', $path);

        $action = $this->routes[$normalizedMethod][$path] ?? null;

        if ($action === null) {
            http_response_code(404);
            echo View::make('pages.errors.404', [
                'pageTitle' => 'Page Not Found',
                'metaDescription' => 'The requested page could not be found.',
            ]);
            return;
        }

        $response = $this->resolve($action);

        if (is_string($response)) {
            echo $response;
        }
    }

    private function register(string $method, string $path, callable|array $action): void
    {
        $this->routes[$method][$this->normalizeRoutePath($path)] = $action;
    }

    private function normalizeRoutePath(string $path): string
    {
        $trimmedPath = '/' . trim($path, '/');

        return $trimmedPath === '//' ? '/' : (rtrim($trimmedPath, '/') ?: '/');
    }

    private function normalizeRequestPath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $basePath = (string) Config::get('app.base_path', '');

        if ($basePath !== '' && strncmp($path, $basePath, strlen($basePath)) === 0) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        return $this->normalizeRoutePath($path);
    }

    private function resolve(callable|array $action): mixed
    {
        if (is_callable($action)) {
            return $action();
        }

        [$className, $method] = $action;
        $controller = new $className();

        return $controller->{$method}();
    }
}
