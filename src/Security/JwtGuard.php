<?php

namespace LunoxHoshizaki\Security;

/**
 * JwtGuard — Lightweight HS256 JWT Token Generator & Verifier
 *
 * Uses HMAC-SHA256 for signing. Requires APP_JWT_SECRET in .env.
 *
 * Usage:
 *   // Generate a token (e.g. on login):
 *   $token = JwtGuard::generate([
 *       'user_id' => $user->id,
 *       'role'    => $user->role,
 *   ]);
 *
 *   // Verify a token (e.g. in middleware):
 *   $payload = JwtGuard::verify($token);
 *   if ($payload === null) { // expired or tampered }
 *
 *   // Get user ID from token directly:
 *   $userId = JwtGuard::getUserId($token);
 *
 * .env keys used:
 *   APP_JWT_SECRET=your-strong-secret-key-here
 *   APP_JWT_TTL=3600   (seconds, default 1 hour)
 */
class JwtGuard
{
    /**
     * Generate a signed HS256 JWT token.
     *
     * @param  array $claims    Custom payload claims (e.g. user_id, role)
     * @param  int|null $ttl   Time-to-live in seconds. Defaults to APP_JWT_TTL or 3600.
     * @return string          The encoded JWT string
     */
    public static function generate(array $claims = [], ?int $ttl = null): string
    {
        $secret = static::getSecret();
        $ttl    = $ttl ?? (int) ($_ENV['APP_JWT_TTL'] ?? 3600);
        $now    = time();

        $header = static::base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ]));

        $payload = static::base64UrlEncode(json_encode(array_merge($claims, [
            'iat' => $now,
            'exp' => $now + $ttl,
        ])));

        $signature = static::base64UrlEncode(
            hash_hmac('sha256', "{$header}.{$payload}", $secret, true)
        );

        return "{$header}.{$payload}.{$signature}";
    }

    /**
     * Verify a JWT token and return its payload if valid.
     *
     * Returns null if:
     * - Token is malformed
     * - Signature does not match (tampered)
     * - Token has expired
     *
     * @param  string $token
     * @return array|null     Decoded payload or null on failure
     */
    public static function verify(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        $secret           = static::getSecret();
        $expectedSignature = static::base64UrlEncode(
            hash_hmac('sha256', "{$header}.{$payload}", $secret, true)
        );

        // Constant-time comparison to prevent timing attacks
        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        $data = json_decode(static::base64UrlDecode($payload), true);

        if (!is_array($data)) {
            return null;
        }

        // Check expiry
        if (isset($data['exp']) && $data['exp'] < time()) {
            return null;
        }

        return $data;
    }

    /**
     * Get the user ID from a JWT token.
     *
     * Expects the payload to contain a 'user_id' claim.
     * Returns null if invalid or claim is absent.
     *
     * @param  string $token
     * @return int|null
     */
    public static function getUserId(string $token): ?int
    {
        $payload = static::verify($token);
        if ($payload && isset($payload['user_id'])) {
            return (int) $payload['user_id'];
        }
        return null;
    }

    /**
     * Decode a JWT token without verifying (useful for debugging).
     *
     * ⚠️ WARNING: Do NOT use for authentication. Use verify() instead.
     *
     * @param  string $token
     * @return array|null
     */
    public static function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        $data = json_decode(static::base64UrlDecode($parts[1]), true);
        return is_array($data) ? $data : null;
    }

    /**
     * Get the JWT secret from the environment.
     *
     * @throws \RuntimeException if APP_JWT_SECRET is not configured.
     */
    protected static function getSecret(): string
    {
        $secret = $_ENV['APP_JWT_SECRET'] ?? '';
        if (empty($secret)) {
            throw new \RuntimeException(
                'APP_JWT_SECRET is not set in .env. Run: php backfire jwt:secret'
            );
        }
        return $secret;
    }

    // -------------------------------------------------------------------------
    // Encoding Helpers
    // -------------------------------------------------------------------------

    protected static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected static function base64UrlDecode(string $data): string
    {
        $padded = str_pad(strtr($data, '-_', '+/'), strlen($data) % 4 === 0 ? strlen($data) : strlen($data) + (4 - strlen($data) % 4), '=');
        return base64_decode($padded);
    }
}
