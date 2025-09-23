<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends BaseController
{
    /**
     * Get all permissions
     */
    public function index()
    {
        $permissions = Permission::with('roles')->get();

        return $this->resourceCollectionResponse(PermissionResource::collection($permissions));
    }

    /**
     * Get permissions by module
     */
    public function getByModule($module)
    {
        $permissions = Permission::forModule($module)->with('roles')->get();

        return $this->resourceCollectionResponse(PermissionResource::collection($permissions));
    }

    /**
     * Create a new permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'resource' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create($validated);

        return $this->createdResponse(new PermissionResource($permission), 'Permission created successfully');
    }

    /**
     * Update a permission
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'module' => 'sometimes|required|string|max:255',
            'action' => 'sometimes|required|string|max:255',
            'resource' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $permission->update($validated);

        return $this->updatedResponse(new PermissionResource($permission), 'Permission updated successfully');
    }

    /**
     * Delete a permission
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return $this->deletedResponse('Permission deleted successfully');
    }

    /**
     * Assign permissions to a role
     */
    public function assignToRole(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,permission_id',
        ]);

        $role->assignPermissions($validated['permission_ids']);

        return $this->successResponse('Permissions assigned to role successfully', [
            'role' => $role,
            'permissions' => PermissionResource::collection($role->permissions)
        ]);
    }

    /**
     * Get role permissions
     */
    public function getRolePermissions($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        return $this->successResponse('success', [
            'role' => $role,
            'permissions' => PermissionResource::collection($role->permissions)
        ]);
    }

    /**
     * Bulk create permissions for all modules
     */
    public function bulkCreate()
    {
        $modules = [
            'groups' => ['create', 'read', 'update', 'delete', 'list'],
            'users' => ['create', 'read', 'update', 'delete', 'list'],
            'snacks' => ['create', 'read', 'update', 'delete', 'list'],
            'reports' => ['create', 'read', 'update', 'delete', 'list'],
            'contributions' => ['create', 'read', 'update', 'delete', 'list'],
            'shops' => ['create', 'read', 'update', 'delete', 'list'],
            'funds' => ['create', 'read', 'update', 'delete', 'list'],
        ];

        $resources = ['account_manager', 'snack_manager', 'operation', 'employee'];

        DB::beginTransaction();
        try {
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
            DB::commit();

            return $this->createdResponse([], 'Bulk permissions created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all resources with their modules and actions
     */
    public function getResourceModules()
    {
        $resourceModules = [
            'account_manager' => [
                'groups' => ['create', 'read', 'update', 'delete', 'list'],
                'shops' => ['create', 'read', 'update', 'delete', 'list'],
                'snacks' => ['create', 'read', 'update', 'delete', 'list'],
                'reports' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            'snack_manager' => [
                'snack_orders' => ['create', 'read', 'update', 'delete', 'list'],
                'weekly_group' => ['create', 'read', 'update', 'delete', 'list'],
                'money_pool' => ['create', 'read', 'update', 'delete', 'list'],
                'contributions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            'operation' => [
                'snack_orders' => ['create', 'read', 'update', 'delete', 'list'],
                'contributions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            'employee' => [
                // Add modules if needed
            ],
        ];

        return $this->successResponse('success', [
            'resource_modules' => $resourceModules,
            'available_actions' => ['create', 'read', 'update', 'delete', 'list'],
            'available_resources' => array_keys($resourceModules),
        ]);
    }

    /**
     * Get permissions grouped by resource and module
     */
    public function getPermissionsByResource()
    {
        $permissions = Permission::with('roles')->get();

        $groupedPermissions = $permissions->groupBy('resource')->map(function ($resourcePermissions) {
            return $resourcePermissions->groupBy('module')->map(function ($modulePermissions) {
                return $modulePermissions->pluck('action')->unique()->values()->toArray();
            });
        });

        return $this->successResponse('success', $groupedPermissions);
    }
}
