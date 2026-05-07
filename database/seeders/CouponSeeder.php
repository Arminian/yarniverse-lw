<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME',
                'type' => 'percentage',
                'value' => 20,
                'minimum_order_value' => 20,
                'ends_at' => now()->addMonths(3),
            ],
            [
                'code' => 'SAVE30',
                'type' => 'fixed',
                'value' => 30,
                'minimum_order_value' => 50,
                'ends_at' => now()->addMonths(2),
            ],
            [
                'code' => 'FREEBIES',
                'type' => 'fixed',
                'value' => 10,
                'minimum_order_value' => null,
                'usage_limit' => 100,
                'ends_at' => now()->addMonth(),
            ],
            [
                'code' => 'SUMMER2026',
                'type' => 'percentage',
                'value' => 25,
                'minimum_order_value' => 50,
                'maximum_discount' => 50,
                'ends_at' => now()->addMonths(3),
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create([
                'code' => $coupon['code'],
                'type' => $coupon['type'],
                'value' => $coupon['value'],
                'minimum_order_value' => $coupon['minimum_order_value'] ?? null,
                'maximum_discount' => $coupon['maximum_discount'] ?? null,
                'usage_limit' => $coupon['usage_limit'] ?? null,
                'usage_limit_per_customer' => 1,
                'starts_at' => now(),
                'ends_at' => $coupon['ends_at'],
                'is_active' => true,
            ]);
        }

        // Generate random coupons using factory
        Coupon::factory()->count(10)->create();

        $this->command->info('Coupons created successfully!');
    }
}
