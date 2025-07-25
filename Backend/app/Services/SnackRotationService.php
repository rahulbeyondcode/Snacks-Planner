<?php

namespace App\Services;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupWeeklyOperation;
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
            $activeGroup = Group::where('status', 'active')->first();
            if ($activeGroup) {
                $activeGroup->status = 'inactive';
                $activeGroup->save();
                Log::info("Group {$activeGroup->name} marked as inactive for month {$month}");

                // Reset all non-Snack Manager users in previous group to employee
                $snackManagerRoleId = $this->getRoleIdByName('Snack Manager');
                $employeeRoleId = $this->getRoleIdByName('Employee');
                $members = GroupMember::where('group_id', $activeGroup->group_id)->get();
                foreach ($members as $member) {
                    if ($member->role_id != $snackManagerRoleId) {
                        $user = User::find($member->user_id);
                        if ($user && $user->role_id != $snackManagerRoleId) {
                            $user->role_id = $employeeRoleId;
                            $user->save();
                            Log::info("User {$user->name} role reset to Employee (group transition)");
                        }
                    }
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
                $nextGroup->status = 'active';
                $nextGroup->save();
                Log::info("Group {$nextGroup->name} marked as active for month {$month}");
            }

            // 3. Handle sub-group (weekly operation) transitions for the active group
            $weeklyOps = GroupWeeklyOperation::where('group_id', $nextGroup->group_id)
                ->orderBy('week_start_date')
                ->get();
            $operationRoleId = $this->getRoleIdByName('Operation');
            $employeeRoleId = $this->getRoleIdByName('Employee');
            foreach ($weeklyOps as $op) {
                $start = Carbon::parse($op->week_start_date);
                $end = Carbon::parse($op->week_end_date ?? $op->week_start_date)->endOfDay();
                if ($now->between($start, $end)) {
                    // Set users in this sub-group to operation
                    $user = User::find($op->employee_id);
                    if ($user && $user->role_id != $snackManagerRoleId) {
                        $user->role_id = $operationRoleId;
                        $user->save();
                        Log::info("User {$user->name} set to Operation for week {$start->toDateString()} - {$end->toDateString()}");
                    }
                } else {
                    // Set users in other sub-groups to employee (unless Snack Manager)
                    $user = User::find($op->employee_id);
                    if ($user && $user->role_id != $snackManagerRoleId) {
                        $user->role_id = $employeeRoleId;
                        $user->save();
                        Log::info("User {$user->name} set to Employee (not in current sub-group)");
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
