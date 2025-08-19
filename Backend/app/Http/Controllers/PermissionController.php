<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Get all permissions
     */
    public function index()
    {
        $permissions = Permission::with('roles')->get();

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($permissions)
        ]);
    }

    /**
     * Get permissions by module
     */
    public function getByModule($module)
    {
        $permissions = Permission::forModule($module)->with('roles')->get();

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($permissions)
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully',
            'data' => new PermissionResource($permission)
        ], 201);
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

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully',
            'data' => new PermissionResource($permission)
        ]);
    }

    /**
     * Delete a permission
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully',
            'data' => []
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Permissions assigned to role successfully',
            'data' => [
                'role' => $role,
                'permissions' => PermissionResource::collection($role->permissions)
            ]
        ]);
    }

    /**
     * Get role permissions
     */
    public function getRolePermissions($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $role,
                'permissions' => PermissionResource::collection($role->permissions)
            ]
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

            return response()->json([
                'success' => true,
                'message' => 'Bulk permissions created successfully',
                'data' => []
            ], 201);
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

        return response()->json([
            'success' => true,
            'data' => [
                'resource_modules' => $resourceModules,
                'available_actions' => ['create', 'read', 'update', 'delete', 'list'],
                'available_resources' => array_keys($resourceModules),
            ]
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

        return response()->json([
            'success' => true,
            'data' => $groupedPermissions
        ]);
    }
}
