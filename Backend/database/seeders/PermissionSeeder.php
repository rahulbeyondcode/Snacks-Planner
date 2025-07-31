<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define modules and their actions
        $modules = [
            'groups' => ['create', 'read', 'update', 'delete', 'list'],
            'weekly_group' => ['create', 'read', 'update', 'delete', 'list'],
            'employees' => ['create', 'read', 'update', 'delete', 'list'],
            'money_pool' => ['create', 'read', 'update', 'delete', 'list'],
            'reports' => ['create', 'read', 'update', 'delete', 'list'],
            'contributions' => ['create', 'read', 'update', 'delete', 'list'],
            'shops' => ['create', 'read', 'update', 'delete', 'list'],
            'snacks' => ['create', 'read', 'update', 'delete', 'list'],
            'permissions' => ['create', 'read', 'update', 'delete', 'list'],
            'snack_orders' => ['create', 'read', 'update', 'delete', 'list']
        ];

        $resources = ['account_manager', 'snack_manager', 'operation', 'employee'];

        // Create permissions
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                foreach ($resources as $resource) {
                    Permission::firstOrCreate([
                        'module' => $module,
                        'action' => $action,
                        'resource' => $resource,
                    ], [
                        'description' => "Can {$action} {$module} ({$resource})",
                    ]);
                }
            }
        }

        // Assign permissions to roles
        $this->assignRolePermissions();
    }

    /**
     * Assign permissions to roles based on role hierarchy
     */
    private function assignRolePermissions()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        foreach ($roles as $role) {
            $permissionIds = [];

            switch ($role->name) {
                case 'account_manager':
                    // Full access to all modules with account_manager resource
                    $permissionIds = $permissions
                        ->where('resource', 'account_manager')
                        ->pluck('permission_id')
                        ->toArray();
                    break;

                case 'operation_manager':
                    // Access to most modules with snack_manager resource
                    $permissionIds = $permissions
                        ->where('module', '!=', 'permissions')
                        ->where('resource', 'snack_manager')
                        ->pluck('permission_id')
                        ->toArray();
                    break;

                case 'operation':
                    // Limited access to groups, users, snacks, contributions with operation resource
                    $permissionIds = $permissions
                        ->whereIn('module', ['groups', 'users', 'snacks', 'contributions'])
                        ->where('resource', 'operation')
                        ->pluck('permission_id')
                        ->toArray();
                    break;

                case 'employee':
                    // Read-only access to snacks, contributions with employee resource
                    $permissionIds = $permissions
                        ->whereIn('module', ['snacks', 'contributions'])
                        ->whereIn('action', ['read', 'list'])
                        ->where('resource', 'employee')
                        ->pluck('permission_id')
                        ->toArray();
                    break;
            }

            if (!empty($permissionIds)) {
                $role->assignPermissions($permissionIds);
            }
        }
    }
} 