<?php

namespace LunoxHoshizaki\Http;

class Request
{
    public readonly array $query;
    public readonly array $request;
    public readonly array $server;
    public readonly array $cookies;
    public readonly array $files;

    public function __construct(array $query = [], array $request = [], array $server = [], array $cookies = [], array $files = [])
    {
        $this->query = $query;
        $this->request = $request;
        $this->server = $server;
        $this->cookies = $cookies;
        $this->files = $files;
    }

    /**
     * Capture the current HTTP request.
     */
    public static function capture(): static
    {
        return new static($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
    }

    /**
     * Get the request method.
     */
    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get the request URI.
     */
    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        return rtrim($uri, '/') ?: '/';
    }

    /**
     * Get input data from request (POST or GET).
     */
    public function input(string $key, $default = null)
    {
        if (isset($this->request[$key])) {
            return $this->request[$key];
        }
        if (isset($this->query[$key])) {
            return $this->query[$key];
        }
        return $default;
    }
    
    /**
     * Get all input data.
     */
    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }

    /**
     * Validate the request data.
     */
    public function validate(array $rules): array
    {
        $data = $this->all();
        $validator = \LunoxHoshizaki\Validation\Validator::make($data, $rules);

        if ($validator->fails()) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['_flash']['errors'] = $validator->errors();
            $_SESSION['_flash']['old'] = $data;

            $referer = $this->server['HTTP_REFERER'] ?? '/';
            header("Location: " . $referer);
            exit;
        }

        return $data;
    }
}
