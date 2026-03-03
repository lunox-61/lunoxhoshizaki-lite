<?php

namespace LunoxHoshizaki;

use Dotenv\Dotenv;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use LunoxHoshizaki\Routing\Router;
use Exception;

class Application
{
    /**
     * The framework base path.
     */
    protected string $basePath;

    /**
     * The active Application instance.
     */
    protected static ?Application $instance = null;

    /**
     * Create a new application instance.
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        static::$instance = $this;

        $this->bootstrap();
    }

    /**
     * Get the active application instance.
     */
    public static function getInstance(): static
    {
        if (is_null(static::$instance)) {
            throw new Exception("Application instance not created yet.");
        }
        return static::$instance;
    }

    /**
     * Bootstrap the application.
     */
    protected function bootstrap(): void
    {
        $this->loadEnvironment();
        $this->loadRoutes();
    }

    /**
     * Load environment variables from .env file.
     */
    protected function loadEnvironment(): void
    {
        if (file_exists($this->basePath . '/.env')) {
            $dotenv = Dotenv::createImmutable($this->basePath);
            $dotenv->load();
        }
    }

    /**
     * Load application routes.
     */
    protected function loadRoutes(): void
    {
        $routesPath = $this->basePath . '/routes/web.php';
        if (file_exists($routesPath)) {
            require $routesPath;
        }

        $apiPath = $this->basePath . '/routes/api.php';
        if (file_exists($apiPath)) {
            require $apiPath;
        }
    }

    /**
     * Handle the incoming HTTP request.
     */
    public function handleRequest(): void
    {
        try {
            if ($this->isDownForMaintenance() && php_sapi_name() !== 'cli') {
                $this->renderMaintenanceMode();
                return;
            }

            if (session_status() === PHP_SESSION_NONE) {
                // Ensure session is started for flash messaging
                session_start();
            }

            // Cycle flash data: move current flash to old, and clear current for new data
            $_SESSION['_flash_old'] = $_SESSION['_flash'] ?? [];
            $_SESSION['_flash'] = [];

            $request = Request::capture();
            $response = $this->dispatchToRouter($request);
            $response->send();
        } catch (Exception $e) {
            $this->renderException($e);
        }
    }

    /**
     * Render the exception into an HTTP response.
     */
    protected function renderException(Exception $e): void
    {
        $statusCode = 500;
        $message = $e->getMessage();

        // Specific handling for routing errors can be added here
        if (str_contains($message, 'Not Found')) {
            $statusCode = 404;
        }

        try {
            $content = \LunoxHoshizaki\View\View::make('errors.error', [
                'code' => $statusCode,
                'message' => 'Internal Server Error',
                'exception' => $e
            ]);
            $response = new Response($content, $statusCode);
        } catch (Exception $viewException) {
            // Fallback if view fails
            $response = new Response("Error {$statusCode}: {$message}", $statusCode);
        }

        $response->send();
    }

    /**
     * Dispatch the request to the router.
     */
    protected function dispatchToRouter(Request $request): Response
    {
        return Router::dispatch($request);
    }

    /**
     * Determine if the application is currently down for maintenance.
     */
    public function isDownForMaintenance(): bool
    {
        return file_exists($this->basePath . '/storage/framework/down');
    }

    /**
     * Render the maintenance mode response.
     */
    protected function renderMaintenanceMode(): void
    {
        try {
            $content = \LunoxHoshizaki\View\View::make('errors.503');
            $response = new Response($content, 503);
            $response->send();
            return;
        } catch (\Exception $e) {
            // Fallback plain 503
            http_response_code(503);
            echo "<h1>503 Service Unavailable</h1><p>The application is currently down for maintenance. Please check back later.</p>";
            return;
        }
    }

    /**
     * Get the base path of the application.
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }
}
