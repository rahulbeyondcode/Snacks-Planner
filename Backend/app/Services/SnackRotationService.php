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

                // Reset all users in previous group to employee
                $snackManagerRoleId = Role::SNACK_MANAGER; // 2
                $employeeRoleId = Role::EMPLOYEE; // 4
                $members = GroupMember::where('group_id', $activeGroup->group_id)->get();
                foreach ($members as $member) {
                    $user = User::find($member->user_id);
                    $user->role_id = $employeeRoleId;
                    $user->save();
                    Log::info("User {$user->name} role reset to Employee (group transition)");
                }
            }

            // 2. Activate the next group in order
            $groups = Group::orderBy('group_id')->get();
            $nextGroup = null;
            if ($activeGroup) {
                $nextGroup = $groups->firstWhere('group_id', '>', $activeGroup->group_id);
            }
            if (!$nextGroup) {
                $nextGroup = $groups->first(); // wrap to first group if at end
            }
            if ($nextGroup) {
                $nextGroup->group_status = 'active';
                $nextGroup->save();
                Log::info("Group {$nextGroup->name} marked as active for month {$month}");

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
                // No sub-groups: treat all group members as normal employees (except Snack Managers)
                Log::info("No sub-groups (group_weekly_operations) for group {$nextGroup->name}. Resetting all non-Snack Manager users to Employee.");
                $members = GroupMember::where('group_id', $nextGroup->group_id)->get();
                foreach ($members as $member) {
                    if ($member->role_id != Role::SNACK_MANAGER) {
                        $user = User::find($member->user_id);
                        if ($user && $user->role_id != Role::SNACK_MANAGER) {
                            $user->role_id = $employeeRoleId;
                            $user->save();
                            Log::info("User {$user->name} set to Employee (no sub-groups)");
                        }
                    }
                }
            } else {
                // There are sub-groups: set users in active date range to 'operation', revert others to 'employee'
                foreach ($weeklyOps as $op) {
                    $start = Carbon::parse($op->week_start_date);
                    $end = Carbon::parse($op->week_end_date ?? $op->week_start_date)->endOfDay();
                    $user = User::find($op->employee_id);
                    if (!$user || $user->role_id == Role::SNACK_MANAGER) {
                        continue; // skip snack managers
                    }
                    if ($now->between($start, $end)) {
                        if ($user->role_id != $operationRoleId) {
                            $user->role_id = $operationRoleId;
                            $user->save();
                            Log::info("User {$user->name} set to Operation for week {$start->toDateString()} - {$end->toDateString()}");
                        }
                    } else {
                        if ($user->role_id != $employeeRoleId) {
                            $user->role_id = $employeeRoleId;
                            $user->save();
                            Log::info("User {$user->name} set to Employee (not in active sub-group)");
                        }
                    }
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
}
