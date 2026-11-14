<?php
namespace App\Core;

/**
 * Minimal router with {param} placeholders and per-route middleware.
 * Handlers are "Controller@method" relative to App\Controllers.
 */
class Router
{
    private $routes = [];

    public function get(string $pattern, string $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, string $handler, array $middleware = []): void
    {
        $this->add('POST', $pattern, $handler, $middleware);
    }

    public function add(string $method, string $pattern, string $handler, array $middleware = []): void
    {
        $regex = '#^' . preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[A-Za-z0-9\-_.]+)', rtrim($pattern, '/') ?: '/') . '$#';
        $this->routes[] = compact('method', 'pattern', 'handler', 'middleware', 'regex');
    }

    public function dispatch(string $method, string $path): void
    {
        $path = '/' . trim($path, '/');
        $allowed = false;
        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $path, $m)) {
                continue;
            }
            $allowed = true;
            if ($route['method'] !== $method) {
                continue;
            }
            $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            foreach ($route['middleware'] as $mw) {
                $this->runMiddleware($mw);
            }
            list($class, $action) = explode('@', $route['handler']);
            $fqcn = 'App\\Controllers\\' . $class;
            $controller = new $fqcn();
            echo call_user_func_array([$controller, $action], array_values($params));
            return;
        }
        if ($allowed) {
            abort(405, 'Method not allowed');
        }
        abort(404, 'Page not found');
    }

    /** Middleware spec: "auth", "role:admin", "csrf". */
    private function runMiddleware(string $spec): void
    {
        $parts = explode(':', $spec, 2);
        $arg = $parts[1] ?? null;
        switch ($parts[0]) {
            case 'auth':
                \App\Middleware\AuthMiddleware::handle();
                break;
            case 'role':
                \App\Middleware\RoleMiddleware::handle((string) $arg);
                break;
            case 'csrf':
                \App\Middleware\CsrfMiddleware::handle();
                break;
        }
    }
}
