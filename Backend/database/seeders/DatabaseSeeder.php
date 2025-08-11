<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Always seed roles first for foreign key constraints
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            SnackItemSeeder::class,
            GroupSeeder::class,
            GroupMemberSeeder::class,
            OfficeHolidaySeeder::class,
            PermissionSeeder::class,
            ShopSeeder::class,
        ]);
    }
}
