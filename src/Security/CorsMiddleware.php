<?php

namespace LunoxHoshizaki\Security;

use LunoxHoshizaki\Middleware\MiddlewareInterface;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use Closure;

class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        // Handle Preflight OPTIONS requests
        if ($request->method() === 'OPTIONS') {
            $response = new Response();
            $this->addCorsHeaders($response);
            $response->setStatusCode(200);
            return $response;
        }

        // Handle actual request
        $response = $next($request);
        $this->addCorsHeaders($response);

        return $response;
    }

    protected function addCorsHeaders(Response $response): void
    {
        $allowedOrigin = $_ENV['CORS_ALLOWED_ORIGIN'] ?? '*';
        $allowedMethods = $_ENV['CORS_ALLOWED_METHODS'] ?? 'GET, POST, PUT, DELETE, OPTIONS';
        $allowedHeaders = $_ENV['CORS_ALLOWED_HEADERS'] ?? 'Content-Type, Authorization, X-Requested-With';

        $response->setHeader('Access-Control-Allow-Origin', $allowedOrigin);
        $response->setHeader('Access-Control-Allow-Methods', $allowedMethods);
        $response->setHeader('Access-Control-Allow-Headers', $allowedHeaders);
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
    }
}
