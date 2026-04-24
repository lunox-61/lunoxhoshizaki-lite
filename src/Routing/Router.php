<?php

namespace LunoxHoshizaki\Routing;

use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use Closure;
use Exception;
use Throwable;

class Router
{
    /**
     * Stored routes.
     * Format: ['GET' => ['/path' => ['action' => ..., 'middlewares' => [], 'name' => null]]]
     */
    protected static array $routes = [];

    /**
     * Set middlewares for the last registered route.
     */
    protected static array $lastRouteParams = [];

    /**
     * Named routes registry.
     */
    protected static array $namedRoutes = [];

    /**
     * Current group attributes stack (prefix, middleware).
     */
    protected static array $groupStack = [];

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
     * Register a PATCH route.
     */
    public static function patch(string $uri, array|Closure $action): static
    {
        return static::addRoute('PATCH', $uri, $action);
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
        // Apply group prefix if any
        $prefix = static::getCurrentPrefix();
        $uri = rtrim($prefix . '/' . ltrim($uri, '/'), '/') ?: '/';

        // Get group middlewares
        $groupMiddlewares = static::getCurrentMiddlewares();

        static::$routes[$method][$uri] = [
            'action' => $action,
            'middlewares' => $groupMiddlewares,
            'name' => null
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
     * Set a name for the previously registered route.
     */
    public function name(string $name): static
    {
        if (empty(static::$lastRouteParams)) {
            return $this;
        }

        $method = static::$lastRouteParams['method'];
        $uri = static::$lastRouteParams['uri'];

        static::$routes[$method][$uri]['name'] = $name;
        static::$namedRoutes[$name] = $uri;

        return $this;
    }

    /**
     * Define a group of routes that share attributes (prefix, middleware).
     *
     * Usage:
     *   Router::prefix('/api')->middleware([...])->group(function() { ... });
     *   Router::middleware([AuthMiddleware::class])->group(function() { ... });
     *   Router::prefix('/admin')->group(function() { ... });
     */
    public function group(Closure $callback): void
    {
        $callback();

        // Pop the current group off the stack after executing
        array_pop(static::$groupStack);
    }

    /**
     * Set prefix for a route group (static entry point).
     */
    public static function prefix(string $prefix): static
    {
        // Push a new group onto the stack
        static::$groupStack[] = [
            'prefix' => '/' . trim($prefix, '/'),
            'middlewares' => []
        ];

        return new static;
    }

    /**
     * Set middleware for a route group (static entry point).
     * Can be called on its own or chained after prefix().
     */
    public static function middlewareGroup(string|array $middlewares): static
    {
        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];

        // Check if there's already a group being built on the stack
        if (!empty(static::$groupStack)) {
            $lastIndex = count(static::$groupStack) - 1;
            static::$groupStack[$lastIndex]['middlewares'] = array_merge(
                static::$groupStack[$lastIndex]['middlewares'],
                $middlewares
            );
        } else {
            // Create new group entry
            static::$groupStack[] = [
                'prefix' => '',
                'middlewares' => $middlewares
            ];
        }

        return new static;
    }

    /**
     * Get the current group prefix from the stack.
     */
    protected static function getCurrentPrefix(): string
    {
        $prefix = '';
        foreach (static::$groupStack as $group) {
            $prefix .= $group['prefix'] ?? '';
        }
        return $prefix;
    }

    /**
     * Get the current group middlewares from the stack.
     */
    protected static function getCurrentMiddlewares(): array
    {
        $middlewares = [];
        foreach (static::$groupStack as $group) {
            $middlewares = array_merge($middlewares, $group['middlewares'] ?? []);
        }
        return $middlewares;
    }

    /**
     * Resolve a named route URL, replacing parameters.
     */
    public static function route(string $name, array $params = []): string
    {
        if (!isset(static::$namedRoutes[$name])) {
            throw new Exception("Route [{$name}] not defined.");
        }

        $uri = static::$namedRoutes[$name];

        // Replace named parameters
        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        return $uri;
    }

    /**
     * Get all registered routes (useful for debugging).
     */
    public static function getRoutes(): array
    {
        return static::$routes;
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
            $content = \LunoxHoshizaki\View\View::make('errors.error', ['code' => 404]);
            return new Response($content, 404);
        } catch (Throwable $e) {
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

            // Handle Redirect objects returned from controllers
            if ($response instanceof \LunoxHoshizaki\Http\Redirect) {
                $response->send();
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
