<?php

namespace App\Http\Controllers;

use App\Services\GroupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\GroupResource;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{

    protected $groupService;

    public function __construct(GroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
    }

    // List groups (admin only)
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->internalServerError(__('Forbidden'));
        }
        $filters = $request->only(['search']);
        $groups = $this->groupService->listGroups($filters);
        return GroupResource::collection($groups);
    }

    // Show group details (admin only)
    public function show($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->internalServerError(__('Forbidden'));
        }
        $group = $this->groupService->getGroup($id);
        if (!$group) {
            return response()->internalServerError(__('Group not found'));
        }

        return new GroupResource($group);
    }

    // Create group (admin only)

    public function store(StoreGroupRequest $request)
    {
        $user = Auth::user();

        if (!$user || $user->role->name !== 'account_manager') {
            return response()->internalServerError(__('Forbidden'));
        }

        try {
            $validated = $request->validated();

            // Check if account manager's user_id is included in employees or snack_managers
            $currentUserId = $user->user_id;
            if (in_array($currentUserId, $validated['employees'])) {
                return response()->internalServerError(__('Account manager cannot be added as an employee.'));
            }

            if (in_array($currentUserId, $validated['snack_managers'])) {
                return response()->internalServerError(__('Account manager cannot be added as a snack manager.'));
            }

            // Collect all user IDs from employees and snack_managers
            $allUserIds = array_merge($validated['employees'], $validated['snack_managers']);
            $allUserIds = array_unique($allUserIds); // Remove duplicates

            // Check if any of these users already exist in another group
            $existingUsers = GroupMember::whereIn('user_id', $allUserIds)
                ->whereNull('deleted_at')
                ->with(['user:user_id,name', 'group:group_id,name'])
                ->get();

            if ($existingUsers->isNotEmpty()) {
                // Separate conflicts by type
                $employeeConflicts = [];
                $snackManagerConflicts = [];

                foreach ($existingUsers as $groupMember) {
                    $userMessage = $groupMember->user->name . ' is already in group "' . $groupMember->group->name . '"';

                    if (in_array($groupMember->user_id, $validated['employees'])) {
                        $employeeConflicts[] = $userMessage;
                    }

                    if (in_array($groupMember->user_id, $validated['snack_managers'])) {
                        $snackManagerConflicts[] = $userMessage;
                    }
                }

                // Build separate error messages
                $errorMessages = [];

                if (!empty($employeeConflicts)) {
                    $errorMessages[] = 'Employee(s) already exist in another group: ' . implode(', ', $employeeConflicts);
                }

                if (!empty($snackManagerConflicts)) {
                    $errorMessages[] = 'Snack Manager(s) already exist in another group: ' . implode(', ', $snackManagerConflicts);
                }

                $finalMessage = implode('. ', $errorMessages);

                return response()->internalServerError($finalMessage);
            }

            $newGroup = $this->groupService->createGroup($validated);

            return (new GroupResource($newGroup))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->internalServerError(__('Validation failed'));
        } catch (\Exception $e) {
            \Log::error('Error creating group: ' . $e->getMessage());
            return response()->internalServerError(__('An error occurred while creating the group'));
        }
    }

    // Update group (admin only)
    public function update(UpdateGroupRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $validated = $request->validated();

            // Check if account manager's user_id is included in employees or snack_managers
            $currentUserId = $user->user_id;
            if (in_array($currentUserId, $validated['employees'])) {
                return response()->internalServerError(__('Account manager cannot be added as an employee.'));
            }

            if (in_array($currentUserId, $validated['snack_managers'])) {
                return response()->internalServerError(__('Account manager cannot be added as a snack manager.'));
            }

            // Collect all user IDs from employees and snack_managers
            $allUserIds = array_merge($validated['employees'], $validated['snack_managers']);
            $allUserIds = array_unique($allUserIds); // Remove duplicates

            // Check if any of these users already exist in another group (excluding current group)
            $existingUsers = GroupMember::whereIn('user_id', $allUserIds)
                ->where('group_id', '!=', $id) // Exclude current group being updated
                ->whereNull('deleted_at')
                ->with(['user:user_id,name', 'group:group_id,name'])
                ->get();

            if ($existingUsers->isNotEmpty()) {
                // Separate conflicts by type
                $employeeConflicts = [];
                $snackManagerConflicts = [];

                foreach ($existingUsers as $groupMember) {
                    $userMessage = $groupMember->user->name . ' is already in group "' . $groupMember->group->name . '"';

                    if (in_array($groupMember->user_id, $validated['employees'])) {
                        $employeeConflicts[] = $userMessage;
                    }

                    if (in_array($groupMember->user_id, $validated['snack_managers'])) {
                        $snackManagerConflicts[] = $userMessage;
                    }
                }

                // Build separate error messages
                $errorMessages = [];

                if (!empty($employeeConflicts)) {
                    $errorMessages[] = 'Employee(s) already exist in another group: ' . implode(', ', $employeeConflicts);
                }

                if (!empty($snackManagerConflicts)) {
                    $errorMessages[] = 'Snack Manager(s) already exist in another group: ' . implode(', ', $snackManagerConflicts);
                }

                $finalMessage = implode('. ', $errorMessages);

                return response()->internalServerError($finalMessage);
            }

            $updatedGroup = $this->groupService->updateGroup($id, $validated);
            if (!$updatedGroup) {
                return response()->internalServerError(__('Group not found'));
            }

            return (new GroupResource($updatedGroup))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->internalServerError(__('Validation failed'));
        } catch (\Exception $e) {
            \Log::error('Error updating group: ' . $e->getMessage());
            return response()->internalServerError(__('An error occurred while updating the group'));
        }
    }

    // Delete group (admin only)
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->internalServerError(__('Forbidden'));
        }
        $deleted = $this->groupService->deleteGroup($id);
        if (!$deleted) {
            return response()->internalServerError(__('Group not found'));
        }

        return response()->noContent();
    }

    // List members of a group (admin only)
    public function members($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->internalServerError(__('Forbidden'));
        }
        $members = $this->groupService->listMembers($id);
        if ($members === null) {
            return response()->internalServerError(__('Group not found'));
        }
        return GroupResource::collection($members);
    }

    // Add members to group (admin only)
    public function addMembers(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->internalServerError(__('Forbidden'));
        }
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,user_id',
        ]);
        $members = $this->groupService->addMembers($id, $validated['user_ids']);
        if ($members === null) {
            return response()->internalServerError(__('Group not found'));
        }
        return GroupResource::collection($members);
    }

    // Remove members from group (admin only)
    public function removeMembers(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->internalServerError(__('Forbidden'));
        }
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,user_id',
        ]);
        $members = $this->groupService->removeMembers($id, $validated['user_ids']);
        if ($members === null) {
            return response()->internalServerError(__('Group not found'));
        }
        return GroupResource::collection($members);
    }

    public function setSortOrder(Request $request)
    {
        // Add authorization check
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->internalServerError(__('Forbidden'));
        }

        $sortOrders = $request->input('sort_orders');

        // Improved validation
        if (!is_array($sortOrders) || empty($sortOrders)) {
            return response()->internalServerError(__('Invalid input format. Expected a non-empty array.'));
        }

        // Validate sort order values are numeric
        foreach ($sortOrders as $groupId => $sortOrder) {
            if (!is_numeric($sortOrder) || $sortOrder < 0) {
                return response()->internalServerError(__('Invalid sort order value. Expected non-negative numbers.'));
            }
        }

        try {
            // Use database transaction for data consistency
            DB::transaction(function () use ($sortOrders) {
                $groupIds = array_keys($sortOrders);
                
                // Get only existing group_ids
                $existingGroupIds = Group::whereIn('group_id', $groupIds)
                    ->pluck('group_id')
                    ->toArray();

                // Update each existing group individually (more efficient than upsert for updates only)
                foreach ($sortOrders as $groupId => $sortOrder) {
                    if (in_array($groupId, $existingGroupIds)) {
                        Group::where('group_id', $groupId)
                            ->update(['sort_order' => (int) $sortOrder]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Group sort orders updated successfully',
                'data' => $sortOrders
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating group sort orders: ' . $e->getMessage());
            return response()->internalServerError(__('An error occurred while updating sort orders'));
        }
    }
}
