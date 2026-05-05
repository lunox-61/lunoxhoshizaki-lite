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

        // Content Security Policy (OWASP A02, ISO A.8.26)
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data:",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'"
        ]);
        $response->setHeader('Content-Security-Policy', $csp);

        // Restrict access to browser features (Permissions-Policy)
        $response->setHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Strict Transport Security (HSTS)
        // Supports: direct HTTPS detection, FORCE_HSTS env for reverse proxies,
        // and auto-enable in production environments.
        $isHttps    = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $forceHsts  = ($_ENV['FORCE_HSTS'] ?? 'false') === 'true';
        $isProduction = ($_ENV['APP_ENV'] ?? 'local') === 'production';

        if ($isHttps || $forceHsts || $isProduction) {
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
