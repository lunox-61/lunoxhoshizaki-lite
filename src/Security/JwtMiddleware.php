<?php

namespace LunoxHoshizaki\Security;

use LunoxHoshizaki\Middleware\MiddlewareInterface;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use LunoxHoshizaki\Http\ApiResponse;
use Closure;

/**
 * JwtMiddleware — JWT Bearer Token Authentication Middleware
 *
 * Verifies a JSON Web Token (HS256) from the Authorization header.
 * Uses the APP_JWT_SECRET key from .env for signing and verification.
 *
 * Usage in routes/api.php:
 *   Router::prefix('/api')
 *       ->middlewareGroup([CorsMiddleware::class, JwtMiddleware::class])
 *       ->group(function () {
 *           Router::get('/me', [ProfileController::class, 'show']);
 *       });
 *
 * Token generation example (in your AuthController):
 *   $token = JwtGuard::generate(['user_id' => $user->id, 'role' => $user->role]);
 *   return ApiResponse::success(['token' => $token]);
 */
class JwtMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ApiResponse::unauthorized('No Bearer token provided.');
        }

        $payload = JwtGuard::verify($token);

        if ($payload === null) {
            return ApiResponse::unauthorized('Invalid or expired JWT token.');
        }

        // Inject payload into the request server bag so controllers can read it
        // Access via: $request->server['JWT_PAYLOAD']
        $_SERVER['JWT_PAYLOAD'] = $payload;

        return $next($request);
    }
}
