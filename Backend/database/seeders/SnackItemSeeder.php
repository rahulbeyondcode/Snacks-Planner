<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SnackItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('snack_items')->insert([
            [
                'name' => 'Potato Chips',
                'description' => 'Classic salted chips',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Chocolate Bar',
                'description' => 'Milk chocolate bar',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Fruit Juice',
                'description' => 'Mixed fruit juice',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
