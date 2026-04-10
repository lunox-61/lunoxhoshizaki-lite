<?php

use LunoxHoshizaki\Routing\Router;
use LunoxHoshizaki\Security\CorsMiddleware;
use LunoxHoshizaki\Security\ApiAuthMiddleware;

/**
 * --------------------------------------------------------------------------
 * API Routes
 * --------------------------------------------------------------------------
 * Here is where you can register API routes for your application.
 * All routes here are automatically prefixed with /api.
 */

// Public API endpoints (CORS only)
Router::prefix('/api')->middlewareGroup([CorsMiddleware::class])->group(function () {

    Router::get('/status', function () {
        return [
            'success' => true,
            'message' => 'API is running smoothly.',
            'version' => $_ENV['APP_VERSION'] ?? '2.0.0'
        ];
    })->name('api.status');

});

// Protected API endpoints (CORS + API Token)
Router::prefix('/api')->middlewareGroup([CorsMiddleware::class, ApiAuthMiddleware::class])->group(function () {

    Router::get('/user', function ($request) {
        return [
            'success' => true,
            'data' => [
                'id' => 1,
                'name' => 'Lunox Hoshizaki',
                'role' => 'Administrator'
            ]
        ];
    })->name('api.user');

    Router::post('/connect', function ($request) {
        $client = $request->input('client', 'Unknown');
        return [
            'success' => true,
            'message' => "Successfully connected to {$_ENV['APP_NAME']} API.",
            'client'  => $client
        ];
    })->name('api.connect');

});
