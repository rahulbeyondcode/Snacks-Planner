<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('payment_methods')->insert([
            [
                'name' => 'cash',
                'display_name' => 'Cash',
                'description' => 'Payment in cash at the shop',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'upi',
                'display_name' => 'UPI',
                'description' => 'Unified Payments Interface - instant digital payments',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'bank_transfer',
                'display_name' => 'Bank Transfer',
                'description' => 'Direct bank transfer or NEFT/RTGS',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'card',
                'display_name' => 'Card Payment',
                'description' => 'Credit or debit card payments',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'digital_wallet',
                'display_name' => 'Digital Wallet',
                'description' => 'Paytm, PhonePe, Google Pay, etc.',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
} 