<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'role_id';

    public const ACCOUNT_MANAGER = 1;
    public const SNACK_MANAGER = 2;
    public const OPERATION = 3;
    public const EMPLOYEE = 4;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the permissions for this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    /**
     * Get the users with this role.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission($module, $action, $resource = null)
    {
        return $this->permissions()
                    ->where('module', $module)
                    ->where('action', $action)
                    ->where('resource', $resource)
                    ->wherePivot('is_active', true)
                    ->exists();
    }

    /**
     * Check if role has any permission for a module.
     */
    public function hasModulePermission($module)
    {
        return $this->permissions()
                    ->where('module', $module)
                    ->wherePivot('is_active', true)
                    ->exists();
    }

    /**
     * Get all permissions for a specific module.
     */
    public function getModulePermissions($module)
    {
        return $this->permissions()
                    ->where('module', $module)
                    ->wherePivot('is_active', true)
                    ->get();
    }

    /**
     * Assign permissions to role.
     */
    public function assignPermissions($permissionIds)
    {
        $permissions = [];
        foreach ($permissionIds as $permissionId) {
            $permissions[$permissionId] = ['is_active' => true];
        }
        
        return $this->permissions()->sync($permissions);
    }

    /**
     * Revoke permissions from role.
     */
    public function revokePermissions($permissionIds)
    {
        return $this->permissions()->detach($permissionIds);
    }
}
