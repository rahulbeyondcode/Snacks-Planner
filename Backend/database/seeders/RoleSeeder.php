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
        \App\Models\Role::insert([
            ['name' => 'admin'], // True admin for middleware
            ['name' => 'accounts'], // Admin
            ['name' => 'manager'], // Monthly Admin
            ['name' => 'operations'], // Weekly Executor
            ['name' => 'employee'], // Future viewer role
        ]);
    }
}
