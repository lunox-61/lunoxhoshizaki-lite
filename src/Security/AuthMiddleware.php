<?php

namespace LunoxHoshizaki\Security;

use Closure;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use LunoxHoshizaki\Auth\Auth;
use LunoxHoshizaki\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Handle the incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!Auth::check()) {
            if ($request->input('wantsJson') || str_contains($request->server['HTTP_ACCEPT'] ?? '', 'application/json')) {
                return new Response(json_encode(['error' => 'Unauthenticated.']), 401, ['Content-Type' => 'application/json']);
            }
            
            // Redirect to login page
            header('Location: /login');
            exit;
        }

        return $next($request);
    }
}
