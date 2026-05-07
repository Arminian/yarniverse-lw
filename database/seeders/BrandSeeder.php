<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Millefili', 'Hobbii', 'LionBrand', 'Yarnspirations',
            'KnitPicks', 'Borocco', 'WeAreKnitters', 'Malabrigo',
            'UniversalYarn', 'Sheepjes', 'PlymouthYarn', 'CascadeYarns',
            'Rifo', 'ErendiraItalia', 'Pietrasanta', 'Poethica',
        ];

        foreach ($brands as $index => $brandName) {
            Brand::create([
                'name' => $brandName,
                'slug' => \Illuminate\Support\Str::slug($brandName),
                'description' => "Quality products from {$brandName}",
                'website' => "https://www.{$brandName}.com",
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }

        $this->command->info('Brands created successfully!');
    }
}
