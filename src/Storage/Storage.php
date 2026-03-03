<?php

namespace LunoxHoshizaki\Storage;

class Storage
{
    protected string $disk = 'public';

    public function __construct(string $disk = 'public')
    {
        $this->disk = $disk;
    }

    /**
     * Set the disk to use.
     */
    public static function disk(string $disk = 'local'): self
    {
        return new self($disk);
    }

    /**
     * Get the base path for the current disk.
     */
    protected function getBasePath(): string
    {
        if ($this->disk === 'public') {
            return dirname(__DIR__, 2) . '/public/storage';
        }
        return dirname(__DIR__, 2) . '/storage/app';
    }

    /**
     * Store contents in a file.
     */
    public function put(string $path, string $contents): bool
    {
        $fullPath = $this->getFullPath($path);
        $this->ensureDirectoryExists(dirname($fullPath));
        return file_put_contents($fullPath, $contents) !== false;
    }

    /**
     * Retrieve contents of a file.
     */
    public function get(string $path): ?string
    {
        $fullPath = $this->getFullPath($path);
        if (file_exists($fullPath)) {
            return file_get_contents($fullPath);
        }
        return null;
    }

    /**
     * Check if a file exists.
     */
    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    /**
     * Delete a file.
     */
    public function delete(string $path): bool
    {
        $fullPath = $this->getFullPath($path);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Get the relative URL for public files.
     */
    public function url(string $path): string
    {
        if ($this->disk === 'public') {
            return '/storage/' . ltrim($path, '/');
        }
        throw new \Exception("Cannot get URL for non-public disk.");
    }

    /**
     * Helper to get full absolute path.
     */
    protected function getFullPath(string $path): string
    {
        return $this->getBasePath() . '/' . ltrim($path, '/');
    }

    /**
     * Helper to recursively ensure a directory exists.
     */
    protected function ensureDirectoryExists(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Magic method to allow static calls to instance methods (defaulting to 'public' disk).
     */
    public static function __callStatic($method, $parameters)
    {
        return (new self('public'))->$method(...$parameters);
    }
}
