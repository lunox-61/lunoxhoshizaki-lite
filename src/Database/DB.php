<?php

namespace LunoxHoshizaki\Database;

use PDO;
use Exception;
use Closure;

class DB
{
    /**
     * Execute a raw SQL query with bindings.
     */
    public static function raw(string $sql, array $bindings = []): array
    {
        $stmt = Model::getConnection()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /**
     * Execute a raw SQL statement (INSERT, UPDATE, DELETE).
     */
    public static function statement(string $sql, array $bindings = []): bool
    {
        $stmt = Model::getConnection()->prepare($sql);
        return $stmt->execute($bindings);
    }

    /**
     * Begin a database transaction.
     */
    public static function beginTransaction(): bool
    {
        return Model::getConnection()->beginTransaction();
    }

    /**
     * Commit the active database transaction.
     */
    public static function commit(): bool
    {
        return Model::getConnection()->commit();
    }

    /**
     * Rollback the active database transaction.
     */
    public static function rollBack(): bool
    {
        return Model::getConnection()->rollBack();
    }

    /**
     * Execute a callback within a transaction.
     * Automatically commits on success, rolls back on exception.
     *
     * Usage:
     *   DB::transaction(function() {
     *       User::create([...]);
     *       Profile::create([...]);
     *   });
     */
    public static function transaction(Closure $callback): mixed
    {
        static::beginTransaction();

        try {
            $result = $callback();
            static::commit();
            return $result;
        } catch (Exception $e) {
            static::rollBack();
            throw $e;
        }
    }

    /**
     * Get the PDO connection instance.
     */
    public static function connection(): PDO
    {
        return Model::getConnection();
    }

    /**
     * Get the last inserted ID.
     */
    public static function lastInsertId(): string
    {
        return Model::getConnection()->lastInsertId();
    }
}
