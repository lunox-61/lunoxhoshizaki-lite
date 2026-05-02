<?php

namespace LunoxHoshizaki\Database\Traits;

trait SoftDeletes
{
    /**
     * Indicator if the model should perform soft deletes.
     */
    protected bool $forceDeleting = false;

    /**
     * Perform the actual delete query.
     */
    public function delete(): bool
    {
        if ($this->forceDeleting) {
            return parent::delete(); // Hard delete
        }

        $time = date('Y-m-d H:i:s');
        $this->attributes['deleted_at'] = $time;
        
        $sql = "UPDATE {$this->table} SET deleted_at = ? WHERE {$this->primaryKey} = ?";
        return static::getConnection()->prepare($sql)->execute([
            $time,
            $this->attributes[$this->primaryKey]
        ]);
    }

    /**
     * Force a hard delete on a soft deleted model.
     */
    public function forceDelete(): bool
    {
        $this->forceDeleting = true;
        return $this->delete();
    }

    /**
     * Restore a soft-deleted model instance.
     */
    public function restore(): bool
    {
        $this->attributes['deleted_at'] = null;
        
        $sql = "UPDATE {$this->table} SET deleted_at = NULL WHERE {$this->primaryKey} = ?";
        return static::getConnection()->prepare($sql)->execute([
            $this->attributes[$this->primaryKey]
        ]);
    }

    /**
     * Query including soft-deleted records.
     *
     * Removes the automatic `deleted_at IS NULL` constraint that is
     * applied by default in the Model constructor.
     *
     * Usage:
     *   User::query()->withTrashed()->get();
     *   User::query()->withTrashed()->where('id', 5)->first();
     */
    public function withTrashed(): self
    {
        // Remove the soft-delete constraint from wheres
        $this->wheres = array_filter($this->wheres, function ($where) {
            return !($where['type'] === 'raw' && str_contains($where['sql'], 'deleted_at IS NULL'));
        });
        // Re-index to maintain proper WHERE clause building
        $this->wheres = array_values($this->wheres);

        return $this;
    }

    /**
     * Query ONLY soft-deleted records.
     *
     * Usage:
     *   User::query()->onlyTrashed()->get();
     */
    public function onlyTrashed(): self
    {
        // Remove the default IS NULL constraint
        $this->withTrashed();

        // Add IS NOT NULL constraint
        $this->wheres[] = [
            'type' => 'raw',
            'sql' => $this->table . '.deleted_at IS NOT NULL',
            'boolean' => 'AND',
        ];

        return $this;
    }

    /**
     * Check if the model instance has been soft-deleted.
     */
    public function trashed(): bool
    {
        return !is_null($this->attributes['deleted_at'] ?? null);
    }
}
