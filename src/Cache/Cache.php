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
     */
    protected static function getFilePath(string $key): string
    {
        return static::getCachePath() . '/' . md5($key) . '.cache';
    }

    /**
     * Store an item in the cache for a given number of seconds.
     */
    public static function put(string $key, mixed $value, int $ttl = 3600): bool
    {
        $file = static::getFilePath($key);
        $data = [
            'expires_at' => time() + $ttl,
            'value' => serialize($value)
        ];
        
        return file_put_contents($file, serialize($data)) !== false;
    }

    /**
     * Retrieve an item from the cache.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $file = static::getFilePath($key);
        if (file_exists($file)) {
            $data = unserialize(file_get_contents($file));
            
            if (time() <= $data['expires_at']) {
                return unserialize($data['value']);
            }
            
            // Cache expired
            static::forget($key);
        }
        
        return $default;
    }

    /**
     * Determine if an item exists in the cache.
     */
    public static function has(string $key): bool
    {
        return static::get($key) !== null;
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
}
