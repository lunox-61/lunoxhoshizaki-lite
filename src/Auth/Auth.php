<?php

namespace LunoxHoshizaki\Auth;

use Exception;

class Auth
{
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
     */
    public static function attempt(array $credentials): bool
    {
        $userModel = class_exists('\App\Models\User') ? '\App\Models\User' : null;
        if (!$userModel) {
            throw new Exception("Auth attempt failed: App\Models\User class not found.");
        }

        $email = $credentials['email'] ?? '';
        $password = $credentials['password'] ?? '';

        $user = $userModel::query()->where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            static::login($user);
            return true;
        }

        return false;
    }

    /**
     * Log a user into the application.
     */
    public static function login($user): void
    {
        // Assuming the model sets the primary key to 'id' property
        $_SESSION['auth_user_id'] = $user->id;
    }

    /**
     * Log the user out of the application.
     */
    public static function logout(): void
    {
        unset($_SESSION['auth_user_id']);
    }
}
