<?php

namespace LunoxHoshizaki\Cache;

/**
 * CacheDriverInterface — Contract for cache driver implementations.
 *
 * All cache drivers (File, Redis, Memcached, etc.) must implement
 * this interface. The main Cache class delegates to the active driver.
 *
 * To swap drivers, bind a different implementation in the Container:
 *   $container->singleton(CacheDriverInterface::class, RedisDriver::class);
 */
interface CacheDriverInterface
{
    /**
     * Store an item in the cache.
     *
     * @param string $key   Cache key
     * @param mixed  $value Value to store (will be JSON-encoded)
     * @param int    $ttl   Time-to-live in seconds
     * @return bool  True on success
     */
    public function put(string $key, mixed $value, int $ttl = 3600): bool;

    /**
     * Retrieve an item from the cache.
     *
     * @param string $key     Cache key
     * @param mixed  $default Default value if key doesn't exist or is expired
     * @return mixed Cached value or default
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Determine if an item exists in the cache (and is not expired).
     */
    public function has(string $key): bool;

    /**
     * Remove an item from the cache.
     */
    public function forget(string $key): bool;

    /**
     * Remove all items from the cache.
     */
    public function flush(): bool;

    /**
     * Increment an integer value atomically.
     *
     * @param string $key  Cache key
     * @param int    $step Amount to increment
     * @param int    $ttl  TTL for new keys
     * @return int   New value after incrementing
     */
    public function increment(string $key, int $step = 1, int $ttl = 3600): int;

    /**
     * Decrement an integer value atomically.
     *
     * @param string $key  Cache key
     * @param int    $step Amount to decrement
     * @param int    $ttl  TTL for new keys
     * @return int   New value after decrementing
     */
    public function decrement(string $key, int $step = 1, int $ttl = 3600): int;
}
