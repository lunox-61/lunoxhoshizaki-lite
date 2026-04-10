<?php

/**
 * Lunox Backfire - A PHP MVC Framework
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Custom CLI Server Logging
if (php_sapi_name() === 'cli-server') {
    $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
    
    register_shutdown_function(function() use ($uri) {
        $isStatic = ($uri !== '/' && file_exists(__DIR__.'/public'.$uri));
        
        // Skip logging for static assets or development polling to avoid console spam
        if ($isStatic || str_contains($uri, '/__dev/poll') || $uri === '/favicon.ico') {
            return;
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $time = date('Y-m-d H:i:s');
        $status = http_response_code() ?: 200;
        
        $methodStr = str_pad($method, 6, " ");
        $methodColor = match ($method) {
            'GET'    => "\033[1;32m$methodStr\033[0m", // Green
            'POST'   => "\033[1;34m$methodStr\033[0m", // Blue
            'PUT'    => "\033[1;33m$methodStr\033[0m", // Yellow
            'DELETE' => "\033[1;31m$methodStr\033[0m", // Red
            'PATCH'  => "\033[1;36m$methodStr\033[0m", // Cyan
            default  => "\033[1;37m$methodStr\033[0m", // White
        };
        
        $statusColor = match (true) {
            $status >= 500 => "\033[41;97m $status \033[0m", // Red BG
            $status >= 400 => "\033[43;30m $status \033[0m", // Yellow BG
            $status >= 300 => "\033[46;30m $status \033[0m", // Cyan BG
            default        => "\033[42;30m $status \033[0m", // Green BG
        };
        
        $duration = number_format((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2);
        
        $log = "\033[90m[$time]\033[0m $statusColor $methodColor $uri \033[90m~ {$duration}ms\033[0m" . PHP_EOL;
        file_put_contents('php://stderr', $log);
    });
}

// Emulate Apache's "mod_rewrite" functionality
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
