<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {            
        $categories = [
            ['name' => 'Wool', 'description' => 'Warm, durable animal-fiber yarn for cozy knits'],
            ['name' => 'Linen', 'description' => 'Breathable flax fabric/yarn for cool summer wear'],
            ['name' => 'Silk', 'description' => 'Lustrous, soft fiber for luxurious garments'],
            ['name' => 'Mohair', 'description' => 'Silky, fluffy yarn with sheen and warmth'],
            ['name' => 'Embroidery threads', 'description' => 'Colored threads for decorative stitching'],
            ['name' => 'Craft supplies', 'description' => 'Tools and materials for DIY projects'],
            ['name' => 'Crochet hooks', 'description' => 'Hooks in various sizes for crochet'],
            ['name' => 'Bath Utensils', 'description' => 'Practical tools for bathing and exfoliation'],
        ];

        foreach ($categories as $index => $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => \Illuminate\Support\Str::slug($category['name']),
                'description' => $category['description'],
                'is_active' => true,
                'sort_order' => $index,
                'meta_title' => $category['name'] . ' - Shop Online',
                'meta_description' => $category['description'],
            ]);
        }

        $this->command->info('Categories created successfully!');
    }
}
