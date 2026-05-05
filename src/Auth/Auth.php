<?php

namespace LunoxHoshizaki\Auth;

use Exception;
use LunoxHoshizaki\Log\Log;
use LunoxHoshizaki\Security\RateLimiter;

class Auth
{
    /**
     * Maximum login attempts per account before lockout.
     * Gap R3 Fix: CWE-307 – Account-level brute force protection.
     * Satisfies: NIST AC-7, OWASP A07:2021
     */
    protected const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Lockout duration in seconds (default 15 minutes).
     */
    protected const LOCKOUT_DECAY = 900;

    /**
     * Check if a user is currently authenticated.
     */
    public static function check(): bool
    {
        return isset($_SESSION['auth_user_id']);
    }

    /**
     * Get the currently authenticated user.
     */
    public static function user()
    {
        if (static::check()) {
            $userModel = class_exists('\App\Models\User') ? '\App\Models\User' : null;
            if ($userModel) {
                return $userModel::find($_SESSION['auth_user_id']);
            }
        }
        return null;
    }

    /**
     * Get the ID of the currently authenticated user.
     */
    public static function id(): ?int
    {
        return $_SESSION['auth_user_id'] ?? null;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     * Assumes an 'email' and 'password' key exist in credentials.
     *
     * R2 Fix: Auto-log all auth events (success, failure, lockout).
     * R3 Fix: Per-account lockout after MAX_LOGIN_ATTEMPTS failures.
     * Satisfies: ISO A.8.5, NIST IA-5, NIST AC-7, OWASP A07:2021
     */
    public static function attempt(array $credentials): bool
    {
        $userModel = class_exists('\App\Models\User') ? '\App\Models\User' : null;
        if (!$userModel) {
            throw new Exception("Auth attempt failed: App\Models\User class not found.");
        }

        $email = $credentials['email'] ?? '';
        $ip    = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // --- R3: Per-account lockout check ---
        $lockKey = 'auth_lockout:' . sha1($email);
        if (RateLimiter::tooManyAttempts($lockKey, static::MAX_LOGIN_ATTEMPTS)) {
            $retryIn = RateLimiter::availableIn($lockKey);
            // R2: Log lockout event
            Log::warning('Account locked — too many failed login attempts', [
                'email'    => $email,
                'ip'       => $ip,
                'retry_in' => $retryIn . 's',
            ]);
            return false;
        }

        $password = $credentials['password'] ?? '';
        $user     = $userModel::query()->where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            // Success: clear lockout counter
            RateLimiter::clear($lockKey);

            // Auto-rehash: upgrade password hash if using outdated algorithm/cost
            // Satisfies: NIST IA-5 — ensure stored credentials use current best practices
            if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
                try {
                    $user->password = password_hash($password, PASSWORD_DEFAULT);
                    $user->save();
                    Log::info('Password hash auto-upgraded', [
                        'user_id' => $user->id,
                    ]);
                } catch (\Throwable $e) {
                    // Non-critical: don't block login if rehash save fails
                    Log::warning('Password rehash save failed', [
                        'user_id' => $user->id,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }

            // R2: Log successful authentication
            Log::info('Authentication successful', [
                'user_id' => $user->id,
                'email'   => $email,
                'ip'      => $ip,
            ]);
            static::login($user);
            return true;
        }

        // Failure: increment lockout counter
        RateLimiter::hit($lockKey, static::LOCKOUT_DECAY);
        $attemptsLeft = static::MAX_LOGIN_ATTEMPTS - RateLimiter::attempts($lockKey);
        // R2: Log failed authentication attempt
        Log::warning('Authentication failed', [
            'email'         => $email,
            'ip'            => $ip,
            'attempts_left' => max(0, $attemptsLeft),
        ]);

        return false;
    }

    /**
     * Log a user into the application.
     * R2 Fix: Emits a login audit log entry.
     */
    public static function login($user): void
    {
        // CWE-384: Regenerate session ID to prevent Session Fixation
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $_SESSION['auth_user_id'] = $user->id;

        // R2: Log session creation event
        Log::info('User session created', [
            'user_id' => $user->id,
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ]);
    }

    /**
     * Log the user out of the application.
     * R2 Fix: Emits a logout audit log entry.
     */
    public static function logout(): void
    {
        $userId = $_SESSION['auth_user_id'] ?? null;

        unset($_SESSION['auth_user_id']);
        // Regenerate session ID on logout to invalidate old session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        // R2: Log logout event
        Log::info('User logged out', [
            'user_id' => $userId,
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ]);
    }

    // -------------------------------------------------------------------------
    // R6: RBAC Foundation
    // Satisfies: ISO A.8.3 – Information Access Restriction, NIST AC-3
    // -------------------------------------------------------------------------

    /**
     * Check if the authenticated user has a given role.
     * Requires the App\Models\User model to expose a `role` attribute
     * OR a `roles()` relationship returning a collection with a `name` field.
     *
     * Usage:
     *   Auth::hasRole('admin')
     *   Auth::hasRole(['admin', 'editor'])  — true if user has ANY of the roles
     */
    public static function hasRole(string|array $roles): bool
    {
        $user = static::user();
        if (!$user) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        // Strategy 1: Single `role` string attribute on the model
        if (isset($user->role)) {
            return in_array($user->role, $roles, true);
        }

        // Strategy 2: `roles` array attribute (serialized or cast to array)
        if (isset($user->roles) && is_array($user->roles)) {
            return (bool) array_intersect($roles, $user->roles);
        }

        return false;
    }

    /**
     * Check if the authenticated user has a specific permission.
     * Requires the App\Models\User model to expose a `permissions` array attribute.
     *
     * Usage:
     *   Auth::can('edit-posts')
     *   Auth::can(['edit-posts', 'delete-posts'])  — true if user has ALL permissions
     */
    public static function can(string|array $permissions): bool
    {
        $user = static::user();
        if (!$user) {
            return false;
        }

        $permissions = is_array($permissions) ? $permissions : [$permissions];

        // Admins bypass all permission checks
        if (static::hasRole('admin') || static::hasRole('super-admin')) {
            return true;
        }

        if (isset($user->permissions) && is_array($user->permissions)) {
            // Check that user has ALL of the required permissions
            foreach ($permissions as $permission) {
                if (!in_array($permission, $user->permissions, true)) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Abort request with 403 if the user does not have the given role.
     *
     * Usage:
     *   Auth::requireRole('admin');  // throws 403 if not admin
     */
    public static function requireRole(string|array $roles): void
    {
        if (!static::hasRole($roles)) {
            Log::warning('Authorization denied — insufficient role', [
                'user_id'        => static::id(),
                'required_roles' => (array) $roles,
                'ip'             => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                'uri'            => $_SERVER['REQUEST_URI'] ?? '',
            ]);
            abort(403, 'Forbidden. You do not have permission to access this resource.');
        }
    }

    /**
     * Abort request with 403 if the user does not have the given permission.
     *
     * Usage:
     *   Auth::requirePermission('delete-posts');
     */
    public static function requirePermission(string|array $permissions): void
    {
        if (!static::can($permissions)) {
            Log::warning('Authorization denied — insufficient permission', [
                'user_id'     => static::id(),
                'permissions' => (array) $permissions,
                'ip'          => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                'uri'         => $_SERVER['REQUEST_URI'] ?? '',
            ]);
            abort(403, 'Forbidden. You do not have permission to perform this action.');
        }
    }
}
