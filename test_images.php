<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

$app = app();

$product = \App\Models\Product::with('images', 'mainImage')->first();

if ($product) {
    echo "=== PRODUCT ===\n";
    echo "Name: " . $product->name . "\n";
    echo "ID: " . $product->id . "\n";
    echo "Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
    echo "In Stock: " . ($product->stock > 0 ? 'Yes' : 'No') . "\n";
    echo "\n=== MAIN IMAGE ===\n";
    if ($product->mainImage) {
        echo "Main Image Path: " . $product->mainImage->path . "\n";
        echo "Main Image URL: " . asset('storage/' . $product->mainImage->path) . "\n";
        echo "Exists: " . (file_exists(storage_path('app/public/' . $product->mainImage->path)) ? 'Yes' : 'No') . "\n";
    } else {
        echo "No main image\n";
    }
    echo "\n=== ALL IMAGES ===\n";
    echo "Total: " . $product->images->count() . "\n";
    foreach ($product->images as $img) {
        echo "- " . $img->path . " (Main: " . ($img->is_main ? 'Yes' : 'No') . ")\n";
    }
} else {
    echo "No products found\n";
}
