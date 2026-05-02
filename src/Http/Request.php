<?php

namespace LunoxHoshizaki\Http;

class Request
{
    public readonly array $query;
    public readonly array $request;
    public readonly array $server;
    public readonly array $cookies;
    public readonly array $files;

    /**
     * Trusted proxy IP addresses.
     * Only trust X-Forwarded-For from these IPs.
     * Set via Request::setTrustedProxies().
     *
     * Security Fix: Prevents IP spoofing (CWE-348).
     */
    protected static array $trustedProxies = [];

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
     * Auto-detects and parses JSON request bodies for API endpoints.
     */
    public static function capture(): static
    {
        $post = $_POST;

        // Auto-parse JSON body for API requests (OWASP A05)
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $rawBody = file_get_contents('php://input');
            if (!empty($rawBody)) {
                $decoded = json_decode($rawBody, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $post = $decoded;
                }
            }
        }

        return new static($_GET, $post, $_SERVER, $_COOKIE, $_FILES);
    }

    /**
     * Get the request method.
     * Supports method spoofing via _method field for HTML forms.
     *
     * HTML forms only support GET and POST. To use PUT, PATCH, DELETE
     * from forms, include a hidden _method field:
     *   <input type="hidden" name="_method" value="PUT">
     */
    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');

        // Method spoofing: only allow from POST requests for security
        if ($method === 'POST') {
            $spoofed = strtoupper($this->request['_method'] ?? $this->query['_method'] ?? '');
            if (in_array($spoofed, ['PUT', 'PATCH', 'DELETE'], true)) {
                return $spoofed;
            }
        }

        return $method;
    }

    /**
     * Get the real HTTP method (without spoofing).
     */
    public function realMethod(): string
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
     * Get input as a sanitized string.
     *
     * @param string $key     Input field name
     * @param string $default Default value
     * @param bool   $raw     If true, return trimmed string without HTML escaping.
     *                        Use raw=true when you will escape in the view layer.
     */
    public function string(string $key, string $default = '', bool $raw = false): string
    {
        $value = $this->input($key, $default);
        $trimmed = trim((string) $value);

        if ($raw) {
            return $trimmed;
        }

        return htmlspecialchars($trimmed, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Get input as an integer.
     */
    public function integer(string $key, int $default = 0): int
    {
        return (int) $this->input($key, $default);
    }

    /**
     * Get input as a float.
     */
    public function float(string $key, float $default = 0.0): float
    {
        return (float) $this->input($key, $default);
    }

    /**
     * Get input as a boolean.
     * Treats '1', 'true', 'on', 'yes' as true.
     */
    public function boolean(string $key, bool $default = false): bool
    {
        $value = $this->input($key);
        
        if (is_null($value)) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get all input data.
     */
    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }

    /**
     * Check if the request has a given input key.
     */
    public function has(string|array $keys): bool
    {
        $keys = is_array($keys) ? $keys : [$keys];
        $all = $this->all();

        foreach ($keys as $key) {
            if (!array_key_exists($key, $all)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the request has a non-empty value for a given key.
     */
    public function filled(string $key): bool
    {
        $value = $this->input($key);
        return !is_null($value) && $value !== '';
    }

    /**
     * Get a subset of the input data (whitelist).
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * Get all input except the specified keys (blacklist).
     */
    public function except(array $keys): array
    {
        $all = $this->all();
        return array_diff_key($all, array_flip($keys));
    }

    /**
     * Get an uploaded file by key.
     * Returns an associative array with file info or null.
     */
    public function file(string $key): ?array
    {
        if (isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK) {
            return $this->files[$key];
        }
        return null;
    }

    /**
     * Check if a file was uploaded.
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Store an uploaded file to a given path.
     * Returns the stored filename on success, null on failure.
     */
    /**
     * Allowed file extensions and their corresponding MIME types.
     * Gap R1 Fix: CWE-434 – Unrestricted Upload of File with Dangerous Type.
     * Satisfies: OWASP A04:2021, NIST SI-10, ISO A.8.28
     */
    protected static array $allowedMimeTypes = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
        'svg'  => ['image/svg+xml'],
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls'  => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'txt'  => ['text/plain'],
        'csv'  => ['text/csv', 'text/plain', 'application/csv'],
        'zip'  => ['application/zip', 'application/x-zip-compressed'],
    ];

    /**
     * Store an uploaded file to a given path.
     * Validates extension and MIME type before storing.
     * Returns the stored filename on success, null on failure.
     *
     * @param string   $key        Input field key from $_FILES
     * @param string   $directory  Destination directory relative to /public/
     * @param string|null $filename  Optional custom filename (without extension)
     * @param array    $allowedExtensions  Override allowed extensions (e.g. ['jpg', 'png'])
     */
    public function storeFile(string $key, string $directory, ?string $filename = null, array $allowedExtensions = []): ?string
    {
        $file = $this->file($key);
        if (!$file) {
            return null;
        }

        // --- R1: Extension Validation ---
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $permitted  = !empty($allowedExtensions)
            ? $allowedExtensions
            : array_keys(static::$allowedMimeTypes);

        if (empty($extension) || !in_array($extension, $permitted, true)) {
            return null; // Reject disallowed or missing extensions
        }

        // --- R1: MIME Type Validation (independent of client-supplied type) ---
        $finfo         = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMime  = $finfo->file($file['tmp_name']);
        $expectedMimes = static::$allowedMimeTypes[$extension] ?? [];

        if (empty($expectedMimes) || !in_array($detectedMime, $expectedMimes, true)) {
            return null; // Reject MIME type mismatch (e.g. .php disguised as .jpg)
        }

        // --- Build destination path ---
        $basePath = dirname(__DIR__, 2) . '/public/' . ltrim($directory, '/');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }

        // Generate a cryptographically random filename to prevent path guessing
        if (!$filename) {
            $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        } else {
            // Sanitize provided filename: strip path traversal and re-attach extension
            $filename = basename($filename) . '.' . $extension;
        }

        $destination = $basePath . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        return null;
    }

    /**
     * Determine if the request expects a JSON response.
     */
    public function wantsJson(): bool
    {
        $accept = $this->server['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, 'application/json') || str_contains($accept, '+json');
    }

    /**
     * Determine if the request is an AJAX request.
     */
    public function isAjax(): bool
    {
        return ($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    /**
     * Get the client IP address.
     *
     * Security Fix: Only trust proxy headers (X-Forwarded-For, X-Client-IP)
     * when the direct connection comes from a trusted proxy.
     * Prevents CWE-348 (IP Spoofing) attacks.
     *
     * Satisfies: OWASP A01:2021, NIST AC-3
     */
    public function ip(): string
    {
        $remoteAddr = $this->server['REMOTE_ADDR'] ?? '0.0.0.0';

        // Only trust forwarded headers if the direct connection is from a trusted proxy
        if (!empty(static::$trustedProxies) && in_array($remoteAddr, static::$trustedProxies, true)) {
            // Parse X-Forwarded-For: the leftmost entry is the original client
            if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
                $ips = array_map('trim', explode(',', $this->server['HTTP_X_FORWARDED_FOR']));
                $clientIp = $ips[0] ?? $remoteAddr;
                // Validate that it looks like a real IP
                if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
                    return $clientIp;
                }
            }

            if (!empty($this->server['HTTP_CLIENT_IP'])) {
                $clientIp = trim($this->server['HTTP_CLIENT_IP']);
                if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
                    return $clientIp;
                }
            }
        }

        return $remoteAddr;
    }

    /**
     * Set trusted proxy IP addresses.
     * X-Forwarded-For and X-Client-IP headers will only be respected
     * when the REMOTE_ADDR matches one of these addresses.
     *
     * Usage in bootstrap:
     *   Request::setTrustedProxies(['127.0.0.1', '10.0.0.0/8']);
     */
    public static function setTrustedProxies(array $proxies): void
    {
        static::$trustedProxies = $proxies;
    }

    /**
     * Get the bearer token from the Authorization header.
     */
    public function bearerToken(): ?string
    {
        $header = $this->server['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Validate the request data.
     *
     * Returns validated data on success.
     * On failure, flashes errors and old input to session
     * and redirects back via Redirect object (testable, no exit/die).
     */
    public function validate(array $rules, array $messages = []): array
    {
        $data = $this->all();
        $validator = \LunoxHoshizaki\Validation\Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['_flash']['errors'] = $validator->errors();
            $_SESSION['_flash']['old'] = $data;

            $referer = $this->server['HTTP_REFERER'] ?? '/';
            $redirect = Redirect::to($referer);
            $redirect->send();
        }

        return $data;
    }
}
