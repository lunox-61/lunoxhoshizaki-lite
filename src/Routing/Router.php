<?php

namespace LunoxHoshizaki\Routing;

use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use Closure;
use Exception;

class Router
{
    /**
     * Stored routes.
     * Format: ['GET' => ['/path' => ['action' => ..., 'middlewares' => []]]]
     */
    protected static array $routes = [];

    /**
     * Set middlewares for the last registered route.
     */
    protected static array $lastRouteParams = [];

    /**
     * Register a GET route.
     */
    public static function get(string $uri, array|Closure $action): static
    {
        return static::addRoute('GET', $uri, $action);
    }

    /**
     * Register a POST route.
     */
    public static function post(string $uri, array|Closure $action): static
    {
        return static::addRoute('POST', $uri, $action);
    }

    /**
     * Register a PUT route.
     */
    public static function put(string $uri, array|Closure $action): static
    {
        return static::addRoute('PUT', $uri, $action);
    }

    /**
     * Register a DELETE route.
     */
    public static function delete(string $uri, array|Closure $action): static
    {
        return static::addRoute('DELETE', $uri, $action);
    }

    /**
     * Add route to the registry.
     */
    protected static function addRoute(string $method, string $uri, array|Closure $action): static
    {
        // Internal method to add route
        $uri = rtrim($uri, '/') ?: '/';

        static::$routes[$method][$uri] = [
            'action' => $action,
            'middlewares' => []
        ];

        static::$lastRouteParams = ['method' => $method, 'uri' => $uri];

        return new static; // Return new instance for chaining
    }

    /**
     * Add middleware to the previously registered route.
     */
    public function middleware(string|array $middlewares): static
    {
        if (empty(static::$lastRouteParams)) {
            return $this;
        }

        $method = static::$lastRouteParams['method'];
        $uri = static::$lastRouteParams['uri'];

        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];

        foreach ($middlewares as $middleware) {
            static::$routes[$method][$uri]['middlewares'][] = $middleware;
        }

        return $this;
    }

    /**
     * Dispatch the request.
     */
    public static function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = $request->uri();

        $routes = static::$routes[$method] ?? [];

        // Simple exact matching first
        if (isset($routes[$uri])) {
            return static::runRoute($request, $routes[$uri]);
        }

        // Handle dynamic parameters
        foreach ($routes as $routeUri => $route) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $routeUri);
            $pattern = str_replace('/', '\/', $pattern);

            if (preg_match('/^' . $pattern . '$/', $uri, $matches)) {
                // Filter out integer keys from matches
                $params = array_filter($matches, function ($key) {
                    return !is_int($key);
                }, ARRAY_FILTER_USE_KEY);

                return static::runRoute($request, $route, $params);
            }
        }

        // Custom 404 error page fallback
        try {
            $content = \LunoxHoshizaki\View\View::make('basic.errors.error', ['code' => 404]);
            return new Response($content, 404);
        } catch (Exception $e) {
            return new Response('404 Not Found', 404);
        }
    }

    /**
     * Run the route action through constraints and middlewares.
     */
    protected static function runRoute(Request $request, array $route, array $params = []): Response
    {
        $action = $route['action'];
        $middlewares = $route['middlewares'];

        $corePipeline = function ($request) use ($action, $params) {
            if ($action instanceof Closure) {
                $response = call_user_func_array($action, array_merge([$request], $params));
            } elseif (is_array($action) && count($action) === 2) {
                $controller = new $action[0]();
                $method = $action[1];
                $response = call_user_func_array([$controller, $method], array_merge([$request], $params));
            } else {
                throw new Exception("Invalid route action.");
            }

            if (!$response instanceof Response) {
                if (is_array($response) || is_object($response)) {
                    $response = new Response(json_encode($response), 200, ['Content-Type' => 'application/json']);
                } else {
                    $response = new Response((string) $response);
                }
            }
            return $response;
        };

        // Build the middleware pipeline
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($next, $middleware) {
                return function ($request) use ($next, $middleware) {
                    $instance = new $middleware();
                    return $instance->handle($request, $next);
                };
            },
            $corePipeline
        );

        return $pipeline($request);
    }
}
