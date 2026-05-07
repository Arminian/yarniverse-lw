<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Wool',
            'Linen',
            'Silk',
            'Mohair',
            'Embroidery threads',
            'Craft supplies',
            'Crochet hooks',
            'Bath Utensils',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
            'meta_title' => "$name - Shop Online",
            'meta_description' => fake()->sentence(20),
        ];
    }
}
