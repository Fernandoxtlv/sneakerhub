<?php

/*
|--------------------------------------------------------------------------
| SneakerHub Configuration
|--------------------------------------------------------------------------
|
| This file contains all the custom configuration values for SneakerHub.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Store Information
    |--------------------------------------------------------------------------
    */

    'store' => [
        'name' => env('STORE_NAME', 'SneakerHub'),
        'ruc' => env('STORE_RUC', '20123456789'),
        'address' => env('STORE_ADDRESS', 'Av. Principal 123, Lima, Perú'),
        'phone' => env('STORE_PHONE', '+51 999 999 999'),
        'email' => env('STORE_EMAIL', 'tienda@sneakerhub.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Configuration
    |--------------------------------------------------------------------------
    */

    'tax_rate' => (float) env('TAX_RATE', 18),
    'tax_name' => env('TAX_NAME', 'IGV'),

    /*
    |--------------------------------------------------------------------------
    | Shipping Configuration
    |--------------------------------------------------------------------------
    */

    'delivery_fee' => (float) env('DELIVERY_FEE', 15.00),
    'free_delivery_threshold' => (float) env('FREE_DELIVERY_THRESHOLD', 300.00),

    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    */

    'currency' => [
        'code' => env('CURRENCY_CODE', 'PEN'),
        'symbol' => env('CURRENCY_SYMBOL', 'S/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stock Configuration
    |--------------------------------------------------------------------------
    */

    'stock_alert_threshold' => (int) env('STOCK_ALERT_THRESHOLD', 5),

    /*
    |--------------------------------------------------------------------------
    | Yape Configuration
    |--------------------------------------------------------------------------
    */

    'yape' => [
        'enabled' => (bool) env('YAPE_ENABLED', true),
        'merchant_id' => env('YAPE_MERCHANT_ID'),
        'api_key' => env('YAPE_API_KEY'),
        'webhook_secret' => env('YAPE_WEBHOOK_SECRET'),
        'phone_number' => env('YAPE_PHONE_NUMBER'),
        'test_mode' => (bool) env('YAPE_TEST_MODE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Configuration
    |--------------------------------------------------------------------------
    */

    'images' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
        'sizes' => [
            'thumb' => 200,
            'medium' => 800,
            'original' => 1920,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'products' => 12,
        'orders' => 15,
        'users' => 20,
    ],

];
