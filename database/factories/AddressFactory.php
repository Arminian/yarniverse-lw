<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'country' => 'US',
            'state' => fake()->state(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'address_line_one' => fake()->streetAddress(),
            'address_line_two' => fake()->boolean(30) ? fake()->secondaryAddress() : null,
            'is_default' => false,
            'type' => fake()->randomElement(['shipping', 'billing', 'both']),
        ];
    }
}
