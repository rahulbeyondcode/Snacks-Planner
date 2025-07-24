<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class OfficeHolidaySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('office_holidays')->insert([
            [
                'user_id' => 1,
                'holiday_date' => $now->copy()->addDays(7)->toDateString(),
                'description' => 'Annual Leave',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'holiday_date' => $now->copy()->addDays(10)->toDateString(),
                'description' => 'Medical Leave',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
