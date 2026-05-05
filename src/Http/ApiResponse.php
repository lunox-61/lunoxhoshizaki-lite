<?php

namespace LunoxHoshizaki\Http;

/**
 * ApiResponse — Standardized JSON API Response Builder
 *
 * Provides a consistent envelope structure for all API responses:
 *
 *   { "success": true,  "message": "...", "data": {...}, "meta": {...} }
 *   { "success": false, "message": "...", "errors": {...}, "code": 422 }
 *
 * Usage:
 *   return ApiResponse::success($data);
 *   return ApiResponse::success($data, 'Created successfully.', 201);
 *   return ApiResponse::error('Validation failed.', 422, $errors);
 *   return ApiResponse::paginated($items, $total, $page, $perPage);
 *   return ApiResponse::noContent();
 */
class ApiResponse
{
    /**
     * Return a successful JSON response.
     *
     * @param  mixed       $data       Data payload (array, object, or null)
     * @param  string      $message    Human-readable success message
     * @param  int         $statusCode HTTP status code (default 200)
     * @param  array       $meta       Optional metadata (e.g. pagination, timestamps)
     * @param  int         $options    json_encode flags
     */
    public static function success(
        mixed  $data       = null,
        string $message    = 'Success.',
        int    $statusCode = 200,
        array  $meta       = [],
        int    $options    = 0
    ): Response {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $payload['data'] = $data;
        }

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return Response::json($payload, $statusCode, $options);
    }

    /**
     * Return an error JSON response.
     *
     * @param  string  $message    Human-readable error message
     * @param  int     $statusCode HTTP status code (default 400)
     * @param  mixed   $errors     Detailed errors (e.g. validation field errors)
     * @param  int     $options    json_encode flags
     */
    public static function error(
        string $message    = 'An error occurred.',
        int    $statusCode = 400,
        mixed  $errors     = null,
        int    $options    = 0
    ): Response {
        $payload = [
            'success' => false,
            'message' => $message,
            'code'    => $statusCode,
        ];

        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return Response::json($payload, $statusCode, $options);
    }

    /**
     * Return a standardized paginated JSON response.
     *
     * @param  array   $items    The paginated data items (current page)
     * @param  int     $total    Total number of items across all pages
     * @param  int     $page     Current page number (1-indexed)
     * @param  int     $perPage  Number of items per page
     * @param  string  $message  Optional message
     */
    public static function paginated(
        array  $items,
        int    $total,
        int    $page,
        int    $perPage,
        string $message = 'Success.'
    ): Response {
        $lastPage = (int) ceil($total / max($perPage, 1));

        return static::success($items, $message, 200, [
            'pagination' => [
                'total'       => $total,
                'per_page'    => $perPage,
                'current_page'=> $page,
                'last_page'   => $lastPage,
                'from'        => ($page - 1) * $perPage + 1,
                'to'          => min($page * $perPage, $total),
            ],
        ]);
    }

    /**
     * Return a 204 No Content response.
     */
    public static function noContent(): Response
    {
        return Response::noContent(204);
    }

    /**
     * Return a 201 Created response.
     *
     * @param  mixed  $data     The created resource
     * @param  string $message
     */
    public static function created(mixed $data = null, string $message = 'Resource created successfully.'): Response
    {
        return static::success($data, $message, 201);
    }

    /**
     * Return a 401 Unauthorized response.
     *
     * @param  string $message
     */
    public static function unauthorized(string $message = 'Unauthorized. Invalid or missing API credentials.'): Response
    {
        return static::error($message, 401);
    }

    /**
     * Return a 403 Forbidden response.
     *
     * @param  string $message
     */
    public static function forbidden(string $message = 'Forbidden. You do not have permission to access this resource.'): Response
    {
        return static::error($message, 403);
    }

    /**
     * Return a 404 Not Found response.
     *
     * @param  string $message
     */
    public static function notFound(string $message = 'Resource not found.'): Response
    {
        return static::error($message, 404);
    }

    /**
     * Return a 422 Unprocessable Entity response (validation errors).
     *
     * @param  mixed  $errors  Validation error bag
     * @param  string $message
     */
    public static function validationError(mixed $errors, string $message = 'The given data was invalid.'): Response
    {
        return static::error($message, 422, $errors);
    }

    /**
     * Return a 429 Too Many Requests response.
     *
     * @param  string $message
     * @param  int    $retryAfter Seconds until the client can retry
     */
    public static function tooManyRequests(string $message = 'Too many requests. Please slow down.', int $retryAfter = 60): Response
    {
        $response = static::error($message, 429);
        $response->setHeader('Retry-After', (string) $retryAfter);
        return $response;
    }

    /**
     * Return a 500 Internal Server Error response.
     *
     * @param  string $message
     */
    public static function serverError(string $message = 'Internal server error.'): Response
    {
        return static::error($message, 500);
    }
}
