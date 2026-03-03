<?php

namespace LunoxHoshizaki\Security;

use LunoxHoshizaki\Middleware\MiddlewareInterface;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use Closure;

class SecureHeadersMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = clone $next($request);

        // Clickjacking Protection
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');

        // Cross-Site Scripting (XSS) Protection for older browsers
        $response->setHeader('X-XSS-Protection', '1; mode=block');

        // MIME-Sniffing Protection
        $response->setHeader('X-Content-Type-Options', 'nosniff');

        // Prevent leaking Referrer header strictly
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Strict Transport Security (HSTS) if HTTPS
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
