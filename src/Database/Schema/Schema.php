<?php

namespace LunoxHoshizaki\Database\Schema;

use LunoxHoshizaki\Database\Model;
use Closure;

class Schema
{
    /**
     * Create a new database table.
     */
    public static function create(string $table, Closure $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        
        $sql = $blueprint->toSql();
        Model::getConnection()->exec($sql);
    }

    /**
     * Modify an existing table.
     */
    public static function table(string $table, Closure $callback): void
    {
        $blueprint = new Blueprint($table);
        $blueprint->setMode('alter');
        $callback($blueprint);

        $statements = $blueprint->toAlterSql();
        foreach ($statements as $sql) {
            Model::getConnection()->exec($sql);
        }
    }

    /**
     * Drop a database table if it exists.
     */
    public static function dropIfExists(string $table): void
    {
        $sql = "DROP TABLE IF EXISTS {$table};";
        Model::getConnection()->exec($sql);
    }

    /**
     * Rename a table.
     */
    public static function rename(string $from, string $to): void
    {
        $sql = "RENAME TABLE {$from} TO {$to};";
        Model::getConnection()->exec($sql);
    }

    /**
     * Check if a table exists.
     */
    public static function hasTable(string $table): bool
    {
        $stmt = Model::getConnection()->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Check if a column exists on a table.
     */
    public static function hasColumn(string $table, string $column): bool
    {
        $stmt = Model::getConnection()->prepare(
            "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()"
        );
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
