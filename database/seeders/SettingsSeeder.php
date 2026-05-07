<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'store_name', 'value' => 'Yarniverse', 'type' => 'string', 'group' => 'general'],
            ['key' => 'store_email', 'value' => 'yarniverse@proton.me', 'type' => 'string', 'group' => 'general'],
            ['key' => 'store_phone', 'value' => '+88 12345678', 'type' => 'string', 'group' => 'general'],
            ['key' => 'store_address', 'value' => '123 Main Street, City, Country', 'type' => 'string', 'group' => 'general'],

            // Shipping Settings
            ['key' => 'flat_shipping_rate', 'value' => '10', 'type' => 'number', 'group' => 'shipping'],
            ['key' => 'free_shipping_threshold', 'value' => '50', 'type' => 'number', 'group' => 'shipping'],

            // Email Settings
            ['key' => 'notification_email', 'value' => 'admin@example.com', 'type' => 'string', 'group' => 'email'],
            ['key' => 'order_confirmation_message', 'value' => 'Thank you for your order! We will process it shortly.', 'type' => 'string', 'group' => 'email'],

            // SEO Settings
            ['key' => 'seo_title', 'value' => 'Online Yarn Store - Best Quality Products', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'seo_description', 'value' => 'Shop for the best quality products at low prices. Free shipping on orders over $50.', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'seo_keywords', 'value' => 'online shopping, ecommerce, yarn, knitting, bath, quality products, best deals', 'type' => 'string', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        $this->command->info('Settings created successfully!');
    }
}
