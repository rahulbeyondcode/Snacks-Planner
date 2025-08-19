<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all modules with their standard actions
        $modules = [
            // Core modules
            'groups' => ['create', 'read', 'update', 'delete', 'list'],
            'users' => ['create', 'read', 'update', 'delete', 'list'],
            'shops' => ['create', 'read', 'update', 'delete', 'list'],
            'permissions' => ['create', 'read', 'update', 'delete', 'list'],

            // Account Manager specific modules
            'payment_methods' => ['create', 'read', 'update', 'delete', 'list'],
            'working_days' => ['read', 'update'],
            'office_holidays' => ['create', 'read', 'update', 'delete', 'list'],
            'money_pool_settings' => ['create', 'read', 'update', 'delete', 'list'],
            'reports' => ['create', 'read', 'update', 'delete', 'list'],
            'profit_loss' => ['read', 'list'],

            // Snack Management modules
            'snack_items' => ['create', 'read', 'update', 'delete', 'list'],
            'snacks' => ['create', 'read', 'update', 'delete', 'list'],
            'categories' => ['create', 'read', 'update', 'delete', 'list'],
            'contributions' => ['create', 'read', 'update', 'delete', 'list'],

            // Operations modules
            'weekly_operations' => ['create', 'read', 'update', 'delete', 'list'],
            'money_pools' => ['create', 'read', 'update', 'delete', 'list'],
            'money_pool_blocks' => ['create', 'read', 'update', 'delete', 'list'],
            'sub_groups' => ['create', 'read', 'update', 'delete', 'list'],
            'no_snacks_days' => ['create', 'read', 'update', 'delete', 'list'],

            // Planning modules
            'snack_preferences' => ['create', 'read', 'update', 'delete', 'list'],
            'snack_plans' => ['create', 'read', 'update', 'delete', 'list'],
            'snack_plan_details' => ['create', 'read', 'update', 'delete', 'list'],

            // Employee specific modules
            'my_contributions' => ['read', 'list'],
            'snack_suggestions' => ['create', 'read', 'update', 'delete', 'list'],
            'snack_ratings' => ['create', 'read', 'update', 'delete', 'list'],
        ];

        // Define which modules each resource can access based on routes analysis
        $resourceModules = [
            'account_manager' => [
                'permissions',
                'groups',
                'users',
                'shops',
                'payment_methods',
                'working_days',
                'office_holidays',
                'money_pool_settings',
                'reports',
                'snack_items',
                'snacks',
                'profit_loss',
                'snack_preferences',
                'snack_plans',
                'snack_plan_details'
            ],
            'snack_manager' => [
                'categories',
                'contributions',
                'snack_items',
                'snacks',
                'shops',
                'weekly_operations',
                'money_pools',
                'money_pool_blocks',
                'sub_groups',
                'no_snacks_days',
                'snack_preferences',
                'snack_plans',
                'snack_plan_details'
            ],
            'operation' => [
                'categories',
                'contributions',
                'snack_items',
                'snacks',
                'shops',
                'weekly_operations',
                'snack_preferences',
                'snack_plans',
                'snack_plan_details'
            ],
            'employee' => [
                'my_contributions',
                'snack_suggestions',
                'snack_ratings'
            ],
        ];

        // Create permissions for each resource-module combination
        foreach ($resourceModules as $resource => $allowedModules) {
            foreach ($allowedModules as $module) {
                if (isset($modules[$module])) {
                    foreach ($modules[$module] as $action) {
                        Permission::firstOrCreate(
                            [
                                'module' => $module,
                                'action' => $action,
                                'resource' => $resource,
                            ],
                            [
                                'description' => "Can {$action} {$module} ({$resource})",
                            ]
                        );
                    }
                }
            }
        }

        // Assign permissions to roles
        $this->assignRolePermissions();
    }

    /**
     * Assign permissions to roles based on their resource access
     */
    private function assignRolePermissions()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        foreach ($roles as $role) {
            $permissionIds = [];

            switch ($role->name) {
                case 'account_manager':
                    // Account manager gets all permissions for account_manager resource
                    $permissionIds = $permissions
                        ->where('resource', 'account_manager')
                        ->pluck('permission_id')
                        ->toArray();
                    break;

                case 'snack_manager':
                    // Snack manager gets all permissions for snack_manager resource
                    $permissionIds = $permissions
                        ->where('resource', 'snack_manager')
                        ->pluck('permission_id')
                        ->toArray();
                    break;

                case 'operation':
                    // Operation gets all permissions for operation resource
                    $permissionIds = $permissions
                        ->where('resource', 'operation')
                        ->pluck('permission_id')
                        ->toArray();
                    break;

                case 'employee':
                    // Employee gets all permissions for employee resource
                    $permissionIds = $permissions
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
