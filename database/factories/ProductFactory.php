<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true) . ' Sneaker';
        $price = fake()->randomFloat(2, 50, 300);

        return [
            'sku' => strtoupper(Str::random(8)),
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'description' => fake()->paragraphs(3, true),
            'price' => $price,
            'cost_price' => $price * 0.6,
            'discount' => fake()->randomElement([0, 0, 0, 5, 10, 15, 20]),
            'stock' => fake()->numberBetween(0, 100),
            'sizes_available' => fake()->randomElements([36, 37, 38, 39, 40, 41, 42, 43, 44, 45], rand(3, 8)),
            'color' => fake()->safeColorName(),
            'featured' => fake()->boolean(20),
            'is_active' => true,
            'rating_avg' => fake()->randomFloat(2, 0, 5),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn(array $attributes) => [
            'featured' => true,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function withDiscount(int $percentage = 20): static
    {
        return $this->state(fn(array $attributes) => [
            'discount' => $percentage,
        ]);
    }
}
