<?php

namespace App\Core;

class Router
{
    private static array $routes = [];
    private static array $namedMiddlewares = [
        'auth' => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'admin' => \App\Middleware\AdminMiddleware::class,
        'csrf' => \App\Middleware\CsrfMiddleware::class,
    ];

    public static function get(string $uri, array|callable $action, array $middleware = []): void
    {
        self::addRoute('GET', $uri, $action, $middleware);
    }

    public static function post(string $uri, array|callable $action, array $middleware = []): void
    {
        // By default add csrf middleware to POST routes unless specified
        if (!in_array('csrf', $middleware, true)) {
            array_unshift($middleware, 'csrf');
        }
        self::addRoute('POST', $uri, $action, $middleware);
    }

    public static function put(string $uri, array|callable $action, array $middleware = []): void
    {
        if (!in_array('csrf', $middleware, true)) {
            array_unshift($middleware, 'csrf');
        }
        self::addRoute('PUT', $uri, $action, $middleware);
    }

    public static function patch(string $uri, array|callable $action, array $middleware = []): void
    {
        if (!in_array('csrf', $middleware, true)) {
            array_unshift($middleware, 'csrf');
        }
        self::addRoute('PATCH', $uri, $action, $middleware);
    }

    public static function delete(string $uri, array|callable $action, array $middleware = []): void
    {
        if (!in_array('csrf', $middleware, true)) {
            array_unshift($middleware, 'csrf');
        }
        self::addRoute('DELETE', $uri, $action, $middleware);
    }

    private static function addRoute(string $method, string $uri, array|callable $action, array $middleware = []): void
    {
        $uri = '/' . trim($uri, '/');
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        self::$routes[] = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public static function dispatch(Request $request): Response
    {
        $requestMethod = $request->method();
        $requestUri = $request->uri();

        foreach (self::$routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = self::compilePattern($route['uri']);
            if (preg_match($pattern, $requestUri, $matches)) {
                $params = [];
                foreach ($matches as $key => $val) {
                    if (is_string($key)) {
                        $params[$key] = $val;
                    }
                }

                return self::runPipeline($route, $request, $params);
            }
        }

        // 404 Not Found
        $html = View::render('errors.404', [
            'message' => 'Kechirasiz, siz qidirayotgan sahifa topilmadi yoki koʻchirilgan.'
        ], null);
        return Response::make($html, 404);
    }

    private static function compilePattern(string $uri): string
    {
        // Replace {param} with named regex group (?P<param>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    private static function runPipeline(array $route, Request $request, array $params): Response
    {
        $middlewares = $route['middleware'];

        $destination = function (Request $req) use ($route, $params): Response {
            $action = $route['action'];

            if (is_callable($action)) {
                $res = call_user_func_array($action, [$req, ...$params]);
                return $res instanceof Response ? $res : Response::make((string)$res);
            }

            if (is_array($action) && count($action) === 2) {
                [$controllerClass, $method] = $action;
                if (!class_exists($controllerClass)) {
                    throw new \RuntimeException("Controller topilmadi: {$controllerClass}");
                }

                $controller = new $controllerClass();
                if (!method_exists($controller, $method)) {
                    throw new \RuntimeException("Controller metodi topilmadi: {$controllerClass}@{$method}");
                }

                $res = $controller->$method($req, ...$params);
                return $res instanceof Response ? $res : Response::make((string)$res);
            }

            throw new \RuntimeException("Yaroqsiz route action");
        };

        // Build middleware stack pipeline
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($next, $middlewareName) {
                return function (Request $req) use ($next, $middlewareName): Response {
                    $middlewareClass = self::$namedMiddlewares[$middlewareName] ?? $middlewareName;

                    if (!class_exists($middlewareClass)) {
                        throw new \RuntimeException("Middleware topilmadi: {$middlewareClass}");
                    }

                    $instance = new $middlewareClass();
                    return $instance->handle($req, $next);
                };
            },
            $destination
        );

        return $pipeline($request);
    }
}
