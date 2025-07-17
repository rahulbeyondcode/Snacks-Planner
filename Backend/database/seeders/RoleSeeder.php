<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin', // True admin for middleware
            'accounts', // Admin
            'manager', // Monthly Admin
            'operations', // Weekly Executor
            'employee', // Future viewer role
        ];
        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate(['name' => $role]);
        }
    }
}
