<?php

namespace LunoxHoshizaki\Database\Traits;

trait HasTimestamps
{
    /**
     * Determine if the model uses timestamps.
     */
    public bool $timestamps = true;

    /**
     * Boot the HasTimestamps trait for a model.
     * This method intercepts save/create to automatically append timestamps.
     */
    protected function updateTimestamps(): void
    {
        if (!$this->timestamps) {
            return;
        }

        $time = date('Y-m-d H:i:s');

        // Check if primary key exists to determine if it's an insert or update
        if (empty($this->attributes[$this->primaryKey ?? 'id'])) {
            $this->attributes['created_at'] = $time;
        }

        $this->attributes['updated_at'] = $time;
    }
}
