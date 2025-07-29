<?php

namespace App\Http\Controllers;

use App\Services\GroupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

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

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
            'description' => 'nullable|string|max:255',
            'employees' => 'required|array',
            'operation_managers' => 'required|array',
        ]);

        $newGroup = $this->groupService->createGroup($validated);

        return apiResponse(true, __('messages.success'), $newGroup, 201);
    }

    // Update group (admin only)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'employees' => 'required|array',
            'operation_managers' => 'required|array',
        ]);
        $updatedGroup = $this->groupService->updateGroup($id, $validated);
        if (!$updatedGroup) {
            return apiResponse(false, __('Group not found'), null, 404);
        }

        return apiResponse(true, __('messages.success'), $updatedGroup, 201);
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
        $$sortOrders = $request->input('sort_orders');

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
