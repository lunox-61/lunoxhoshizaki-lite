<?php

namespace LunoxHoshizaki\Http;

class Response
{
    protected string $content;
    protected int $statusCode;
    protected array $headers;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Send the HTTP response.
     */
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }
        echo $this->content;
    }

    // -------------------------------------------------------------------------
    // Factory Methods
    // -------------------------------------------------------------------------

    /**
     * Create a JSON response.
     *
     * Usage:
     *   return Response::json(['status' => 'ok'], 200);
     *   return Response::json($user->toArray(), 201);
     */
    public static function json(mixed $data, int $statusCode = 200, int $options = 0): static
    {
        $content = json_encode($data, $options | JSON_UNESCAPED_UNICODE);

        if ($content === false) {
            $content = json_encode(['error' => 'JSON encoding failed: ' . json_last_error_msg()]);
            $statusCode = 500;
        }

        return new static($content, $statusCode, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }

    /**
     * Create a file download response.
     *
     * Usage:
     *   return Response::download('/path/to/file.pdf', 'report.pdf');
     */
    public static function download(string $filePath, ?string $filename = null, array $headers = []): static
    {
        if (!file_exists($filePath)) {
            return new static('File not found.', 404);
        }

        $filename = $filename ?? basename($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        $fileSize = filesize($filePath);

        $defaultHeaders = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => (string) $fileSize,
            'Cache-Control' => 'no-cache, must-revalidate',
        ];

        $response = new static('', 200, array_merge($defaultHeaders, $headers));

        // We need to override send() behavior for file streaming
        return new class('', 200, array_merge($defaultHeaders, $headers), $filePath) extends Response {
            private string $filePath;

            public function __construct(string $content, int $statusCode, array $headers, string $filePath)
            {
                parent::__construct($content, $statusCode, $headers);
                $this->filePath = $filePath;
            }

            public function send(): void
            {
                if (!headers_sent()) {
                    http_response_code($this->statusCode);
                    foreach ($this->headers as $name => $value) {
                        header("{$name}: {$value}");
                    }
                }
                readfile($this->filePath);
            }
        };
    }

    /**
     * Create a streamed response with a callback.
     *
     * Usage:
     *   return Response::stream(function() {
     *       echo "streaming data...";
     *   }, 200, ['Content-Type' => 'text/event-stream']);
     */
    public static function stream(callable $callback, int $statusCode = 200, array $headers = []): static
    {
        return new class('', $statusCode, $headers, $callback) extends Response {
            /** @var callable */
            private $streamCallback;

            public function __construct(string $content, int $statusCode, array $headers, callable $callback)
            {
                parent::__construct($content, $statusCode, $headers);
                $this->streamCallback = $callback;
            }

            public function send(): void
            {
                if (!headers_sent()) {
                    http_response_code($this->statusCode);
                    foreach ($this->headers as $name => $value) {
                        header("{$name}: {$value}");
                    }
                }
                ($this->streamCallback)();
            }
        };
    }

    /**
     * Create a "no content" response (204).
     */
    public static function noContent(int $statusCode = 204): static
    {
        return new static('', $statusCode);
    }
}
