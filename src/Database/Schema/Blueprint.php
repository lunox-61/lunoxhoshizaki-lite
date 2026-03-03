<?php

namespace LunoxHoshizaki\Database\Schema;

class Blueprint
{
    protected string $table;
    protected array $columns = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(string $name = 'id'): self
    {
        $this->columns[] = "{$name} INT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function string(string $name, int $length = 255): self
    {
        $this->columns[] = "{$name} VARCHAR({$length})";
        return $this;
    }

    public function text(string $name): self
    {
        $this->columns[] = "{$name} TEXT";
        return $this;
    }

    public function integer(string $name): self
    {
        $this->columns[] = "{$name} INT";
        return $this;
    }

    public function boolean(string $name): self
    {
        $this->columns[] = "{$name} TINYINT(1)";
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns[] = "created_at TIMESTAMP NULL";
        $this->columns[] = "updated_at TIMESTAMP NULL";
        return $this;
    }

    public function softDeletes(): self
    {
        $this->columns[] = "deleted_at TIMESTAMP NULL";
        return $this;
    }

    /**
     * Compile the blueprint into a CREATE TABLE SQL statement.
     */
    public function toSql(): string
    {
        $columnsSql = implode(', ', $this->columns);
        return "CREATE TABLE {$this->table} ({$columnsSql});";
    }
}
