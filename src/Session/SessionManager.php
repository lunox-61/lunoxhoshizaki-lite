<?php

namespace LunoxHoshizaki\Session;

class SessionManager
{
    /**
     * Flash a key-value pair to the session.
     */
    public static function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Get a flashed value from the previous request.
     */
    public static function getFlash(string $key, $default = null)
    {
        return $_SESSION['_flash_old'][$key] ?? $default;
    }

    /**
     * Get old input by key.
     */
    public static function getOld(string $key, $default = '')
    {
        $old = static::getFlash('old', []);
        return $old[$key] ?? $default;
    }

    /**
     * Get validation errors for a specific field.
     */
    public static function getError(string $key)
    {
        $errors = static::getFlash('errors', []);
        return $errors[$key][0] ?? null; // return first error
    }

    /**
     * Get all validation errors.
     */
    public static function getErrors(): array
    {
        return static::getFlash('errors', []);
    }
}
