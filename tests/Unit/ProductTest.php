<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_has_correct_fillable_attributes(): void
    {
        $product = new Product();

        $this->assertContains('name', $product->getFillable());
        $this->assertContains('sku', $product->getFillable());
        $this->assertContains('price', $product->getFillable());
        $this->assertContains('stock', $product->getFillable());
    }

    public function test_product_belongs_to_brand(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Brand::class, $product->brand);
        $this->assertEquals($brand->id, $product->brand->id);
    }

    public function test_product_belongs_to_category(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_active_scope_returns_only_active_products(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        Product::factory()->count(3)->create([
            'is_active' => true,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        Product::factory()->count(2)->create([
            'is_active' => false,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $activeProducts = Product::active()->get();

        $this->assertCount(3, $activeProducts);
        $this->assertTrue($activeProducts->every(fn($p) => $p->is_active));
    }

    public function test_featured_scope_returns_only_featured_products(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        Product::factory()->count(2)->create([
            'featured' => true,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        Product::factory()->count(4)->create([
            'featured' => false,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $featuredProducts = Product::featured()->get();

        $this->assertCount(2, $featuredProducts);
    }

    public function test_in_stock_scope_returns_products_with_stock(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        Product::factory()->count(3)->create([
            'stock' => 10,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        Product::factory()->count(2)->create([
            'stock' => 0,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $inStockProducts = Product::inStock()->get();

        $this->assertCount(3, $inStockProducts);
    }

    public function test_final_price_calculated_correctly_with_discount(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'price' => 100.00,
            'discount' => 20, // 20% discount
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        // Assuming getFinalPriceAttribute exists
        $this->assertEquals(80.00, $product->current_price);
    }

    public function test_final_price_equals_price_when_no_discount(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'price' => 150.00,
            'discount' => 0,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $this->assertEquals(150.00, $product->current_price);
    }

    public function test_sizes_available_returns_array(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'sizes_available' => [38, 39, 40, 41],
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $sizes = $product->sizes_available;

        $this->assertIsArray($sizes);
        $this->assertContains(38, $sizes);
        $this->assertContains(41, $sizes);
    }
}
