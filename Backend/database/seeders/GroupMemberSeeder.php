<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class GroupMemberSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('group_members')->insert([
            [
                'group_id' => 1,
                'user_id' => 1,
                'role_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'group_id' => 1,
                'user_id' => 2,
                'role_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'group_id' => 2,
                'user_id' => 3,
                'role_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'group_id' => 2,
                'user_id' => 4,
                'role_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
