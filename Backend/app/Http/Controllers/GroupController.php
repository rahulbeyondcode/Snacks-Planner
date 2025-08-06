<?php

namespace App\Http\Controllers;

use App\Services\GroupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller
{
    // Assign group leader (operations manager)
    public function assignLeader(Request $request, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
        ]);
        $group = $this->groupService->assignLeader($id, $validated['user_id']);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        return response()->json($group);
    }

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
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $filters = $request->only(['search']);
        $groups = $this->groupService->listGroups($filters);
        return response()->json($groups);
    }

    // Show group details (admin only)
    public function show($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $group = $this->groupService->getGroup($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        return apiResponse(true, __('messages.success'), $group, 201);
    }

    // Create group (admin only)

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role->name !== 'account_manager') {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('groups', 'name')->whereNull('deleted_at'),
                ],
                'description' => 'nullable|string|max:255',
                'employees' => 'required|array',
                'snack_managers' => 'required|array',
            ]);

            // Check if account manager's user_id is included in employees or snack_managers
            $currentUserId = $user->user_id;
            if (in_array($currentUserId, $validated['employees'])) {
                return apiResponse(false, 'Account manager cannot be added as an employee.', null, 422);
            }

            if (in_array($currentUserId, $validated['snack_managers'])) {
                return apiResponse(false, 'Account manager cannot be added as a snack manager.', null, 422);
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

                return apiResponse(false, $finalMessage, null, 422);
            }

            $newGroup = $this->groupService->createGroup($validated);

            return apiResponse(true, __('messages.success'), $newGroup, 201);
        } catch (ValidationException $e) {
            return apiResponse(false, 'Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            \Log::error('Error creating group: ' . $e->getMessage());
            return apiResponse(false, 'An error occurred while creating the group', null, 500);
        }
    }

    // Update group (admin only)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'employees' => 'required|array',
                'snack_managers' => 'required|array',
            ]);

            // Check if account manager's user_id is included in employees or snack_managers
            $currentUserId = $user->user_id;
            if (in_array($currentUserId, $validated['employees'])) {
                return apiResponse(false, 'Account manager cannot be added as an employee.', null, 422);
            }

            if (in_array($currentUserId, $validated['snack_managers'])) {
                return apiResponse(false, 'Account manager cannot be added as a snack manager.', null, 422);
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

                return apiResponse(false, $finalMessage, null, 422);
            }

            $updatedGroup = $this->groupService->updateGroup($id, $validated);
            if (!$updatedGroup) {
                return apiResponse(false, __('Group not found'), null, 404);
            }

            return apiResponse(true, __('messages.success'), $updatedGroup, 201);
        } catch (ValidationException $e) {
            return apiResponse(false, 'Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            \Log::error('Error updating group: ' . $e->getMessage());
            return apiResponse(false, 'An error occurred while updating the group', null, 500);
        }
    }

    // Delete group (admin only)
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $deleted = $this->groupService->deleteGroup($id);
        if (!$deleted) {
            return apiResponse(true, __('Group not found'), [], 404);
        }

        return apiResponse(true, __('Group deleted successfully'), [], 201);
    }

    // List members of a group (admin only)
    public function members($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $members = $this->groupService->listMembers($id);
        if ($members === null) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        return response()->json($members);
    }

    // Add members to group (admin only)
    public function addMembers(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,user_id',
        ]);
        $members = $this->groupService->addMembers($id, $validated['user_ids']);
        if ($members === null) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        return response()->json($members);
    }

    // Remove members from group (admin only)
    public function removeMembers(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,user_id',
        ]);
        $members = $this->groupService->removeMembers($id, $validated['user_ids']);
        if ($members === null) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        return response()->json($members);
    }

    public function setSortOrder(Request $request)
    {
        $sortOrders = $request->input('sort_orders');

        if (!is_array($sortOrders)) {
            return response()->json(['message' => 'Invalid input format. Expected an array.'], 400);
        }

        $groupIds = array_keys($sortOrders);

        // Get only existing group_ids
        $validGroupIds = Group::whereIn('group_id', $groupIds)->pluck('group_id')->toArray();

        foreach ($sortOrders as $groupId => $sortOrder) {
            if (in_array($groupId, $validGroupIds)) {
                Group::where('group_id', $groupId)->update(['sort_order' => $sortOrder]);
            }
        }

        return response()->json(['message' => 'Sort order updated for valid groups only.']);
    }
}
