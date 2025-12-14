<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Store settings
            ['key' => 'store_name', 'value' => 'SneakerHub', 'type' => 'string', 'group' => 'store', 'is_public' => true],
            ['key' => 'store_ruc', 'value' => '20123456789', 'type' => 'string', 'group' => 'store', 'is_public' => true],
            ['key' => 'store_address', 'value' => 'Av. Principal 123, Lima, Perú', 'type' => 'string', 'group' => 'store', 'is_public' => true],
            ['key' => 'store_phone', 'value' => '+51 999 999 999', 'type' => 'string', 'group' => 'store', 'is_public' => true],
            ['key' => 'store_email', 'value' => 'tienda@sneakerhub.com', 'type' => 'string', 'group' => 'store', 'is_public' => true],

            // Tax settings
            ['key' => 'tax_rate', 'value' => '18', 'type' => 'float', 'group' => 'tax', 'is_public' => true],
            ['key' => 'tax_name', 'value' => 'IGV', 'type' => 'string', 'group' => 'tax', 'is_public' => true],

            // Shipping settings
            ['key' => 'delivery_fee', 'value' => '15.00', 'type' => 'float', 'group' => 'shipping', 'is_public' => true],
            ['key' => 'free_delivery_threshold', 'value' => '300.00', 'type' => 'float', 'group' => 'shipping', 'is_public' => true],

            // Payment settings
            ['key' => 'yape_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'payment', 'is_public' => true],
            ['key' => 'yape_phone', 'value' => '999999999', 'type' => 'string', 'group' => 'payment', 'is_public' => true],
            ['key' => 'cash_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'payment', 'is_public' => true],

            // General settings
            ['key' => 'stock_alert_threshold', 'value' => '5', 'type' => 'integer', 'group' => 'general', 'is_public' => false],
            ['key' => 'currency_code', 'value' => 'PEN', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'currency_symbol', 'value' => 'S/', 'type' => 'string', 'group' => 'general', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
