<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and assign admin role to test user
        $this->call(RoleSeeder::class);
        $this->call(AdminUserRoleSeeder::class);

        // Create 10 dummy users
        \App\Models\User::factory(10)->create()->each(function ($user) {
            // Assign a random role to each user
            $role = \App\Models\Role::inRandomOrder()->first();
            if ($role) {
                \App\Models\UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                ]);
            }
            // Create a contribution for each user
            \App\Models\Contribution::factory()->create([
                'user_id' => $user->id,
            ]);
        });

        // Keep the original test user (idempotent)
        $user = \App\Models\User::firstOrCreate(
            [
                'email' => 'test@example.com',
            ],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]
        );
    }
}
