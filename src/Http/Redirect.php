<?php

namespace LunoxHoshizaki\Http;

class Redirect
{
    protected string $url;
    protected array $flashData = [];

    public function __construct(string $url = '')
    {
        $this->url = $url;
    }

    /**
     * Create a redirect to a given URL.
     */
    public static function to(string $url): static
    {
        return new static($url);
    }

    /**
     * Create a redirect back to the previous page.
     */
    public static function back(): static
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return new static($referer);
    }

    /**
     * Flash data to the session for the next request.
     */
    public function with(string $key, mixed $value): static
    {
        $this->flashData[$key] = $value;
        return $this;
    }

    /**
     * Flash validation errors to the session.
     */
    public function withErrors(array $errors): static
    {
        $this->flashData['errors'] = $errors;
        return $this;
    }

    /**
     * Flash old input to the session.
     */
    public function withInput(array $input = []): static
    {
        if (empty($input)) {
            $input = array_merge($_GET, $_POST);
        }
        $this->flashData['old'] = $input;
        return $this;
    }

    /**
     * Execute the redirect.
     */
    public function send(): never
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Store flash data in session
        foreach ($this->flashData as $key => $value) {
            $_SESSION['_flash'][$key] = $value;
        }

        header('Location: ' . $this->url, true, 302);
        exit;
    }

    /**
     * Automatically send when the object is used in a response context.
     */
    public function __toString(): string
    {
        $this->send();
    }

    /**
     * Allow the redirect to work when returned from a controller.
     * The Application/Router will call send() on the Response,
     * but we intercept it here.
     */
    public function __destruct()
    {
        // Only auto-redirect if URL is set and headers haven't been sent
        // This allows returning a Redirect from a controller action
    }
}
