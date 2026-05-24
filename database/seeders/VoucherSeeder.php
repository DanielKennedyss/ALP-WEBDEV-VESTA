<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. VESTAPRIVE10 - Percentage Discount (10% Off)
        Voucher::updateOrCreate(
            ['code' => 'VESTAPRIVE10'],
            [
                'type' => 'percentage',
                'reward_value' => 10.00,
                'total_quota' => 100,
                'used_quota' => 0,
                'expired_at' => now()->addYear(),
            ]
        );

        // 2. HAUTE100 - Fixed Value Discount (IDR 100,000 Off)
        Voucher::updateOrCreate(
            ['code' => 'HAUTE100'],
            [
                'type' => 'fixed',
                'reward_value' => 100000.00,
                'total_quota' => 50,
                'used_quota' => 0,
                'expired_at' => now()->addYear(),
            ]
        );

        // 3. WELCOME50 - Fixed Value Discount (IDR 50,000 Off)
        Voucher::updateOrCreate(
            ['code' => 'WELCOME50'],
            [
                'type' => 'fixed',
                'reward_value' => 50000.00,
                'total_quota' => 200,
                'used_quota' => 0,
                'expired_at' => now()->addYear(),
            ]
        );
    }
}
