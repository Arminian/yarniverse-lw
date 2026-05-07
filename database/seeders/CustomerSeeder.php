<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Address;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Generating Customers...');
        $bar = $this->command->getOutput()->createProgressBar(50);

        for ($i = 0; $i < 50; $i++) {
            $customer = Customer::factory()->create();

            Address::factory()->create([
                'customer_id' => $customer->id,
            ]);

            if (rand(0, 100) > 50) {
                Address::factory()->create([
                    'customer_id' => $customer->id,
                ]);
            }

            $reviewCount = rand(0, 3);
            for ($j = 0; $j < $reviewCount; $j++) {
                Review::factory()->create([
                    'customer_id' => $customer->id,
                    'product_id' => Product::inRandomOrder()->first()->id,
                ]);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Customers have been created!');
    }
}
