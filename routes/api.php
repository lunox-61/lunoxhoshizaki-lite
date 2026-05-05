<?php

use LunoxHoshizaki\Routing\Router;
use LunoxHoshizaki\Security\CorsMiddleware;
use LunoxHoshizaki\Security\ApiAuthMiddleware;
use LunoxHoshizaki\Security\JwtMiddleware;
use LunoxHoshizaki\Http\ApiResponse;

/**
 * --------------------------------------------------------------------------
 * API Routes — Lunox Backfire v2.3.0
 * --------------------------------------------------------------------------
 * Semua rute terdaftar di bawah prefix /api.
 *
 * Tersedia tiga lapisan auth:
 *   1. Public          — Hanya CORS, tanpa token
 *   2. Token Auth      — Static Bearer token (APP_API_TOKEN / APP_API_TOKENS)
 *   3. JWT Auth        — JSON Web Token via JwtMiddleware (APP_JWT_SECRET)
 *
 * Gunakan helper:
 *   api_success($data)              → 200 JSON success
 *   api_error('msg', 422, $errors)  → error JSON
 *   api_paginated($items, $total, $page, $perPage)
 *   jwt_generate(['user_id' => 1])  → buat JWT token
 *   jwt_verify($token)              → verifikasi JWT
 * --------------------------------------------------------------------------
 */

// =============================================================================
// [GROUP 1] Public Endpoints — Hanya CORS, tanpa autentikasi
// =============================================================================
Router::prefix('/api')->middlewareGroup([CorsMiddleware::class])->group(function () {

    /**
     * GET /api/status
     * Health check — tidak membutuhkan token.
     */
    Router::get('/status', function () {
        return api_success([
            'version'     => $_ENV['APP_VERSION'] ?? '2.3.0',
            'environment' => $_ENV['APP_ENV']     ?? 'local',
            'php'         => PHP_VERSION,
            'timestamp'   => now(),
        ], 'API is running smoothly.');
    })->name('api.status');

});

// =============================================================================
// [GROUP 2] Token-Protected Endpoints — Static API Token (APP_API_TOKEN)
// =============================================================================
Router::prefix('/api')->middlewareGroup([CorsMiddleware::class, ApiAuthMiddleware::class])->group(function () {

    /**
     * GET /api/ping
     * Verifikasi token aktif.
     */
    Router::get('/ping', function ($request) {
        return api_success(null, 'pong');
    })->name('api.ping');

    /**
     * POST /api/connect
     * Tes koneksi klien dengan token.
     * Body: { "client": "MyApp" }
     */
    Router::post('/connect', function ($request) {
        $client = $request->string('client', 'Unknown', raw: true);
        return api_success([
            'connected_at' => now(),
            'client'       => $client,
        ], "Successfully connected to {$_ENV['APP_NAME']} API.");
    })->name('api.connect');

});

// =============================================================================
// [GROUP 3] JWT-Protected Endpoints — Bearer JWT Token (APP_JWT_SECRET)
// =============================================================================
Router::prefix('/api')->middlewareGroup([CorsMiddleware::class, JwtMiddleware::class])->group(function () {

    /**
     * GET /api/me
     * Kembalikan data user dari JWT payload.
     * JWT payload harus mengandung: user_id
     */
    Router::get('/me', function ($request) {
        $payload = $_SERVER['JWT_PAYLOAD'] ?? [];
        return api_success([
            'user_id' => $payload['user_id'] ?? null,
            'role'    => $payload['role']    ?? null,
            'exp'     => $payload['exp']     ?? null,
        ], 'Authenticated user info.');
    })->name('api.me');

});
