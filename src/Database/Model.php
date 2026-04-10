<?php

namespace LunoxHoshizaki\Database;

use PDO;
use PDOException;
use Exception;

class Model
{
    /**
     * Database connection instance.
     */
    protected static ?PDO $pdo = null;

    /**
     * The table associated with the model.
     */
    protected string $table;

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be hidden for serialization (API responses).
     */
    protected array $hidden = [];

    /**
     * The attributes that should be cast.
     */
    protected array $casts = [];

    /**
     * The primary key for the model.
     */
    protected string $primaryKey = 'id';

    /**
     * The model's attributes.
     */
    public array $attributes = [];

    /**
     * Internal query builder parts.
     */
    protected array $wheres = [];
    protected array $bindings = [];
    protected array $joins = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected array $columns = ['*'];
    protected ?string $groupBy = null;
    protected ?string $havingRaw = null;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);

        if (!isset($this->table)) {
            $classParts = explode('\\', get_class($this));
            $this->table = strtolower(end($classParts)) . 's'; // simple pluralization
        }

        // Handle SoftDeletes trait initialization
        if (in_array(\LunoxHoshizaki\Database\Traits\SoftDeletes::class, class_uses_recursive(static::class))) {
            $this->where($this->table . '.deleted_at', 'IS', null);
        }
    }

    /**
     * Fill the model with an array of attributes.
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    protected function isFillable(string $key): bool
    {
        return in_array($key, $this->fillable);
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get the PDO connection.
     */
    public static function getConnection(): PDO
    {
        if (is_null(static::$pdo)) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $db   = $_ENV['DB_DATABASE'] ?? 'test';
            $user = $_ENV['DB_USERNAME'] ?? 'root';
            $pass = $_ENV['DB_PASSWORD'] ?? '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                static::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }

        return static::$pdo;
    }

    /**
     * Begin querying the model.
     */
    public static function query(): static
    {
        return new static;
    }

    /**
     * Set columns to select.
     */
    public function select($columns = ['*']): self
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Add a basic where clause to the query.
     */
    public function where(string $column, $operator = null, $value = null): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        // Handle IS NULL / IS NOT NULL
        $upperOp = strtoupper(trim($operator));
        if ($upperOp === 'IS' && is_null($value)) {
            $this->wheres[] = ['type' => 'raw', 'sql' => "{$column} IS NULL", 'boolean' => 'AND'];
            return $this;
        }
        if ($upperOp === 'IS NOT' && is_null($value)) {
            $this->wheres[] = ['type' => 'raw', 'sql' => "{$column} IS NOT NULL", 'boolean' => 'AND'];
            return $this;
        }

        $this->wheres[] = ['type' => 'basic', 'sql' => "{$column} {$operator} ?", 'boolean' => 'AND'];
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     */
    public function orWhere(string $column, $operator = null, $value = null): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = ['type' => 'basic', 'sql' => "{$column} {$operator} ?", 'boolean' => 'OR'];
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add a "where in" clause to the query.
     */
    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            // If no values, make the condition always false
            $this->wheres[] = ['type' => 'raw', 'sql' => '0 = 1', 'boolean' => 'AND'];
            return $this;
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $this->wheres[] = ['type' => 'raw', 'sql' => "{$column} IN ({$placeholders})", 'boolean' => 'AND'];
        $this->bindings = array_merge($this->bindings, array_values($values));

        return $this;
    }

    /**
     * Add a "where not in" clause to the query.
     */
    public function whereNotIn(string $column, array $values): self
    {
        if (empty($values)) {
            return $this; // No exclusion needed
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $this->wheres[] = ['type' => 'raw', 'sql' => "{$column} NOT IN ({$placeholders})", 'boolean' => 'AND'];
        $this->bindings = array_merge($this->bindings, array_values($values));

        return $this;
    }

    /**
     * Add a "where null" clause to the query.
     */
    public function whereNull(string $column): self
    {
        $this->wheres[] = ['type' => 'raw', 'sql' => "{$column} IS NULL", 'boolean' => 'AND'];
        return $this;
    }

    /**
     * Add a "where not null" clause to the query.
     */
    public function whereNotNull(string $column): self
    {
        $this->wheres[] = ['type' => 'raw', 'sql' => "{$column} IS NOT NULL", 'boolean' => 'AND'];
        return $this;
    }

    /**
     * Add a "where between" clause to the query.
     */
    public function whereBetween(string $column, array $values): self
    {
        $this->wheres[] = ['type' => 'raw', 'sql' => "{$column} BETWEEN ? AND ?", 'boolean' => 'AND'];
        $this->bindings[] = $values[0];
        $this->bindings[] = $values[1];

        return $this;
    }

    /**
     * Add a "where like" clause to the query.
     */
    public function whereLike(string $column, string $value): self
    {
        $this->wheres[] = ['type' => 'basic', 'sql' => "{$column} LIKE ?", 'boolean' => 'AND'];
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Add a raw where clause.
     */
    public function whereRaw(string $sql, array $bindings = []): self
    {
        $this->wheres[] = ['type' => 'raw', 'sql' => $sql, 'boolean' => 'AND'];
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }

    /**
     * Add a join clause to the query.
     */
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }

    /**
     * Add a left join clause to the query.
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Add an "order by" clause to the query.
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
        $this->orders[] = "$column $direction";
        return $this;
    }

    /**
     * Add a "group by" clause.
     */
    public function groupBy(string $column): self
    {
        $this->groupBy = $column;
        return $this;
    }

    /**
     * Add a raw "having" clause.
     */
    public function havingRaw(string $sql): self
    {
        $this->havingRaw = $sql;
        return $this;
    }

    /**
     * Set the "limit" value of the query.
     */
    public function limit(int $value): self
    {
        $this->limit = $value;
        return $this;
    }

    /**
     * Set the "offset" value of the query.
     */
    public function offset(int $value): self
    {
        $this->offset = $value;
        return $this;
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    /**
     * Build the WHERE clause from the wheres array.
     */
    protected function buildWhereClause(): string
    {
        if (empty($this->wheres)) {
            return '';
        }

        $clauses = [];
        foreach ($this->wheres as $i => $where) {
            $prefix = $i === 0 ? '' : " {$where['boolean']} ";
            $clauses[] = $prefix . $where['sql'];
        }

        return ' WHERE ' . implode('', $clauses);
    }

    /**
     * Execute the query as a "select" statement.
     */
    public function get(): array
    {
        $columns = implode(', ', $this->columns);
        $sql = "SELECT {$columns} FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= " " . implode(" ", $this->joins);
        }

        $sql .= $this->buildWhereClause();

        if ($this->groupBy !== null) {
            $sql .= " GROUP BY " . $this->groupBy;
        }

        if ($this->havingRaw !== null) {
            $sql .= " HAVING " . $this->havingRaw;
        }

        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(", ", $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT " . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET " . $this->offset;
        }

        $stmt = static::getConnection()->prepare($sql);
        $stmt->execute($this->bindings);
        
        $results = $stmt->fetchAll();

        return array_map(function ($result) {
            $model = new static;
            $model->attributes = $result;
            return $model;
        }, $results);
    }

    /**
     * Execute the query and get the first result.
     */
    public function first(): ?static
    {
        $this->limit(1);
        $results = $this->get();
        return count($results) > 0 ? reset($results) : null;
    }

    public static function find($id): ?static
    {
        $instance = new static;
        return $instance->where($instance->primaryKey, '=', $id)->first();
    }

    /**
     * Get the count of records.
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as aggregate FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= " " . implode(" ", $this->joins);
        }

        $sql .= $this->buildWhereClause();

        $stmt = static::getConnection()->prepare($sql);
        $stmt->execute($this->bindings);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Check if any records exist.
     */
    public function exists(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Paginate the given query.
     */
    public function paginate(int $perPage = 15): Pagination
    {
        $page = (int) ($_GET['page'] ?? 1);
        if ($page < 1) {
            $page = 1;
        }

        // Count total before applying limit/offset
        $countSql = "SELECT COUNT(*) as aggregate FROM {$this->table}";
        if (!empty($this->joins)) {
            $countSql .= " " . implode(" ", $this->joins);
        }
        $countSql .= $this->buildWhereClause();

        $stmt = static::getConnection()->prepare($countSql);
        $stmt->execute($this->bindings);
        $total = (int) $stmt->fetchColumn();

        // Apply limit and offset
        $this->limit($perPage);
        $this->offset(($page - 1) * $perPage);

        // Fetch items
        $items = $this->get();

        return new Pagination($items, $total, $perPage, $page);
    }

    /**
     * Define a one-to-one relationship.
     */
    public function hasOne(string $related, string $foreignKey = null, string $localKey = null): static
    {
        $instance = new $related;
        $foreignKey = $foreignKey ?: strtolower((new \ReflectionClass($this))->getShortName()) . '_id';
        $localKey = $localKey ?: $this->primaryKey;

        return $instance->where($foreignKey, '=', $this->{$localKey});
    }

    /**
     * Define a one-to-many relationship.
     */
    public function hasMany(string $related, string $foreignKey = null, string $localKey = null): static
    {
        $instance = new $related;
        $foreignKey = $foreignKey ?: strtolower((new \ReflectionClass($this))->getShortName()) . '_id';
        $localKey = $localKey ?: $this->primaryKey;

        return $instance->where($foreignKey, '=', $this->{$localKey});
    }

    /**
     * Define an inverse one-to-one or many relationship.
     */
    public function belongsTo(string $related, string $foreignKey = null, string $ownerKey = null): static
    {
        $instance = new $related;
        $foreignKey = $foreignKey ?: strtolower((new \ReflectionClass($instance))->getShortName()) . '_id';
        $ownerKey = $ownerKey ?: $instance->primaryKey;

        return $instance->where($ownerKey, '=', $this->{$foreignKey});
    }

    /**
     * Define a many-to-many relationship.
     */
    public function belongsToMany(string $related, string $table = null, string $foreignPivotKey = null, string $relatedPivotKey = null): static
    {
        $instance = new $related;
        
        $sourceClass = strtolower((new \ReflectionClass($this))->getShortName());
        $relatedClass = strtolower((new \ReflectionClass($instance))->getShortName());

        $table = $table ?: static::getJoinedTableName($sourceClass, $relatedClass);
        $foreignPivotKey = $foreignPivotKey ?: $sourceClass . '_id';
        $relatedPivotKey = $relatedPivotKey ?: $relatedClass . '_id';

        return $instance->select([$instance->table . '.*'])
            ->join($table, "{$table}.{$relatedPivotKey}", '=', "{$instance->table}.{$instance->primaryKey}")
            ->where("{$table}.{$foreignPivotKey}", '=', $this->{$this->primaryKey});
    }

    /**
     * Helper to get pivot table name
     */
    private static function getJoinedTableName(string $first, string $second): string
    {
        $models = [$first, $second];
        sort($models);
        return implode('_', $models);
    }

    /**
     * Save the model to the database.
     */
    public function save(): bool
    {
        // Hook for Timestamps trait
        if (method_exists($this, 'updateTimestamps')) {
            $this->updateTimestamps();
        }

        $columns = array_keys($this->attributes);
        $columns = array_filter($columns, fn($col) => $col !== $this->primaryKey);

        if (empty($this->attributes[$this->primaryKey])) {
            // INSERT
            $placeholders = array_fill(0, count($columns), '?');
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $values = [];
            foreach ($columns as $col) {
                $values[] = $this->attributes[$col];
            }

            $stmt = static::getConnection()->prepare($sql);
            $result = $stmt->execute($values);
            
            if ($result) {
                $this->attributes[$this->primaryKey] = static::getConnection()->lastInsertId();
            }
            return $result;
        } else {
            // UPDATE
            $sets = array_map(fn($col) => "$col = ?", $columns);
            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = ?";
            
            $bindings = [];
            foreach ($columns as $col) {
                $bindings[] = $this->attributes[$col];
            }
            $bindings[] = $this->attributes[$this->primaryKey];

            return static::getConnection()->prepare($sql)->execute($bindings);
        }
    }

    /**
     * Create a new model instance and save it.
     */
    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    /**
     * Update multiple attributes at once.
     */
    public function update(array $attributes): bool
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this->save();
    }

    /**
     * Delete the model.
     */
    public function delete(): bool
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return false;
        }

        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return static::getConnection()->prepare($sql)->execute([$this->attributes[$this->primaryKey]]);
    }

    /**
     * Convert the model to an array, respecting $hidden.
     */
    public function toArray(): array
    {
        $attributes = $this->attributes;
        
        foreach ($this->hidden as $key) {
            unset($attributes[$key]);
        }

        // Apply casts
        foreach ($this->casts as $key => $type) {
            if (isset($attributes[$key])) {
                $attributes[$key] = $this->castAttribute($type, $attributes[$key]);
            }
        }

        return $attributes;
    }

    /**
     * Convert the model to JSON, respecting $hidden.
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Cast an attribute to a given type.
     */
    protected function castAttribute(string $type, mixed $value): mixed
    {
        return match ($type) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'string' => (string) $value,
            'bool', 'boolean' => (bool) $value,
            'array', 'json' => is_string($value) ? json_decode($value, true) : $value,
            'datetime' => $value,
            default => $value,
        };
    }

    /**
     * Get the table name.
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
