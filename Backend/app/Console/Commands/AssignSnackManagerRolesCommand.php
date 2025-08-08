<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignSnackManagerRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snack:assign-manager-roles 
                            {--dry-run : Show what would be updated without making changes} 
                            {--active-only : Only process active groups}
                            {--all-groups : Process all groups regardless of status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign snack_manager role to users based on group_members table before updating all to employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $activeOnly = $this->option('active-only');
        $allGroups = $this->option('all-groups');

        $this->info('🚀 Starting snack manager role assignment process...');

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
        }

        DB::beginTransaction();

        try {
            // Get groups to process based on options
            $groupsQuery = Group::query();

            if ($activeOnly) {
                $groupsQuery->where('group_status', 'active');
                $this->info('📋 Processing ACTIVE groups only');
            } elseif (!$allGroups) {
                // Default: only newly activated groups (active status)
                $groupsQuery->where('group_status', 'active');
                $this->info('📋 Processing newly activated groups (active status)');
            } else {
                $this->info('📋 Processing ALL groups');
            }

            $groups = $groupsQuery->orderBy('group_id')->get();

            if ($groups->isEmpty()) {
                $this->warn('⚠️  No groups found to process');
                DB::rollBack();
                return 0;
            }

            $totalUpdated = 0;
            $totalSkipped = 0;
            $snackManagerRoleId = Role::SNACK_MANAGER; // Role ID 2
            $results = [];

            $this->info("Found {$groups->count()} groups to process");

            foreach ($groups as $group) {
                $this->info("🏢 Processing Group: {$group->name} (ID: {$group->group_id}, Status: {$group->group_status})");

                // Find users with snack_manager role in this group
                $snackManagerMembers = GroupMember::where('group_id', $group->group_id)
                    ->where('role_id', $snackManagerRoleId)
                    ->where('deleted_at', null) // Only active group members
                    ->with(['user', 'user.role'])
                    ->get();

                if ($snackManagerMembers->isEmpty()) {
                    $this->warn("  ⚠️  No snack managers found in group {$group->name}");
                    continue;
                }

                $groupResults = [];
                foreach ($snackManagerMembers as $member) {
                    $user = $member->user;

                    if (!$user) {
                        $this->error("  ❌ User not found for member ID: {$member->group_member_id}");
                        continue;
                    }

                    $currentRole = $user->role ? $user->role->name : 'No Role';

                    if ($user->role_id == $snackManagerRoleId) {
                        $this->comment("  ✅ User {$user->name} ({$user->email}) already has snack_manager role");
                        $totalSkipped++;
                        continue;
                    }

                    $this->info("  🔄 Will assign snack_manager role to: {$user->name} ({$user->email})");
                    $this->info("    📝 Current role: {$currentRole} (ID: {$user->role_id})");
                    $this->info("    📝 New role: snack_manager (ID: {$snackManagerRoleId})");

                    $groupResults[] = [
                        'user_id' => $user->user_id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'old_role' => $currentRole,
                        'old_role_id' => $user->role_id,
                        'new_role_id' => $snackManagerRoleId
                    ];

                    if (!$isDryRun) {
                        $user->role_id = $snackManagerRoleId;
                        $user->save();

                        Log::info("Assigned snack_manager role to user {$user->name} (ID: {$user->user_id}) for group {$group->name}");
                    }

                    $totalUpdated++;
                }

                $results[$group->group_id] = [
                    'group_name' => $group->name,
                    'group_status' => $group->group_status,
                    'users' => $groupResults
                ];
            }

            if ($isDryRun) {
                $this->info("🔍 DRY RUN COMPLETE: {$totalUpdated} users would be updated, {$totalSkipped} already correct");
                DB::rollBack();
            } else {
                DB::commit();
                $this->info("✅ SUCCESS: {$totalUpdated} users have been assigned snack_manager role, {$totalSkipped} were already correct");
                Log::info("AssignSnackManagerRolesCommand completed successfully. Updated {$totalUpdated} users, skipped {$totalSkipped}.");
            }

            // Display detailed summary
            if (!empty($results)) {
                $this->info("\n📊 DETAILED RESULTS:");
                foreach ($results as $groupId => $result) {
                    if (!empty($result['users'])) {
                        $this->info("Group: {$result['group_name']} ({$result['group_status']})");
                        foreach ($result['users'] as $userResult) {
                            $status = $isDryRun ? 'WOULD UPDATE' : 'UPDATED';
                            $this->line("  - {$userResult['name']} ({$userResult['email']}) - {$status}");
                            $this->line("    {$userResult['old_role']} → snack_manager");
                        }
                    }
                }
            }

            // Display summary table
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Groups Processed', $groups->count()],
                    ['Users Updated', $totalUpdated],
                    ['Users Skipped (Already Correct)', $totalSkipped],
                    ['Status', $isDryRun ? 'DRY RUN' : 'COMPLETED']
                ]
            );

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error occurred during role assignment: ' . $e->getMessage());
            Log::error('AssignSnackManagerRolesCommand failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
    }
}
