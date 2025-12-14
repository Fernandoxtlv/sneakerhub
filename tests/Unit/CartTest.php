<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function createProduct(array $attributes = []): Product
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        return Product::factory()->create(array_merge([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ], $attributes));
    }

    public function test_cart_can_be_created_for_user(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals($user->id, $cart->user_id);
    }

    public function test_cart_can_be_created_for_guest_with_session(): void
    {
        $sessionId = 'test-session-' . uniqid();
        $cart = Cart::create(['session_id' => $sessionId]);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertNull($cart->user_id);
        $this->assertEquals($sessionId, $cart->session_id);
    }

    public function test_cart_has_many_items(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $product1 = $this->createProduct(['price' => 100]);
        $product2 = $this->createProduct(['price' => 150]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => $product1->price,
            'subtotal' => $product1->price * 2,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => $product2->price,
            'subtotal' => $product2->price,
        ]);

        $this->assertCount(2, $cart->items);
    }

    public function test_cart_calculates_total_correctly(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $product1 = $this->createProduct(['price' => 100]);
        $product2 = $this->createProduct(['price' => 50]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 100,
            'subtotal' => 200,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'price' => 50,
            'subtotal' => 150,
        ]);

        // Assuming getTotal method exists
        $this->assertEquals(350, $cart->total);
    }

    public function test_cart_calculates_item_count_correctly(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $product1 = $this->createProduct(['price' => 100]);
        $product2 = $this->createProduct(['price' => 50]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 100,
            'subtotal' => 200,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'price' => 50,
            'subtotal' => 150,
        ]);

        // Assuming getItemCount method exists
        $this->assertEquals(5, $cart->items_count);
    }

    public function test_cart_item_subtotal_calculated_correctly(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $product = $this->createProduct(['price' => 75.50]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 4,
            'price' => 75.50,
            'subtotal' => 75.50 * 4,
        ]);

        $this->assertEquals(302.00, $item->subtotal);
    }

    public function test_empty_cart_returns_zero_total(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $this->assertEquals(0, $cart->total);
    }

    public function test_cart_can_clear_all_items(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $product = $this->createProduct();

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->price,
            'subtotal' => $product->price * 2,
        ]);

        $cart->items()->delete();

        $this->assertCount(0, $cart->fresh()->items);
    }
}
