<?php

namespace LunoxHoshizaki\Log;

class Log
{
    /**
     * Log levels as defined by PSR-3.
     */
    protected const LEVELS = ['debug', 'info', 'warning', 'error', 'critical'];

    /**
     * Log a debug message.
     */
    public static function debug(string $message, array $context = []): void
    {
        static::write('debug', $message, $context);
    }

    /**
     * Log an informational message.
     */
    public static function info(string $message, array $context = []): void
    {
        static::write('info', $message, $context);
    }

    /**
     * Log a warning message.
     */
    public static function warning(string $message, array $context = []): void
    {
        static::write('warning', $message, $context);
    }

    /**
     * Log an error message.
     */
    public static function error(string $message, array $context = []): void
    {
        static::write('error', $message, $context);
    }

    /**
     * Log a critical message.
     */
    public static function critical(string $message, array $context = []): void
    {
        static::write('critical', $message, $context);
    }

    /**
     * Write a log entry to the daily log file.
     * Format: [YYYY-MM-DD HH:MM:SS] LEVEL: message {context}
     */
    protected static function write(string $level, string $message, array $context = []): void
    {
        $logDir = static::getLogPath();
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $filename = $logDir . '/lunox-' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);

        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        
        $entry = "[{$timestamp}] {$levelUpper}: {$message}{$contextStr}" . PHP_EOL;

        file_put_contents($filename, $entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Get the log directory path.
     */
    protected static function getLogPath(): string
    {
        // Try to resolve from Application base path
        try {
            $basePath = \LunoxHoshizaki\Application::getInstance()->getBasePath();
            return $basePath . '/storage/logs';
        } catch (\Exception $e) {
            return dirname(__DIR__, 2) . '/storage/logs';
        }
    }

    /**
     * Clean up old log files older than the given number of days.
     */
    public static function cleanup(int $days = 30): int
    {
        $logDir = static::getLogPath();
        if (!is_dir($logDir)) {
            return 0;
        }

        $count = 0;
        $cutoff = time() - ($days * 86400);
        
        foreach (glob($logDir . '/lunox-*.log') as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                $count++;
            }
        }

        return $count;
    }
}
