<?php

use LunoxHoshizaki\Session\SessionManager;
use LunoxHoshizaki\Security\CsrfMiddleware;

if (!function_exists('old')) {
    /**
     * Get old input value.
     */
    function old(string $key, $default = '')
    {
        return SessionManager::getOld($key, $default);
    }
}

if (!function_exists('errors')) {
    /**
     * Get validation error message for a field.
     */
    function errors(string $key)
    {
        return SessionManager::getError($key);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a CSRF token HTML generic hidden input field.
     */
    function csrf_field(): string
    {
        $token = CsrfMiddleware::getToken();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token) . '">';
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     */
    function csrf_token(): string
    {
        return CsrfMiddleware::getToken();
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset path.
     */
    function asset(string $path): string
    {
        $path = ltrim($path, '/');
        return '/' . $path;
    }
}

if (!function_exists('auth')) {
    /**
     * Return Auth class name for static method calls.
     *
     * Usage:
     *   auth()::check()     → Auth::check()
     *   auth()::user()      → Auth::user()
     *   auth()::hasRole('admin')
     *
     * All Auth methods are static, so this returns the class string
     * which PHP can use for static dispatch via ::.
     */
    function auth(): string
    {
        return \LunoxHoshizaki\Auth\Auth::class;
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML special characters in a string.
     */
    function e(?string $value, bool $doubleEncode = true): string
    {
        if (is_null($value)) {
            return '';
        }
        
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a new redirect response.
     *
     * Usage:
     *   redirect('/dashboard');          → Sends redirect immediately
     *   redirect('/login')->with(...)    → Chain flash data before sending
     *   redirect()->back();              → Redirect to previous page
     */
    function redirect(?string $url = null): \LunoxHoshizaki\Http\Redirect
    {
        if ($url) {
            return \LunoxHoshizaki\Http\Redirect::to($url);
        }
        return new \LunoxHoshizaki\Http\Redirect();
    }
}

if (!function_exists('route')) {
    /**
     * Generate the URL to a named route.
     *
     * Usage:
     *   route('login')                    → '/login'
     *   route('user.show', ['id' => 5])   → '/users/5'
     */
    function route(string $name, array $params = []): string
    {
        return \LunoxHoshizaki\Routing\Router::route($name, $params);
    }
}

if (!function_exists('config')) {
    /**
     * Get a configuration value using dot notation.
     *
     * Usage:
     *   config('app.name')              → reads config/app.php ['name']
     *   config('app.name', 'Default')   → with fallback default
     *   config('DB_HOST')               → reads $_ENV['DB_HOST']
     */
    function config(string $key, mixed $default = null): mixed
    {
        return \LunoxHoshizaki\Config\Config::get($key, $default);
    }
}

if (!function_exists('collect')) {
    /**
     * Create a new collection from the given value.
     */
    function collect(array $items = []): \LunoxHoshizaki\Support\Collection
    {
        return new \LunoxHoshizaki\Support\Collection($items);
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     */
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('now')) {
    /**
     * Get the current date/time as a formatted string.
     */
    function now(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump the given variables and end the script.
     */
    function dd(mixed ...$vars): never
    {
        echo '<pre style="background:#1e1e2e;color:#cdd6f4;padding:16px;border-radius:8px;font-family:monospace;font-size:13px;overflow:auto;">';
        foreach ($vars as $var) {
            var_dump($var);
            echo "\n";
        }
        echo '</pre>';
        exit(1);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump the given variables (without ending the script).
     */
    function dump(mixed ...$vars): void
    {
        echo '<pre style="background:#1e1e2e;color:#cdd6f4;padding:16px;border-radius:8px;font-family:monospace;font-size:13px;overflow:auto;">';
        foreach ($vars as $var) {
            var_dump($var);
            echo "\n";
        }
        echo '</pre>';
    }
}

if (!function_exists('abort')) {
    /**
     * Throw an HTTP error response.
     *
     * Tries to render the errors.error view, falls back to plain text.
     */
    function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        try {
            echo \LunoxHoshizaki\View\View::make('errors.error', [
                'code' => $code,
                'message' => $message
            ]);
        } catch (\Throwable $e) {
            echo "Error {$code}: " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        }
        exit;
    }
}

if (!function_exists('session')) {
    /**
     * Get or set session values.
     *
     * Usage:
     *   session('key')              → get value
     *   session('key', 'default')   → get with default
     *   session(['key' => 'val'])   → set value(s)
     */
    function session(string|array $key = null, mixed $default = null): mixed
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Set mode: session(['key' => 'value', ...])
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $_SESSION[$k] = $v;
            }
            return null;
        }

        // Get mode: session('key') or session('key', 'default')
        if (is_string($key)) {
            return $_SESSION[$key] ?? $default;
        }

        // No arguments — return null (or could return session manager)
        return null;
    }
}

if (!function_exists('url')) {
    /**
     * Generate a full URL for the given path.
     */
    function url(string $path = ''): string
    {
        $base = $_ENV['APP_URL'] ?? 'http://localhost:8000';
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('method_field')) {
    /**
     * Generate a hidden input field for HTTP method spoofing.
     *
     * HTML forms only support GET and POST. Use this helper to submit
     * PUT, PATCH, or DELETE requests from forms:
     *
     *   <form method="POST" action="/users/5">
     *       <?= csrf_field() ?>
     *       <?= method_field('DELETE') ?>
     *       <button type="submit">Delete</button>
     *   </form>
     */
    function method_field(string $method): string
    {
        $method = strtoupper($method);
        return '<input type="hidden" name="_method" value="' . htmlspecialchars($method) . '">';
    }
}

if (!function_exists('response')) {
    /**
     * Create a new Response instance or JSON response.
     *
     * Usage:
     *   return response()->json(['status' => 'ok']);
     *   return response('Hello', 200);
     */
    function response(string $content = '', int $statusCode = 200, array $headers = []): \LunoxHoshizaki\Http\Response
    {
        return new \LunoxHoshizaki\Http\Response($content, $statusCode, $headers);
    }
}

if (!function_exists('app')) {
    /**
     * Get the Application instance, or resolve a service from the Container.
     *
     * Usage:
     *   app()                       → Application instance
     *   app(UserService::class)     → Resolve UserService via Container
     */
    function app(?string $abstract = null): mixed
    {
        if (is_null($abstract)) {
            return \LunoxHoshizaki\Application::getInstance();
        }
        return \LunoxHoshizaki\Container\Container::getInstance()->make($abstract);
    }
}

if (!function_exists('resolve')) {
    /**
     * Resolve a service from the IoC Container.
     *
     * Alias for app(ServiceClass::class).
     *
     * Usage:
     *   $logger = resolve(Logger::class);
     */
    function resolve(string $abstract, array $params = []): mixed
    {
        return \LunoxHoshizaki\Container\Container::getInstance()->make($abstract, $params);
    }
}

if (!function_exists('api_success')) {
    /**
     * Return a standardized API success JSON response.
     *
     * Usage:
     *   return api_success($data);
     *   return api_success($user->toArray(), 'User fetched.', 200);
     *   return api_success($item, 'Created.', 201, ['server_time' => now()]);
     */
    function api_success(
        mixed  $data       = null,
        string $message    = 'Success.',
        int    $statusCode = 200,
        array  $meta       = []
    ): \LunoxHoshizaki\Http\Response {
        return \LunoxHoshizaki\Http\ApiResponse::success($data, $message, $statusCode, $meta);
    }
}

if (!function_exists('api_error')) {
    /**
     * Return a standardized API error JSON response.
     *
     * Usage:
     *   return api_error('Not found.', 404);
     *   return api_error('Validation failed.', 422, $validator->errors());
     */
    function api_error(
        string $message    = 'An error occurred.',
        int    $statusCode = 400,
        mixed  $errors     = null
    ): \LunoxHoshizaki\Http\Response {
        return \LunoxHoshizaki\Http\ApiResponse::error($message, $statusCode, $errors);
    }
}

if (!function_exists('api_paginated')) {
    /**
     * Return a standardized paginated API JSON response.
     *
     * Usage:
     *   return api_paginated($users, $total, $page, $perPage);
     */
    function api_paginated(
        array  $items,
        int    $total,
        int    $page    = 1,
        int    $perPage = 15,
        string $message = 'Success.'
    ): \LunoxHoshizaki\Http\Response {
        return \LunoxHoshizaki\Http\ApiResponse::paginated($items, $total, $page, $perPage, $message);
    }
}

if (!function_exists('jwt_generate')) {
    /**
     * Generate a signed JWT token for the given claims.
     *
     * Usage:
     *   $token = jwt_generate(['user_id' => $user->id, 'role' => $user->role]);
     *   $token = jwt_generate(['user_id' => 1], ttl: 86400); // 24 hours
     */
    function jwt_generate(array $claims = [], ?int $ttl = null): string
    {
        return \LunoxHoshizaki\Security\JwtGuard::generate($claims, $ttl);
    }
}

if (!function_exists('jwt_verify')) {
    /**
     * Verify a JWT token and return its payload, or null if invalid/expired.
     *
     * Usage:
     *   $payload = jwt_verify($token);
     *   if (!$payload) { // handle unauthorized }
     */
    function jwt_verify(string $token): ?array
    {
        return \LunoxHoshizaki\Security\JwtGuard::verify($token);
    }
}

if (!function_exists('bearer_token')) {
    /**
     * Get the Bearer token from the current Authorization header.
     *
     * Usage:
     *   $token = bearer_token();
     */
    function bearer_token(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
