<?php

namespace LunoxHoshizaki\Security;

use LunoxHoshizaki\Middleware\MiddlewareInterface;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use Closure;

class ApiAuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = $_ENV['API_TOKEN'] ?? '';

        $authHeader = $request->server['HTTP_AUTHORIZATION'] ?? '';
        $token = '';

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            // Fallback to check api_token in query or body
            $token = $request->input('api_token', '');
        }

        if (empty($expectedToken) || $token !== $expectedToken) {
            return new Response(json_encode([
                'success' => false,
                'message' => 'Unauthorized Access. Invalid or missing API token.'
            ]), 401, ['Content-Type' => 'application/json']);
        }

        return $next($request);
    }
}
