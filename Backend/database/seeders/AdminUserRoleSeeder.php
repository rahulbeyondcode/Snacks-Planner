<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class AdminUserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (or you can customize to select a specific user)
        $user = User::first();
        $adminRole = Role::where('name', 'admin')->first();

        if ($user && $adminRole) {
            // Attach admin role if not already attached
            $exists = DB::table('user_roles')
                ->where('user_id', $user->id)
                ->where('role_id', $adminRole->id)
                ->exists();
            if (!$exists) {
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role_id' => $adminRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
