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
}
