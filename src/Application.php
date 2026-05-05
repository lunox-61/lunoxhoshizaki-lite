<?php

namespace LunoxHoshizaki;

use Dotenv\Dotenv;
use LunoxHoshizaki\Container\Container;
use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use LunoxHoshizaki\Routing\Router;
use Exception;
use Throwable;

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
     * The IoC service container.
     */
    protected Container $container;

    /**
     * Create a new application instance.
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        static::$instance = $this;

        $this->container = Container::getInstance();
        $this->container->instance(Application::class, $this);

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
     * Get the IoC container.
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Bootstrap the application.
     */
    protected function bootstrap(): void
    {
        $this->loadEnvironment();
        $this->configureTrustedProxies();
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
     * Configure trusted proxies from environment.
     *
     * Set TRUSTED_PROXIES in .env as a comma-separated list of IP addresses.
     * Example: TRUSTED_PROXIES=127.0.0.1,10.0.0.1,172.16.0.0/12
     *
     * This ensures X-Forwarded-For and X-Client-IP headers are only trusted
     * when the request comes from a known proxy, preventing IP spoofing.
     */
    protected function configureTrustedProxies(): void
    {
        $proxies = $_ENV['TRUSTED_PROXIES'] ?? '';
        if (!empty($proxies)) {
            $proxyList = array_map('trim', explode(',', $proxies));
            $proxyList = array_filter($proxyList);
            Request::setTrustedProxies($proxyList);
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
                // ISO 27001 Security: Secure Session Cookie Parameters
                $currentParams = session_get_cookie_params();
                $isProduction = ($_ENV['APP_ENV'] ?? 'local') === 'production';
                $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

                session_set_cookie_params([
                    'lifetime' => $currentParams['lifetime'],
                    'path' => $currentParams['path'],
                    'domain' => $currentParams['domain'],
                    'secure' => $isProduction || $isHttps,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);

                ini_set('session.use_strict_mode', '1');

                // Ensure session is started for flash messaging
                session_start();
            }

            // Cycle flash data: move current flash to old, and clear current for new data
            $_SESSION['_flash_old'] = $_SESSION['_flash'] ?? [];
            $_SESSION['_flash'] = [];

            $request = Request::capture();
            $response = $this->dispatchToRouter($request);
            $response->send();
        } catch (Throwable $e) {
            $this->renderException($e);
        }
    }

    /**
     * Render the exception into an HTTP response.
     */
    protected function renderException(Throwable $e): void
    {
        $statusCode = 500;
        $message = $e->getMessage();

        // Specific HTTP status code detection
        if (str_contains($message, 'Not Found')) {
            $statusCode = 404;
        }

        // Debug mode is controlled strictly by APP_DEBUG=true
        // APP_ENV does NOT automatically enable debug output
        $appDebug = $_ENV['APP_DEBUG'] ?? 'false';
        $isDebug  = ($appDebug === 'true');

        try {
            if ($isDebug) {
                // Local/debug view — shows full exception, code snippet, stack trace
                $content = \LunoxHoshizaki\View\View::make('errors.debug', [
                    'exception' => $e,
                    'code'      => $statusCode,
                ]);
            } else {
                // Production view — safe, no internal details exposed
                $content = \LunoxHoshizaki\View\View::make('errors.error', [
                    'code'    => $statusCode,
                    'message' => 'Internal Server Error',
                ]);
            }
            $response = new Response($content, $statusCode);
        } catch (Throwable $viewException) {
            // Last-resort plaintext fallback
            $fallback = $isDebug
                ? "Error {$statusCode}: {$message}"
                : "Error {$statusCode}: An unexpected error occurred.";
            $response = new Response($fallback, $statusCode);
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
        } catch (Throwable $e) {
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
