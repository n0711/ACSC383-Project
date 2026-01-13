<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $getRoutes = [];
    private array $postRoutes = [];

    public function get(string $key, callable|array $handler): void
    {
        $this->getRoutes[$key] = $handler;
    }

    public function post(string $key, callable|array $handler): void
    {
        $this->postRoutes[$key] = $handler;
    }

    public function dispatch(Request $request): Response
    {
        $key = $request->route();
        $method = strtoupper($request->method());

        $routes = $method === 'POST' ? $this->postRoutes : $this->getRoutes;
        $handler = $routes[$key] ?? null;

        if (!$handler) {
            return Response::html("<h2>404 – Page not found</h2>", 404);
        }

        if (is_array($handler)) {
            [$class, $method] = $handler;
            return (new $class())->$method($request);
        }

        return $handler($request);
    }
}
