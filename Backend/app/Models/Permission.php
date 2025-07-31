<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'module',
        'action',
        'resource',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    /**
     * Scope to filter by module.
     */
    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by action.
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by resource.
     */
    public function scopeForResource($query, $resource)
    {
        return $query->where('resource', $resource);
    }

    /**
     * Get permission identifier.
     */
    public function getIdentifierAttribute()
    {
        return "{$this->module}.{$this->action}" . ($this->resource ? ".{$this->resource}" : '');
    }
} 