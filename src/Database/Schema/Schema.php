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
     * Drop a database table if it exists.
     */
    public static function dropIfExists(string $table): void
    {
        $sql = "DROP TABLE IF EXISTS {$table};";
        Model::getConnection()->exec($sql);
    }
}
