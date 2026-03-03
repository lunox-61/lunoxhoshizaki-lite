<?php

namespace LunoxHoshizaki\Security;

use LunoxHoshizaki\Cache\Cache;

class RateLimiter
{
    /**
     * Determine if the given key has been "accessed" too many times.
     */
    public static function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        if (static::attempts($key) >= $maxAttempts) {
            return true;
        }

        return false;
    }

    /**
     * Increment the counter for a given key for a given decay in seconds.
     */
    public static function hit(string $key, int $decaySeconds = 60): int
    {
        $hits = (int) Cache::get($key, 0);

        if (!Cache::has($key . ':timer')) {
            Cache::put($key . ':timer', time() + $decaySeconds, $decaySeconds);
        }

        $hits++;
        
        // Retrieve remaining TTL
        $timer = Cache::get($key . ':timer');
        $ttl = $timer ? max(1, $timer - time()) : $decaySeconds;

        Cache::put($key, $hits, $ttl);

        return $hits;
    }

    /**
     * Get the number of attempts for the given key.
     */
    public static function attempts(string $key): int
    {
        return (int) Cache::get($key, 0);
    }

    /**
     * Clear the hits and lockout timer for the given key.
     */
    public static function clear(string $key): void
    {
        Cache::forget($key);
        Cache::forget($key . ':timer');
    }

    /**
     * Get the number of remaining seconds until the block is lifted.
     */
    public static function availableIn(string $key): int
    {
        $timer = Cache::get($key . ':timer');

        if ($timer) {
            return max(0, $timer - time());
        }

        return 0;
    }
}
