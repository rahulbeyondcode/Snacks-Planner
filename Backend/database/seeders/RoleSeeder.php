<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('roles')->insert([
            [
                'role_id' => 1,
                'name' => 'account_manager',
                'description' => 'Account Manager',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => 2,
                'name' => 'snack_manager',
                'description' => 'Snack Manager',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => 3,
                'name' => 'operation',
                'description' => 'Operation Staff',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => 4,
                'name' => 'employee',
                'description' => 'Employee',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
