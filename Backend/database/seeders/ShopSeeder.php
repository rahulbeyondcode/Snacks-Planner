<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('shops')->insert([
            [
                'name' => 'Snack World',
                'address' => '123 Main St',
                'contact_number' => '1234567890',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Office Bites',
                'address' => '456 Park Ave',
                'contact_number' => '0987654321',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
