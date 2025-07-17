<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@flock.com',
            'password' => Hash::make('flock666'),
        ]);

        // Seed roles and assign admin role to test user
        $this->call(RoleSeeder::class);
        $this->call(AdminUserRoleSeeder::class);        
    }
}
