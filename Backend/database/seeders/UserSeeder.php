<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('users')->insert([
            [
                'name' => 'Alice Manager',
                'email' => 'alice.manager@example.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Bob Operations',
                'email' => 'bob.operations@example.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Carol Employee',
                'email' => 'carol.employee@example.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Dave OpManager',
                'email' => 'dave.opmanager@example.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
