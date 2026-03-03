<?php

namespace LunoxHoshizaki\Security;

use LunoxHoshizaki\Middleware\MiddlewareInterface;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use Closure;

class ThrottleRequests implements MiddlewareInterface
{
    protected int $maxAttempts = 60;
    protected int $decaySeconds = 60;

    public function handle(Request $request, Closure $next): Response
    {
        // Resolve key based on IP
        $key = 'throttle:' . $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            $response = new Response();
            $response->setStatusCode(429);
            $response->setHeader('Retry-After', (string) $retryAfter);
            $response->setHeader('X-RateLimit-Limit', (string) $this->maxAttempts);
            $response->setHeader('X-RateLimit-Remaining', '0');
            $response->setContent(json_encode([
                'error' => 'Too Many Requests',
                'message' => 'Try again in ' . $retryAfter . ' seconds.'
            ]));
            
            return $response;
        }

        RateLimiter::hit($key, $this->decaySeconds);

        $response = $next($request);

        // Calculate remaining hits
        $remaining = max(0, $this->maxAttempts - RateLimiter::attempts($key));
        $response->setHeader('X-RateLimit-Limit', (string) $this->maxAttempts);
        $response->setHeader('X-RateLimit-Remaining', (string) $remaining);

        return $response;
    }

    /**
     * Resolve request signature based on IP address.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1($_SERVER['REMOTE_ADDR'] . '|' . $_SERVER['REQUEST_URI']);
    }
}
