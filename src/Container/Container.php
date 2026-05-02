<?php

namespace LunoxHoshizaki\Container;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionParameter;

/**
 * Simple IoC (Inversion of Control) Service Container.
 *
 * Provides dependency injection and service resolution for the framework.
 * Supports binding interfaces to implementations, singleton registration,
 * and auto-resolution of class dependencies via reflection.
 *
 * Satisfies: SOLID Dependency Inversion Principle, Testability requirements.
 *
 * Usage:
 *   // Bind interface to implementation
 *   $container->bind(CacheInterface::class, FileCache::class);
 *
 *   // Bind singleton
 *   $container->singleton(Logger::class, function($c) {
 *       return new FileLogger('/path/to/logs');
 *   });
 *
 *   // Resolve (auto-injects dependencies)
 *   $logger = $container->make(Logger::class);
 *
 *   // Resolve with constructor parameters
 *   $controller = $container->make(UserController::class);
 */
class Container
{
    /**
     * The active Container instance (global singleton).
     */
    protected static ?Container $instance = null;

    /**
     * Registered bindings.
     * Format: ['abstract' => ['concrete' => ..., 'shared' => bool]]
     */
    protected array $bindings = [];

    /**
     * Resolved singleton instances.
     */
    protected array $instances = [];

    /**
     * Get the global container instance.
     */
    public static function getInstance(): static
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * Set the global container instance.
     */
    public static function setInstance(?Container $container): void
    {
        static::$instance = $container;
    }

    /**
     * Register a binding in the container.
     *
     * @param string         $abstract  Interface or class name to bind
     * @param Closure|string|null $concrete  Implementation class or factory closure
     * @param bool           $shared    If true, only one instance will be created (singleton)
     */
    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'shared' => $shared,
        ];
    }

    /**
     * Register a shared binding (singleton).
     *
     * The closure/class is resolved once, then the same instance is returned
     * for all subsequent calls.
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance as a singleton.
     *
     * Usage:
     *   $container->instance(Config::class, $configInstance);
     */
    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve an abstract type from the container.
     *
     * Resolution order:
     *   1. Pre-resolved singleton instances
     *   2. Registered bindings (closure or class name)
     *   3. Auto-resolution via reflection (if class exists)
     *
     * @throws Exception if the type cannot be resolved
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        // 1. Return pre-resolved singleton
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // 2. Check registered bindings
        $concrete = $this->bindings[$abstract]['concrete'] ?? $abstract;
        $shared = $this->bindings[$abstract]['shared'] ?? false;

        // 3. Resolve
        if ($concrete instanceof Closure) {
            $object = $concrete($this, ...$parameters);
        } elseif (is_string($concrete) && class_exists($concrete)) {
            $object = $this->build($concrete, $parameters);
        } else {
            throw new Exception("Container: Unable to resolve [{$abstract}]. No binding or class found.");
        }

        // Store if singleton
        if ($shared) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Build a concrete class instance, auto-resolving constructor dependencies.
     *
     * Uses PHP Reflection to inspect the constructor and recursively
     * resolve each typed parameter from the container.
     *
     * @throws Exception if a dependency cannot be resolved
     */
    protected function build(string $concrete, array $parameters = []): mixed
    {
        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Container: [{$concrete}] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        // No constructor — just create it
        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $resolved = $this->resolveDependencies($dependencies, $parameters);

        return $reflector->newInstanceArgs($resolved);
    }

    /**
     * Resolve constructor dependencies.
     */
    protected function resolveDependencies(array $dependencies, array $parameters = []): array
    {
        $resolved = [];

        foreach ($dependencies as $dependency) {
            /** @var ReflectionParameter $dependency */
            $name = $dependency->getName();

            // Check explicit parameters first
            if (array_key_exists($name, $parameters)) {
                $resolved[] = $parameters[$name];
                continue;
            }

            // Try to resolve from type hint
            $type = $dependency->getType();

            if ($type && !$type->isBuiltin()) {
                try {
                    $resolved[] = $this->make($type->getName());
                } catch (Exception $e) {
                    // If it has a default value, use it
                    if ($dependency->isDefaultValueAvailable()) {
                        $resolved[] = $dependency->getDefaultValue();
                    } else {
                        throw $e;
                    }
                }
            } elseif ($dependency->isDefaultValueAvailable()) {
                $resolved[] = $dependency->getDefaultValue();
            } elseif ($dependency->allowsNull()) {
                $resolved[] = null;
            } else {
                throw new Exception(
                    "Container: Unable to resolve parameter [\${$name}] in class [{$dependency->getDeclaringClass()->getName()}]."
                );
            }
        }

        return $resolved;
    }

    /**
     * Check if a binding exists.
     */
    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Flush all bindings and resolved instances.
     */
    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
    }

    /**
     * Call a method on an object, resolving its dependencies.
     *
     * Usage:
     *   $container->call([$controller, 'show'], ['id' => 5]);
     */
    public function call(array|Closure $callback, array $parameters = []): mixed
    {
        if ($callback instanceof Closure) {
            $reflection = new \ReflectionFunction($callback);
            $deps = $this->resolveDependencies($reflection->getParameters(), $parameters);
            return $callback(...$deps);
        }

        if (is_array($callback) && count($callback) === 2) {
            [$object, $method] = $callback;
            $reflection = new \ReflectionMethod($object, $method);
            $deps = $this->resolveDependencies($reflection->getParameters(), $parameters);
            return $reflection->invokeArgs(is_object($object) ? $object : null, $deps);
        }

        throw new Exception("Container: Invalid callback provided to call().");
    }
}
