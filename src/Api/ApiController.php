<?php

namespace LunoxHoshizaki\Api;

use LunoxHoshizaki\Http\ApiResponse;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;

/**
 * ApiController — Base Controller for API Endpoints
 *
 * Extend this class in your API controllers to gain built-in shortcuts
 * for generating standardized JSON responses.
 *
 * Usage:
 *   class UserController extends ApiController
 *   {
 *       public function index(Request $request): Response
 *       {
 *           $users = User::all();
 *           return $this->success($users);
 *       }
 *
 *       public function store(Request $request): Response
 *       {
 *           $data = $request->validate([...]);
 *           $user = User::create($data);
 *           return $this->created($user, 'User created.');
 *       }
 *   }
 */
abstract class ApiController
{
    /**
     * Return a success response.
     */
    protected function success(
        mixed  $data       = null,
        string $message    = 'Success.',
        int    $statusCode = 200,
        array  $meta       = []
    ): Response {
        return ApiResponse::success($data, $message, $statusCode, $meta);
    }

    /**
     * Return a created (201) response.
     */
    protected function created(mixed $data = null, string $message = 'Resource created successfully.'): Response
    {
        return ApiResponse::created($data, $message);
    }

    /**
     * Return an error response.
     */
    protected function error(string $message = 'An error occurred.', int $statusCode = 400, mixed $errors = null): Response
    {
        return ApiResponse::error($message, $statusCode, $errors);
    }

    /**
     * Return a validation error (422) response.
     */
    protected function validationError(mixed $errors, string $message = 'The given data was invalid.'): Response
    {
        return ApiResponse::validationError($errors, $message);
    }

    /**
     * Return a paginated response.
     */
    protected function paginated(array $items, int $total, int $page, int $perPage, string $message = 'Success.'): Response
    {
        return ApiResponse::paginated($items, $total, $page, $perPage, $message);
    }

    /**
     * Return a 404 Not Found response.
     */
    protected function notFound(string $message = 'Resource not found.'): Response
    {
        return ApiResponse::notFound($message);
    }

    /**
     * Return a 401 Unauthorized response.
     */
    protected function unauthorized(string $message = 'Unauthorized.'): Response
    {
        return ApiResponse::unauthorized($message);
    }

    /**
     * Return a 403 Forbidden response.
     */
    protected function forbidden(string $message = 'Forbidden.'): Response
    {
        return ApiResponse::forbidden($message);
    }

    /**
     * Return a 204 No Content response.
     */
    protected function noContent(): Response
    {
        return ApiResponse::noContent();
    }

    /**
     * Validate request data for API endpoints.
     *
     * Unlike the web validate(), this does NOT redirect on failure —
     * it returns a 422 JSON response instead.
     *
     * @throws \RuntimeException Never throws; returns a Response on failure.
     */
    protected function validateApi(Request $request, array $rules, array $messages = []): array|Response
    {
        $data      = $request->all();
        $validator = \LunoxHoshizaki\Validation\Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        return $data;
    }

    /**
     * Get the authenticated user's ID from the Bearer token.
     *
     * Returns null if no valid token is present.
     * For more robust auth, use JWT or session-based lookup.
     */
    protected function getAuthUserId(Request $request): ?int
    {
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }

        // Decode JWT if the token looks like one (header.payload.signature)
        if (substr_count($token, '.') === 2) {
            return \LunoxHoshizaki\Security\JwtGuard::getUserId($token);
        }

        return null;
    }
}
