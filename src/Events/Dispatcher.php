<?php

namespace LunoxHoshizaki\Events;

class Dispatcher
{
    protected static array $listeners = [];

    /**
     * Register an event listener.
     */
    public static function listen(string $event, $listener): void
    {
        static::$listeners[$event][] = $listener;
    }

    /**
     * Dispatch an event.
     */
    public static function dispatch(object|string $event, mixed $payload = null): void
    {
        $eventName = is_object($event) ? get_class($event) : $event;
        $eventPayload = is_object($event) ? $event : $payload;

        if (isset(static::$listeners[$eventName])) {
            foreach (static::$listeners[$eventName] as $listener) {
                if (is_callable($listener)) {
                    $listener($eventPayload);
                } elseif (is_string($listener) && class_exists($listener)) {
                    $instance = new $listener;
                    if (method_exists($instance, 'handle')) {
                        $instance->handle($eventPayload);
                    }
                }
            }
        }
    }
    
    /**
     * Clear all listeners.
     */
    public static function forgetAll(): void
    {
        static::$listeners = [];
    }
}
