<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('groups')->insert([
            [
                'name' => 'Engineering',
                'description' => 'Engineering team',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'HR',
                'description' => 'Human Resources',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
