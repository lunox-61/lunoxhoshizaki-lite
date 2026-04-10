<?php

namespace LunoxHoshizaki\Database\Schema;

class Blueprint
{
    protected string $table;
    protected array $columns = [];
    protected string $mode = 'create'; // 'create' or 'alter'
    protected array $alterStatements = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Set the blueprint mode (create or alter).
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    // --- Column Type Methods ---

    public function id(string $name = 'id'): self
    {
        return $this->addColumn($name, "{$name} BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY");
    }

    public function bigIncrements(string $name): self
    {
        return $this->addColumn($name, "{$name} BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY");
    }

    public function string(string $name, int $length = 255): self
    {
        return $this->addColumn($name, "{$name} VARCHAR({$length})");
    }

    public function text(string $name): self
    {
        return $this->addColumn($name, "{$name} TEXT");
    }

    public function longText(string $name): self
    {
        return $this->addColumn($name, "{$name} LONGTEXT");
    }

    public function integer(string $name): self
    {
        return $this->addColumn($name, "{$name} INT");
    }

    public function bigInteger(string $name): self
    {
        return $this->addColumn($name, "{$name} BIGINT");
    }

    public function unsignedBigInteger(string $name): self
    {
        return $this->addColumn($name, "{$name} BIGINT UNSIGNED");
    }

    public function tinyInteger(string $name): self
    {
        return $this->addColumn($name, "{$name} TINYINT");
    }

    public function float(string $name, int $precision = 8, int $scale = 2): self
    {
        return $this->addColumn($name, "{$name} FLOAT({$precision},{$scale})");
    }

    public function decimal(string $name, int $precision = 8, int $scale = 2): self
    {
        return $this->addColumn($name, "{$name} DECIMAL({$precision},{$scale})");
    }

    public function boolean(string $name): self
    {
        return $this->addColumn($name, "{$name} TINYINT(1) DEFAULT 0");
    }

    public function date(string $name): self
    {
        return $this->addColumn($name, "{$name} DATE");
    }

    public function dateTime(string $name): self
    {
        return $this->addColumn($name, "{$name} DATETIME");
    }

    public function timestamp(string $name): self
    {
        return $this->addColumn($name, "{$name} TIMESTAMP NULL");
    }

    public function json(string $name): self
    {
        return $this->addColumn($name, "{$name} JSON");
    }

    public function enum(string $name, array $values): self
    {
        $enumValues = implode("','", $values);
        return $this->addColumn($name, "{$name} ENUM('{$enumValues}')");
    }

    public function timestamps(): self
    {
        $this->addColumn('created_at', "created_at TIMESTAMP NULL");
        $this->addColumn('updated_at', "updated_at TIMESTAMP NULL");
        return $this;
    }

    public function softDeletes(): self
    {
        return $this->addColumn('deleted_at', "deleted_at TIMESTAMP NULL");
    }

    /**
     * Add a nullable modifier to the last added column.
     */
    public function nullable(): self
    {
        if (!empty($this->columns)) {
            $lastKey = array_key_last($this->columns);
            $this->columns[$lastKey] .= ' NULL';
        }
        return $this;
    }

    /**
     * Add a default value modifier to the last added column.
     */
    public function default(mixed $value): self
    {
        if (!empty($this->columns)) {
            $lastKey = array_key_last($this->columns);
            if (is_string($value)) {
                $this->columns[$lastKey] .= " DEFAULT '{$value}'";
            } elseif (is_null($value)) {
                $this->columns[$lastKey] .= " DEFAULT NULL";
            } else {
                $this->columns[$lastKey] .= " DEFAULT {$value}";
            }
        }
        return $this;
    }

    /**
     * Add a column definition (internal).
     */
    protected function addColumn(string $name, string $definition): self
    {
        if ($this->mode === 'alter') {
            $this->alterStatements[] = "ADD COLUMN {$definition}";
        }
        $this->columns[] = $definition;
        return $this;
    }

    // --- Alter Table Operations ---

    /**
     * Drop a column from the table.
     */
    public function dropColumn(string|array $columns): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        foreach ($columns as $column) {
            $this->alterStatements[] = "DROP COLUMN {$column}";
        }
        return $this;
    }

    /**
     * Rename a column.
     */
    public function renameColumn(string $from, string $to): self
    {
        $this->alterStatements[] = "RENAME COLUMN {$from} TO {$to}";
        return $this;
    }

    /**
     * Add an index.
     */
    public function index(string|array $columns, ?string $name = null): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $columnList = implode(', ', $columns);
        $name = $name ?: 'idx_' . $this->table . '_' . implode('_', $columns);

        if ($this->mode === 'alter') {
            $this->alterStatements[] = "ADD INDEX {$name} ({$columnList})";
        } else {
            $this->columns[] = "INDEX {$name} ({$columnList})";
        }
        return $this;
    }

    /**
     * Add a unique index.
     */
    public function unique(string|array $columns, ?string $name = null): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $columnList = implode(', ', $columns);
        $name = $name ?: 'uniq_' . $this->table . '_' . implode('_', $columns);

        if ($this->mode === 'alter') {
            $this->alterStatements[] = "ADD UNIQUE INDEX {$name} ({$columnList})";
        } else {
            $this->columns[] = "UNIQUE INDEX {$name} ({$columnList})";
        }
        return $this;
    }

    /**
     * Add a foreign key constraint.
     */
    public function foreign(string $column): ForeignKeyDefinition
    {
        return new ForeignKeyDefinition($this, $column);
    }

    /**
     * Add a raw foreign key constraint (used internally).
     */
    public function addForeignKey(string $sql): void
    {
        if ($this->mode === 'alter') {
            $this->alterStatements[] = "ADD {$sql}";
        } else {
            $this->columns[] = $sql;
        }
    }

    // --- SQL Generation ---

    /**
     * Compile the blueprint into a CREATE TABLE SQL statement.
     */
    public function toSql(): string
    {
        $columnsSql = implode(', ', $this->columns);
        return "CREATE TABLE IF NOT EXISTS {$this->table} ({$columnsSql}) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

    /**
     * Compile the blueprint into ALTER TABLE SQL statements.
     */
    public function toAlterSql(): array
    {
        $statements = [];
        foreach ($this->alterStatements as $stmt) {
            $statements[] = "ALTER TABLE {$this->table} {$stmt};";
        }
        return $statements;
    }
}

/**
 * Helper class for building foreign key definitions.
 */
class ForeignKeyDefinition
{
    protected Blueprint $blueprint;
    protected string $column;
    protected string $referencesTable = '';
    protected string $referencesColumn = '';
    protected string $onDelete = 'RESTRICT';
    protected string $onUpdate = 'CASCADE';

    public function __construct(Blueprint $blueprint, string $column)
    {
        $this->blueprint = $blueprint;
        $this->column = $column;
    }

    public function references(string $column): self
    {
        $this->referencesColumn = $column;
        return $this;
    }

    public function on(string $table): self
    {
        $this->referencesTable = $table;
        $this->build();
        return $this;
    }

    public function onDelete(string $action): self
    {
        $this->onDelete = strtoupper($action);
        return $this;
    }

    public function onUpdate(string $action): self
    {
        $this->onUpdate = strtoupper($action);
        return $this;
    }

    protected function build(): void
    {
        $sql = "FOREIGN KEY ({$this->column}) REFERENCES {$this->referencesTable}({$this->referencesColumn}) ON DELETE {$this->onDelete} ON UPDATE {$this->onUpdate}";
        $this->blueprint->addForeignKey($sql);
    }
}
