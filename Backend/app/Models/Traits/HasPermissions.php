<?php

namespace App\Models\Traits;

trait HasPermissions
{
    /**
     * Check if the model has a specific permission.
     */
    public function hasPermission($module, $action, $resource = null)
    {
        if ($this instanceof \App\Models\Role) {
            return $this->permissions()
                ->where('module', $module)
                ->where('action', $action)
                ->where('resource', $resource)
                ->wherePivot('is_active', true)
                ->exists();
        } elseif ($this instanceof \App\Models\User) {
            return $this->role && $this->role->hasPermission($module, $action, $resource);
        }
        return false;
    }

    /**
     * Check if the model has any permission for a module.
     */
    public function hasModulePermission($module)
    {
        if ($this instanceof \App\Models\Role) {
            return $this->permissions()
                ->where('module', $module)
                ->wherePivot('is_active', true)
                ->exists();
        } elseif ($this instanceof \App\Models\User) {
            return $this->role && $this->role->hasModulePermission($module);
        }
        return false;
    }
}
