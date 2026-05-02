<?php

namespace LunoxHoshizaki\Cache;

class Cache
{
    protected static ?string $cachePath = null;

    /**
     * Initialize the cache directory.
     */
    protected static function getCachePath(): string
    {
        if (is_null(static::$cachePath)) {
            static::$cachePath = dirname(__DIR__, 2) . '/storage/framework/cache';
            if (!is_dir(static::$cachePath)) {
                mkdir(static::$cachePath, 0755, true);
            }
        }
        return static::$cachePath;
    }

    /**
     * Get the file path for a given key.
     *
     * Uses SHA-256 instead of MD5 to prevent hash collisions (CWE-328).
     */
    protected static function getFilePath(string $key): string
    {
        return static::getCachePath() . '/' . hash('sha256', $key) . '.cache';
    }

    /**
     * Store an item in the cache for a given number of seconds.
     * Uses JSON encoding instead of serialize() to prevent CWE-502 Object Injection.
     */
    public static function put(string $key, mixed $value, int $ttl = 3600): bool
    {
        $file = static::getFilePath($key);
        $data = [
            'expires_at' => time() + $ttl,
            'value' => json_encode($value)
        ];
        
        return file_put_contents($file, json_encode($data), LOCK_EX) !== false;
    }

    /**
     * Retrieve an item from the cache.
     * Uses JSON decoding (safe) instead of unserialize() (unsafe).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $file = static::getFilePath($key);
        if (file_exists($file)) {
            $raw = file_get_contents($file);
            $data = json_decode($raw, true);
            
            if ($data && isset($data['expires_at']) && time() <= $data['expires_at']) {
                return json_decode($data['value'], true);
            }
            
            // Cache expired
            static::forget($key);
        }
        
        return $default;
    }

    /**
     * Determine if an item exists in the cache.
     *
     * Optimized: reads file metadata (existence + expiry) without
     * fully deserializing the cached value. Avoids the double-read
     * penalty of calling get().
     */
    public static function has(string $key): bool
    {
        $file = static::getFilePath($key);
        if (!file_exists($file)) {
            return false;
        }

        $raw = file_get_contents($file);
        $data = json_decode($raw, true);

        if ($data && isset($data['expires_at']) && time() <= $data['expires_at']) {
            return true;
        }

        // Expired — clean up
        @unlink($file);
        return false;
    }

    /**
     * Remove an item from the cache.
     */
    public static function forget(string $key): bool
    {
        $file = static::getFilePath($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    /**
     * Remove all items from the cache.
     */
    public static function flush(): bool
    {
        $files = glob(static::getCachePath() . '/*.cache');
        $success = true;
        foreach ($files as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     */
    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = static::get($key);
        
        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        static::put($key, $value, $ttl);
        
        return $value;
    }

    // -------------------------------------------------------------------------
    // Atomic Increment / Decrement
    // -------------------------------------------------------------------------

    /**
     * Increment a cached integer value atomically.
     *
     * Uses LOCK_EX for file-level locking to prevent race conditions
     * when multiple processes increment the same counter simultaneously.
     *
     * @param string $key   Cache key
     * @param int    $step  Amount to increment by
     * @param int    $ttl   TTL in seconds (only used if key doesn't exist yet)
     * @return int   The new value after incrementing
     */
    public static function increment(string $key, int $step = 1, int $ttl = 3600): int
    {
        $file = static::getFilePath($key);

        // Open with exclusive lock for atomic read-modify-write
        $fp = fopen($file, 'c+');
        if (!$fp) {
            return $step; // Fallback: assume starting from 0
        }

        flock($fp, LOCK_EX);

        $raw = stream_get_contents($fp);
        $current = 0;
        $expiresAt = time() + $ttl;

        if (!empty($raw)) {
            $data = json_decode($raw, true);
            if ($data && isset($data['expires_at']) && time() <= $data['expires_at']) {
                $current = (int) json_decode($data['value'], true);
                $expiresAt = $data['expires_at']; // Preserve original TTL
            }
        }

        $newValue = $current + $step;

        // Write back
        $writeData = json_encode([
            'expires_at' => $expiresAt,
            'value' => json_encode($newValue),
        ]);

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $writeData);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return $newValue;
    }

    /**
     * Decrement a cached integer value atomically.
     *
     * @param string $key   Cache key
     * @param int    $step  Amount to decrement by
     * @param int    $ttl   TTL in seconds (only used if key doesn't exist yet)
     * @return int   The new value after decrementing
     */
    public static function decrement(string $key, int $step = 1, int $ttl = 3600): int
    {
        return static::increment($key, -$step, $ttl);
    }

    // -------------------------------------------------------------------------
    // Cache Tags
    // -------------------------------------------------------------------------

    /**
     * Tag a set of cache keys for group invalidation.
     *
     * Usage:
     *   Cache::tags(['users', 'admins'])->put('user:1', $data, 3600);
     *   Cache::tags(['users'])->flush();  // Invalidates all keys tagged with 'users'
     *
     * @param array $tags Tag names
     * @return TaggedCache
     */
    public static function tags(array $tags): TaggedCache
    {
        return new TaggedCache($tags);
    }
}

/**
 * TaggedCache — Provides cache tag grouping and bulk invalidation.
 *
 * Tags are stored as index files mapping tag names to their cached key hashes.
 * Flushing a tag removes all cache entries associated with that tag.
 */
class TaggedCache
{
    protected array $tags;

    public function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * Store an item under the given tags.
     */
    public function put(string $key, mixed $value, int $ttl = 3600): bool
    {
        // Store the actual cache entry
        $result = Cache::put($key, $value, $ttl);

        // Register this key with each tag
        foreach ($this->tags as $tag) {
            $this->addKeyToTag($tag, $key);
        }

        return $result;
    }

    /**
     * Retrieve an item (delegates to base Cache).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    /**
     * Flush all cache entries for the current tag set.
     */
    public function flush(): bool
    {
        $success = true;

        foreach ($this->tags as $tag) {
            $keys = $this->getTaggedKeys($tag);
            foreach ($keys as $key) {
                if (!Cache::forget($key)) {
                    $success = false;
                }
            }
            // Remove the tag index itself
            $this->removeTagIndex($tag);
        }

        return $success;
    }

    /**
     * Get the tag index file path.
     */
    protected function getTagIndexPath(string $tag): string
    {
        $cachePath = dirname(__DIR__, 2) . '/storage/framework/cache';
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }
        return $cachePath . '/tag_' . hash('sha256', $tag) . '.index';
    }

    /**
     * Add a key to a tag's index.
     */
    protected function addKeyToTag(string $tag, string $key): void
    {
        $indexFile = $this->getTagIndexPath($tag);
        $keys = $this->getTaggedKeys($tag);
        $keys[] = $key;
        $keys = array_unique($keys);
        file_put_contents($indexFile, json_encode($keys), LOCK_EX);
    }

    /**
     * Get all keys registered under a tag.
     */
    protected function getTaggedKeys(string $tag): array
    {
        $indexFile = $this->getTagIndexPath($tag);
        if (!file_exists($indexFile)) {
            return [];
        }
        $data = json_decode(file_get_contents($indexFile), true);
        return is_array($data) ? $data : [];
    }

    /**
     * Remove a tag's index file.
     */
    protected function removeTagIndex(string $tag): void
    {
        $indexFile = $this->getTagIndexPath($tag);
        if (file_exists($indexFile)) {
            @unlink($indexFile);
        }
    }
}
