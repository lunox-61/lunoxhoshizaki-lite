<?php

namespace LunoxHoshizaki\Security;

use LunoxHoshizaki\Middleware\MiddlewareInterface;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use Closure;
use Exception;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Generate token if not exists
        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }

        // Check token on state-changing methods
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            $token = $request->input('_token');
            $headerToken = $request->server['HTTP_X_CSRF_TOKEN'] ?? null;
            
            $providedToken = $token ?: $headerToken;

            if (empty($providedToken) || !hash_equals($_SESSION['_token'], $providedToken)) {
                try {
                    $content = \LunoxHoshizaki\View\View::make('errors.error', ['code' => 419]);
                    return new Response($content, 419);
                } catch (Exception $e) {
                    return new Response("CSRF Token Mismatch.", 419);
                }
            }
        }

        return $next($request);
    }

    /**
     * Helper to get current token.
     */
    public static function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }
}
