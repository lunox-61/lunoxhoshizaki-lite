<?php

use LunoxHoshizaki\Routing\Router;
use LunoxHoshizaki\Security\CorsMiddleware;
use LunoxHoshizaki\Security\ApiAuthMiddleware;

/**
 * --------------------------------------------------------------------------
 * API Routes
 * --------------------------------------------------------------------------
 * Here is where you can register API routes for your application.
 * All routes here should be prefixed with /api.
 */

// Public API endpoints
Router::get('/api/status', function() {
    return [
        'success' => true,
        'message' => 'API is running smoothly.',
        'version' => $_ENV['APP_VERSION'] ?? '1.2.0'
    ];
})->middleware([CorsMiddleware::class]);

// Protected API endpoints (Require API_TOKEN)
Router::get('/api/user', function($request) {
    return [
        'success' => true,
        'data' => [
            'id' => 1,
            'name' => 'Lunox Hoshizaki',
            'role' => 'Administrator'
        ]
    ];
})->middleware([CorsMiddleware::class, ApiAuthMiddleware::class]);

Router::post('/api/connect', function($request) {
    $client = $request->input('client', 'Unknown');
    return [
        'success' => true,
        'message' => "Successfully connected to {$_ENV['APP_NAME']} API.",
        'client'  => $client
    ];
})->middleware([CorsMiddleware::class, ApiAuthMiddleware::class]);
