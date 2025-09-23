<?php

namespace App\Http\Controllers;

use App\Services\GroupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\GroupResource;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupController extends BaseController
{

    protected $groupService;

    public function __construct(GroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
    }


    // List groups (admin only)
    public function index(Request $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            $filters = $request->only(['search']);
            $groups = $this->groupService->listGroups($filters);
            return $this->resourceCollectionResponse(GroupResource::collection($groups));
        }, 'account_manager', 'Failed to retrieve groups');
    }

    // Show group details (admin only)
    public function show($id)
    {
        return $this->executeWithAuth(function ($user) use ($id) {
            $group = $this->groupService->getGroup($id);
            if (!$group) {
                return $this->notFoundResponse(__('Group not found'));
            }

            return $this->resourceResponse(new GroupResource($group));
        }, 'account_manager', 'Failed to retrieve group');
    }

    // Create group (admin only)

    public function store(StoreGroupRequest $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            $validated = $request->validated();

            // Check if account manager's user_id is included in employees or snack_managers
            $currentUserId = $user->user_id;
            if (in_array($currentUserId, $validated['employees'])) {
                return $this->errorResponse(__('Account manager cannot be added as an employee.'), [], 422);
            }

            if (in_array($currentUserId, $validated['snack_managers'])) {
                return $this->errorResponse(__('Account manager cannot be added as a snack manager.'), [], 422);
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

                return $this->errorResponse($finalMessage, [], 422);
            }

            $newGroup = $this->groupService->createGroup($validated);

            return $this->createdResponse(new GroupResource($newGroup), 'Group created successfully');
        }, 'account_manager', 'An error occurred while creating the group');
    }

    // Update group (admin only)
    public function update(UpdateGroupRequest $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            $validated = $request->validated();

            // Check if account manager's user_id is included in employees or snack_managers
            $currentUserId = $user->user_id;
            if (in_array($currentUserId, $validated['employees'])) {
                return $this->errorResponse(__('Account manager cannot be added as an employee.'), [], 422);
            }

            if (in_array($currentUserId, $validated['snack_managers'])) {
                return $this->errorResponse(__('Account manager cannot be added as a snack manager.'), [], 422);
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

                return $this->errorResponse($finalMessage, [], 422);
            }

            $updatedGroup = $this->groupService->updateGroup($id, $validated);
            if (!$updatedGroup) {
                return $this->notFoundResponse(__('Group not found'));
            }

            return $this->updatedResponse(new GroupResource($updatedGroup), 'Group updated successfully');
        }, 'account_manager', 'An error occurred while updating the group');
    }

    // Delete group (admin only)
    public function destroy($id)
    {
        return $this->executeWithAuth(function ($user) use ($id) {
            $deleted = $this->groupService->deleteGroup($id);
            if (!$deleted) {
                return $this->notFoundResponse(__('Group not found'));
            }

            return $this->noContentResponse();
        }, 'account_manager', 'Failed to delete group');
    }

    // List members of a group (admin only)
    public function members($id)
    {
        return $this->executeWithAuth(function ($user) use ($id) {
            $members = $this->groupService->listMembers($id);
            if ($members === null) {
                return $this->notFoundResponse(__('Group not found'));
            }
            return $this->resourceCollectionResponse(GroupResource::collection($members));
        }, 'account_manager', 'Failed to retrieve group members');
    }

    // Add members to group (admin only)
    public function addMembers(Request $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            $validated = $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,user_id',
            ]);
            $members = $this->groupService->addMembers($id, $validated['user_ids']);
            if ($members === null) {
                return $this->notFoundResponse(__('Group not found'));
            }
            return $this->resourceCollectionResponse(GroupResource::collection($members));
        }, 'account_manager', 'Failed to add members to group');
    }

    // Remove members from group (admin only)
    public function removeMembers(Request $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            $validated = $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,user_id',
            ]);
            $members = $this->groupService->removeMembers($id, $validated['user_ids']);
            if ($members === null) {
                return $this->notFoundResponse(__('Group not found'));
            }
            return $this->resourceCollectionResponse(GroupResource::collection($members));
        }, 'account_manager', 'Failed to remove members from group');
    }

    public function setSortOrder(Request $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            $sortOrders = $request->input('sort_orders');

            // Improved validation
            if (!is_array($sortOrders) || empty($sortOrders)) {
                return $this->errorResponse(__('Invalid input format. Expected a non-empty array.'), [], 422);
            }

            // Validate sort order values are numeric
            foreach ($sortOrders as $groupId => $sortOrder) {
                if (!is_numeric($sortOrder) || $sortOrder < 0) {
                    return $this->errorResponse(__('Invalid sort order value. Expected non-negative numbers.'), [], 422);
                }
            }

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

            return $this->successResponse('Group sort orders updated successfully', $sortOrders);
        }, 'account_manager', 'An error occurred while updating sort orders');
    }
}
