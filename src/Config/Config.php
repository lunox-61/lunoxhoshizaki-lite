<?php

namespace LunoxHoshizaki\Config;

class Config
{
    /**
     * Cached configuration values.
     */
    protected static array $config = [];

    /**
     * Whether config has been loaded.
     */
    protected static bool $loaded = false;

    /**
     * Get a configuration value using "dot" notation.
     * Falls back to environment variables if no config file matches.
     * 
     * Usage:
     *   config('app.name')        → reads config/app.php → ['name']
     *   config('app.name', 'Foo') → with default
     *   config('DB_HOST')         → reads $_ENV['DB_HOST']
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // Simple env lookup if no dot notation
        if (!str_contains($key, '.')) {
            return $_ENV[$key] ?? $default;
        }

        $segments = explode('.', $key);
        $file = array_shift($segments);

        // Load config file if not cached
        if (!isset(static::$config[$file])) {
            static::loadFile($file);
        }

        // Traverse the config array using the remaining segments
        $value = static::$config[$file] ?? [];
        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Set a configuration value at runtime.
     */
    public static function set(string $key, mixed $value): void
    {
        if (!str_contains($key, '.')) {
            static::$config[$key] = $value;
            return;
        }

        $segments = explode('.', $key);
        $file = array_shift($segments);

        $arr = &static::$config;
        $arr = &$arr[$file];

        foreach ($segments as $i => $segment) {
            if ($i === count($segments) - 1) {
                $arr[$segment] = $value;
            } else {
                if (!isset($arr[$segment]) || !is_array($arr[$segment])) {
                    $arr[$segment] = [];
                }
                $arr = &$arr[$segment];
            }
        }
    }

    /**
     * Load a configuration file.
     */
    protected static function loadFile(string $name): void
    {
        try {
            $basePath = \LunoxHoshizaki\Application::getInstance()->getBasePath();
        } catch (\Exception $e) {
            $basePath = dirname(__DIR__, 2);
        }

        $path = $basePath . '/config/' . $name . '.php';

        if (file_exists($path)) {
            static::$config[$name] = require $path;
        } else {
            static::$config[$name] = [];
        }
    }

    /**
     * Check if a configuration key exists.
     */
    public static function has(string $key): bool
    {
        return static::get($key) !== null;
    }

    /**
     * Clear all cached configuration.
     */
    public static function flush(): void
    {
        static::$config = [];
    }
}
