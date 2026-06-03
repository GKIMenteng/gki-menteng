<?php

declare(strict_types=1);

namespace GkiMenteng\Core;

use GkiMenteng\Middleware\MiddlewareInterface;

final class Router
{
    /** @var list<array{methods: list<string>, pattern: string, handler: callable}> */
    private array $routes = [];

    /** @var list<MiddlewareInterface> */
    private array $middleware = [];

    /** @param list<MiddlewareInterface> $middleware */
    public function setMiddleware(array $middleware): void
    {
        $this->middleware = $middleware;
    }

    public function get(string $pattern, callable $handler): void
    {
        $this->add(['GET'], $pattern, $handler);
    }

    /** @param list<string> $methods */
    public function add(array $methods, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'methods' => array_map('strtoupper', $methods),
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $handler = function (Request $request): Response {
            foreach ($this->routes as $route) {
                if (!in_array($request->method, $route['methods'], true)) {
                    continue;
                }

                $params = $this->match($route['pattern'], $request->path);
                if ($params === null) {
                    continue;
                }

                return ($route['handler'])($request, $params);
            }

            return Response::error('Route not found.', 404);
        };

        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn (callable $next, MiddlewareInterface $middleware) => fn (Request $request) => $middleware->handle($request, $next),
            $handler,
        );

        return $pipeline($request);
    }

    /** @return array<string, string>|null */
    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
