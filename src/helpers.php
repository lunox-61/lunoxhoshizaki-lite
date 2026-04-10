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
     * Return Auth manager instance (static Facade wrapper).
     */
    function auth(): \LunoxHoshizaki\Auth\Auth
    {
        return new \LunoxHoshizaki\Auth\Auth();
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
     */
    function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        try {
            echo \LunoxHoshizaki\View\View::make('basic.errors.error', [
                'code' => $code,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            echo "Error {$code}: {$message}";
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
     */
    function session(string $key, mixed $default = null): mixed
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION[$key] ?? $default;
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
