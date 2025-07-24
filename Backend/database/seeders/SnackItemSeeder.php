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
                'price' => 50.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Chocolate Bar',
                'description' => 'Milk chocolate bar',
                'price' => 30.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Fruit Juice',
                'description' => 'Mixed fruit juice',
                'price' => 20.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
