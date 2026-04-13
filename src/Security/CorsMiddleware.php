<?php

namespace LunoxHoshizaki\Security;

use LunoxHoshizaki\Middleware\MiddlewareInterface;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use LunoxHoshizaki\Log\Log;
use Closure;

/**
 * CorsMiddleware — Handles Cross-Origin Resource Sharing policy.
 *
 * R4 Fix: Default CORS origin changed from '*' (wildcard) to '' (block-all).
 * The CORS_ALLOWED_ORIGIN env variable MUST be explicitly configured in production.
 * Supports comma-separated multi-origin allowlists and dynamic origin validation.
 *
 * Satisfies: OWASP A01:2021, OWASP A05:2021, ISO A.8.26
 */
class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        // Handle Preflight OPTIONS requests
        if ($request->method() === 'OPTIONS') {
            $response = new Response();
            $this->addCorsHeaders($request, $response);
            $response->setStatusCode(200);
            return $response;
        }

        // Handle actual request
        $response = $next($request);
        $this->addCorsHeaders($request, $response);

        return $response;
    }

    protected function addCorsHeaders(Request $request, Response $response): void
    {
        // R4: Default is empty string — block all cross-origin by default.
        // Set CORS_ALLOWED_ORIGIN in .env to enable (e.g. 'https://app.example.com')
        $configuredOrigins = $_ENV['CORS_ALLOWED_ORIGIN'] ?? '';
        $allowedMethods    = $_ENV['CORS_ALLOWED_METHODS'] ?? 'GET, POST, OPTIONS';
        $allowedHeaders    = $_ENV['CORS_ALLOWED_HEADERS'] ?? 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN';

        // If no origin is configured, do not emit CORS headers (safest default)
        if (empty($configuredOrigins)) {
            return;
        }

        // Resolve the origin to send back (supports wildcard '*' or multi-origin list)
        $resolvedOrigin = $this->resolveAllowedOrigin(
            $request->server['HTTP_ORIGIN'] ?? '',
            $configuredOrigins
        );

        if ($resolvedOrigin === null) {
            // Origin not in allowlist — log and do not add CORS headers
            Log::warning('CORS request blocked — origin not in allowlist', [
                'origin'     => $request->server['HTTP_ORIGIN'] ?? '(none)',
                'uri'        => $request->server['REQUEST_URI'] ?? '',
                'ip'         => $request->server['REMOTE_ADDR'] ?? '0.0.0.0',
            ]);
            return;
        }

        $response->setHeader('Access-Control-Allow-Origin', $resolvedOrigin);
        $response->setHeader('Access-Control-Allow-Methods', $allowedMethods);
        $response->setHeader('Access-Control-Allow-Headers', $allowedHeaders);

        // Only send credentials header when NOT using wildcard
        if ($resolvedOrigin !== '*') {
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
            // Inform proxies that response varies by Origin
            $response->setHeader('Vary', 'Origin');
        }
    }

    /**
     * Resolve the appropriate Access-Control-Allow-Origin value.
     *
     * - If configured to '*', return '*' directly.
     * - If configured as a comma-separated list, validate the incoming
     *   request Origin against the list and echo it back if matched.
     * - Returns null if the request origin is not permitted.
     */
    protected function resolveAllowedOrigin(string $requestOrigin, string $configuredOrigins): ?string
    {
        // Explicit wildcard — allow all (only appropriate for public APIs with no credentials)
        if (trim($configuredOrigins) === '*') {
            return '*';
        }

        // Build an allowlist from comma-separated origins
        $allowlist = array_map('trim', explode(',', $configuredOrigins));
        $allowlist = array_filter($allowlist); // remove empty entries

        if (in_array($requestOrigin, $allowlist, true)) {
            return $requestOrigin; // Echo matched origin back (required for credentials)
        }

        return null; // Not permitted
    }
}
