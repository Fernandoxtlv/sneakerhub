<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_page_loads_successfully()
    {
        $category = Category::factory()->create([
            'name' => 'Running',
            'slug' => 'running',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->get(route('category.show', $category));

        $response->assertStatus(200);
        $response->assertSee('Running');
    }

    public function test_brand_page_loads_successfully()
    {
        $brand = Brand::factory()->create([
            'name' => 'Nike',
            'slug' => 'nike',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        $response = $this->get(route('brand.show', $brand));

        $response->assertStatus(200);
        $response->assertSee('Nike');
    }
}
