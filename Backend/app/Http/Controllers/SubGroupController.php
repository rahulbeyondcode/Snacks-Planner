<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubGroupRequest;
use App\Http\Requests\UpdateSubGroupRequest;
use App\Services\SubGroupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubGroupController extends Controller
{
    protected $subGroupService;

    public function __construct(SubGroupServiceInterface $subGroupService)
    {
        $this->subGroupService = $subGroupService;
    }

    /**
     * List all sub groups with optional filters
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role->name, ['account_manager', 'snack_manager'])) {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $filters = $request->only(['search', 'group_id', 'status']);
            $subGroups = $this->subGroupService->listSubGroups($filters);

            return apiResponse(true, __('messages.success'), $subGroups, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Show sub group details
     */
    public function show($id)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role->name, ['account_manager', 'snack_manager'])) {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $subGroup = $this->subGroupService->getSubGroup($id);
            if (! $subGroup) {
                return apiResponse(false, 'Sub group not found', null, 404);
            }

            return apiResponse(true, __('messages.success'), $subGroup, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Create new sub group
     */
    public function store(StoreSubGroupRequest $request)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role->name, ['account_manager', 'snack_manager'])) {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $validated = $request->validated();
            $subGroup = $this->subGroupService->createSubGroup($validated);

            return apiResponse(true, __('messages.create_msg'), $subGroup, 201);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update sub group
     */
    public function update(UpdateSubGroupRequest $request, $id)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role->name, ['account_manager', 'snack_manager'])) {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $validated = $request->validated();
            $subGroup = $this->subGroupService->updateSubGroup($id, $validated);

            if (! $subGroup) {
                return apiResponse(false, 'Sub group not found', null, 404);
            }

            return apiResponse(true, __('messages.update_msg'), $subGroup, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Delete sub group
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role->name, ['account_manager', 'snack_manager'])) {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $deleted = $this->subGroupService->deleteSubGroup($id);
            if (! $deleted) {
                return apiResponse(false, 'Sub group not found', null, 404);
            }

            return apiResponse(true, __('messages.delete_msg'), null, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * List sub group members
     */
    public function members($id)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role->name, ['account_manager', 'snack_manager'])) {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $members = $this->subGroupService->listMembers($id);

            return apiResponse(true, __('messages.success'), $members, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Add members to sub group
     */
    public function addMembers(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role->name, ['account_manager', 'snack_manager'])) {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,user_id',
        ]);

        try {
            $this->subGroupService->addMembers($id, $validated['user_ids']);

            return apiResponse(true, 'Members added successfully', null, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove members from sub group
     */
    public function removeMembers(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role->name, ['account_manager', 'snack_manager'])) {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,user_id',
        ]);

        try {
            $this->subGroupService->removeMembers($id, $validated['user_ids']);

            return apiResponse(true, 'Members removed successfully', null, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Get sub groups by group
     */
    public function getByGroup($groupId)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role->name, ['account_manager', 'snack_manager'])) {
            return apiResponse(false, __('messages.forbidden'), null, 403);
        }

        try {
            $subGroups = $this->subGroupService->getByGroup($groupId);

            return apiResponse(true, __('messages.success'), $subGroups, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), null, 500);
        }
    }
}
