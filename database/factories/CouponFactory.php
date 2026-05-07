<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['fixed', 'percentage']);
        $value = $type === 'percentage' ? fake()->numberBetween(5, 75) : fake()->numberBetween(5, 100);

        return [
            'code' => strtoupper(Str::random(10)),
            'type' => $type,
            'value' => $value,
            'minimum_order_value' => fake()->boolean(50) ? fake()->numberBetween(1, 100) : null,
            'maximum_discount' => $type === 'percentage' ? fake()->numberBetween(20, 99) : null,
            'usage_limit' => fake()->boolean(70) ? fake()->numberBetween(1, 100) : null,
            'usage_limit_per_customer' => fake()->boolean(80) ? fake()->numberBetween(1, 5) : null,
            'starts_at' => now()->subDays(fake()->numberBetween(0, 30)),
            'ends_at' => now()->addDays(fake()->numberBetween(30, 90)),
            'is_active' => true,
        ];
    }
}
