<?php

use LunoxHoshizaki\Session\SessionManager;
use LunoxHoshizaki\Security\CsrfMiddleware;

if (!function_exists('old')) {
    /**
     * Get old input value.
     */
    function old(string $key, $default = '')
    {
        return SessionManager::getOld($key, $default);
    }
}

if (!function_exists('errors')) {
    /**
     * Get validation error message for a field.
     */
    function errors(string $key)
    {
        return SessionManager::getError($key);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a CSRF token HTML generic hidden input field.
     */
    function csrf_field(): string
    {
        $token = CsrfMiddleware::getToken();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token) . '">';
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset path.
     */
    function asset(string $path): string
    {
        $path = ltrim($path, '/');
        // Simple asset helper, can be extended for CDN or versioning later
        return '/' . $path;
    }
}

if (!function_exists('auth')) {
    /**
     * Return Auth manager instance (static Facade wrapper).
     */
    function auth(): \LunoxHoshizaki\Auth\Auth
    {
        return new \LunoxHoshizaki\Auth\Auth();
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML special characters in a string.
     */
    function e(?string $value, bool $doubleEncode = true): string
    {
        if (is_null($value)) {
            return '';
        }
        
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}
