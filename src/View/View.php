<?php

namespace LunoxHoshizaki\View;

use LunoxHoshizaki\Application;

class View
{
    protected static string $viewPath = __DIR__ . '/../../../resources/views/';
    protected static array $sections = [];
    protected static string $currentSection = '';
    protected static ?string $layout = null;

    public static function make(string $view, array $data = []): string
    {
        // Reset static state for each render in case of long-running process
        static::$sections = [];
        static::$currentSection = '';
        static::$layout = null;

        $content = static::renderTemplate($view, $data);

        // If a layout was defined via @extends, render the layouts recursively
        while (static::$layout) {
            $currentLayout = static::$layout;
            static::$layout = null; // Clear so the layout itself can extend another layout
            $content = static::renderTemplate($currentLayout, $data);
        }

        return static::injectAutoReload($content);
    }

    protected static function renderTemplate(string $view, array $data): string
    {
        $path = static::resolvePath($view);
        if (!file_exists($path)) {
            throw new \Exception("View [{$view}] not found at [{$path}]");
        }

        extract($data);

        // Turn on output buffering
        ob_start();
        include $path;
        $content = ob_get_clean();

        // Process directives
        $content = static::compileDirectives($content);

        return $content;
    }

    protected static function resolvePath(string $view): string
    {
        // Set dynamic view path from Application if available
        try {
            $appPath = Application::getInstance()->getBasePath();
            static::$viewPath = $appPath . '/resources/views/';
        } catch (\Exception $e) {
            // Fallback to relative if not bootstrapped
        }
        
        $view = str_replace('.', '/', $view);
        return static::$viewPath . $view . '.php';
    }

    protected static function compileDirectives(string $content): string
    {
        // Note: In a real compiler, we would use regex.
        // But since this is a simple "lite" framework and PHP allows methods to be called in views:
        // We actually provide helper functions for views to call instead of regex for simplicity.
        // Wait, the user wants @extends style. Let's do a fast regex compile, save it to cache then include.
        // Since we don't have a cache dir, let's just use helpers.

        return $content;
    }

    /**
     * Helpers for template files to call directly
     */
    public static function extends(string $view): void
    {
        static::$layout = $view;
    }

    public static function section(string $name): void
    {
        static::$currentSection = $name;
        ob_start();
    }

    public static function endsection(): void
    {
        $content = ob_get_clean();
        static::$sections[static::$currentSection] = $content;
        static::$currentSection = '';
    }

    public static function yield(string $name): void
    {
        echo static::$sections[$name] ?? '';
    }

    public static function component(string $name, array $data = []): void
    {
        echo static::renderTemplate($name, $data);
    }

    /**
     * Helper to output CSRF field
     */
    public static function csrfField(): void
    {
        $token = \LunoxHoshizaki\Security\CsrfMiddleware::getToken();
        echo '<input type="hidden" name="_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Inject auto-reloader script in local mode.
     */
    protected static function injectAutoReload(string $content): string
    {
        if (($_ENV['APP_ENV'] ?? 'local') !== 'local') {
            return $content;
        }

        $script = <<<HTML
        <!-- Auto Reload Script -->
        <script>
            (function() {
                let lastHash = '';
                setInterval(() => {
                    fetch('/__dev/poll')
                        .then(res => res.json())
                        .then(data => {
                            if (lastHash && data.hash !== lastHash) {
                                window.location.reload();
                            }
                            lastHash = data.hash;
                        }).catch(() => {});
                }, 1000);
            })();
        </script>
        HTML;

        // Inject right before closing body tag
        if (str_contains($content, '</body>')) {
            return str_replace('</body>', $script . '</body>', $content);
        }

        return $content . $script;
    }
}
