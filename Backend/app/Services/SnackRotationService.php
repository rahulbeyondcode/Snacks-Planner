<?php

namespace App\Services;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupWeeklyOperation;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SnackRotationService
{
    public function rotateGroupsAndRoles()
    {
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $month = $now->format('Y-m');

            // 1. Find the currently active group
            $activeGroup = Group::where('group_status', 'active')->first();
            if ($activeGroup) {
                $activeGroup->group_status = 'inactive';
                $activeGroup->save();
                Log::info("Group {$activeGroup->name} marked as inactive for month {$month}");
            }

            // Reset all users to employee role except account managers
            $this->resetAllUsersToEmployee();
            Log::info("All users reset to Employee role except Account Managers");

            // 2. Activate the next group in order using sort_order
            $groups = Group::orderBy('sort_order')->get();
            $nextGroup = null;
            if ($activeGroup) {
                $nextGroup = $groups->firstWhere('sort_order', '>', $activeGroup->sort_order);
            }
            if (!$nextGroup) {
                $nextGroup = $groups->first(); // wrap to first group if at end
            }
            if ($nextGroup) {
                $nextGroup->group_status = 'active';
                $nextGroup->save();
                Log::info("Group {$nextGroup->name} marked as active for month {$month}");

                // Rotate sort orders: active group gets sort_order 1, previous active goes to max
                $this->rotateSortOrders($activeGroup, $nextGroup);
                Log::info("Sort orders rotated successfully");

                // 2.1. Assign snack_manager role to users designated as snack managers in the newly activated group
                $snackManagerMembers = GroupMember::where('group_id', $nextGroup->group_id)
                    ->where('role_id', Role::SNACK_MANAGER)
                    ->where('deleted_at', null)
                    ->get();

                foreach ($snackManagerMembers as $member) {
                    $user = User::find($member->user_id);
                    if ($user && $user->role_id != Role::SNACK_MANAGER) {
                        $user->role_id = Role::SNACK_MANAGER;
                        $user->save();
                        Log::info("User {$user->name} assigned snack_manager role for newly activated group {$nextGroup->name}");
                    } elseif ($user && $user->role_id == Role::SNACK_MANAGER) {
                        Log::info("User {$user->name} already has snack_manager role for group {$nextGroup->name}");
                    }
                }
            }

            // 3. Handle sub-group (weekly operation) transitions for the active group
            $weeklyOps = GroupWeeklyOperation::where('group_id', $nextGroup->group_id)
                ->orderBy('week_start_date')
                ->get();
            $operationRoleId = Role::OPERATION; // 3
            $employeeRoleId = Role::EMPLOYEE; // 4

            if ($weeklyOps->isEmpty()) {
                // No sub-groups: all users are already set to employee role from global reset
                // Only snack managers will be promoted from the earlier logic
                Log::info("No sub-groups (group_weekly_operations) for group {$nextGroup->name}. All users remain as Employee except Snack Managers.");
            } else {
                // There are sub-groups: set users in active date range to 'operation', others remain as employee
                foreach ($weeklyOps as $op) {
                    $start = Carbon::parse($op->week_start_date);
                    $end = Carbon::parse($op->week_end_date ?? $op->week_start_date)->endOfDay();
                    $user = User::find($op->employee_id);
                    if (!$user || $user->role_id == Role::SNACK_MANAGER || $user->role_id == Role::ACCOUNT_MANAGER) {
                        continue; // skip snack managers and account managers
                    }
                    if ($now->between($start, $end)) {
                        if ($user->role_id != $operationRoleId) {
                            $user->role_id = $operationRoleId;
                            $user->save();
                            Log::info("User {$user->name} set to Operation for week {$start->toDateString()} - {$end->toDateString()}");
                        }
                    }
                    // No need to revert to employee since global reset already handled this
                }
            }


            DB::commit();
            Log::info('Snack group and role rotation completed successfully.');
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Snack group/role rotation failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function getRoleIdByName($name)
    {
        $role = \App\Models\Role::where('name', $name)->first();
        return $role ? $role->role_id : null;
    }

    /**
     * Rotate sort orders so that the new active group has sort_order 1,
     * the previous active group gets the maximum sort_order,
     * and all other groups shift their sort_order accordingly.
     */
    protected function rotateSortOrders($previousActiveGroup, $newActiveGroup)
    {
        if (!$previousActiveGroup || !$newActiveGroup) {
            return;
        }

        // Get all groups ordered by current sort_order
        $allGroups = Group::orderBy('sort_order')->get();
        $maxSortOrder = $allGroups->count();

        // Create a mapping of new sort orders
        $newSortOrders = [];

        // The new active group always gets sort_order 1
        $newSortOrders[$newActiveGroup->group_id] = 1;

        // The previous active group gets the maximum sort_order
        $newSortOrders[$previousActiveGroup->group_id] = $maxSortOrder;

        // For all other groups, assign sort orders 2, 3, 4, etc.
        $currentOrder = 2;
        foreach ($allGroups as $group) {
            // Skip the groups we've already assigned
            if (
                $group->group_id == $newActiveGroup->group_id ||
                $group->group_id == $previousActiveGroup->group_id
            ) {
                continue;
            }

            $newSortOrders[$group->group_id] = $currentOrder;
            $currentOrder++;
        }

        // Update all groups with their new sort orders
        foreach ($newSortOrders as $groupId => $sortOrder) {
            Group::where('group_id', $groupId)->update(['sort_order' => $sortOrder]);
            Log::info("Group ID {$groupId} sort_order updated to {$sortOrder}");
        }
    }

    /**
     * Reset all users to employee role except those with account_manager role.
     */
    protected function resetAllUsersToEmployee()
    {
        $accountManagerRoleId = Role::ACCOUNT_MANAGER; // 1
        $employeeRoleId = Role::EMPLOYEE; // 4

        // Get all users that are not account managers
        $usersToReset = User::where('role_id', '!=', $accountManagerRoleId)->get();

        foreach ($usersToReset as $user) {
            $user->role_id = $employeeRoleId;
            $user->save();
            Log::info("User {$user->name} role reset to Employee (global reset)");
        }

        Log::info("Reset " . $usersToReset->count() . " users to Employee role");
    }
}
