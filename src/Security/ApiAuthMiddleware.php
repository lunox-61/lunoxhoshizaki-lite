<?php

namespace LunoxHoshizaki\Security;

use LunoxHoshizaki\Middleware\MiddlewareInterface;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use LunoxHoshizaki\Http\ApiResponse;
use Closure;

/**
 * ApiAuthMiddleware — Static API Token Authentication Middleware
 *
 * Validates Bearer tokens against the APP_API_TOKEN (single)
 * or APP_API_TOKENS (comma-separated list) env variables.
 *
 * Token lookup order:
 *   1. Authorization: Bearer <token>
 *   2. Query string:  ?api_token=<token>
 *   3. Request body:  { "api_token": "<token>" }
 *
 * For JWT-based auth, use JwtMiddleware instead.
 */
class ApiAuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        // Support single token (APP_API_TOKEN) and multi-token (APP_API_TOKENS)
        $validTokens = static::resolveValidTokens();

        if (empty($validTokens)) {
            return ApiResponse::serverError(
                'API authentication is not configured. Set APP_API_TOKEN in .env.'
            );
        }

        $token = $request->bearerToken()
            ?? $request->input('api_token', '');

        if (empty($token) || !in_array($token, $validTokens, true)) {
            return ApiResponse::unauthorized(
                'Invalid or missing API token.'
            );
        }

        return $next($request);
    }

    /**
     * Resolve the list of valid tokens from the environment.
     *
     * Priority:
     *   - APP_API_TOKENS (comma-separated, supports multiple consumers)
     *   - APP_API_TOKEN  (single token, legacy / simple setup)
     */
    protected static function resolveValidTokens(): array
    {
        // Multi-token support
        $multiToken = $_ENV['APP_API_TOKENS'] ?? '';
        if (!empty($multiToken)) {
            return array_filter(array_map('trim', explode(',', $multiToken)));
        }

        // Single token (legacy key)
        $single = $_ENV['APP_API_TOKEN'] ?? $_ENV['API_TOKEN'] ?? '';
        return $single !== '' ? [$single] : [];
    }
}
