<?php

namespace App\Models\Traits;

trait HasHiddenTimestamps
{
    /**
     * Default hidden timestamps for models
     * Models using this trait will hide created_at, updated_at, deleted_at
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
