<?php

namespace LunoxHoshizaki\Security;

/**
 * Hash Helper — Provides a clean API for password hashing.
 * Uses Argon2ID when available, falls back to BCrypt.
 * 
 * Satisfies: OWASP A07, CWE-916, NIST IA-5, ISO A.8.24
 */
class Hash
{
    /**
     * Hash the given value using the best available algorithm.
     */
    public static function make(string $value, array $options = []): string
    {
        $algorithm = static::getAlgorithm();
        return password_hash($value, $algorithm, $options);
    }

    /**
     * Check the given plain value against a hash.
     * Uses timing-safe comparison internally via password_verify.
     */
    public static function check(string $value, string $hashedValue): bool
    {
        if (empty($hashedValue)) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

    /**
     * Check if the given hash needs to be rehashed.
     * Useful when upgrading algorithm or cost factors.
     */
    public static function needsRehash(string $hashedValue, array $options = []): bool
    {
        $algorithm = static::getAlgorithm();
        return password_needs_rehash($hashedValue, $algorithm, $options);
    }

    /**
     * Get the best available hashing algorithm.
     * Prefers Argon2ID > Argon2I > BCrypt.
     */
    protected static function getAlgorithm(): string|int
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return PASSWORD_ARGON2ID;
        }

        if (defined('PASSWORD_ARGON2I')) {
            return PASSWORD_ARGON2I;
        }

        return PASSWORD_BCRYPT;
    }

    /**
     * Get information about a hashed value.
     */
    public static function info(string $hashedValue): array
    {
        return password_get_info($hashedValue);
    }
}
