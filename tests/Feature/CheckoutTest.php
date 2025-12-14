<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    protected function createProduct(array $attributes = []): Product
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        return Product::factory()->create(array_merge([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'stock' => 10,
        ], $attributes));
    }

    protected function createUserWithCart(): array
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $cart = Cart::create(['user_id' => $user->id]);
        $product = $this->createProduct(['price' => 100, 'stock' => 20]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'size' => '42',
            'price' => 100,
            'subtotal' => 200,
        ]);

        return ['user' => $user, 'cart' => $cart, 'product' => $product];
    }

    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get(route('checkout'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_checkout(): void
    {
        $data = $this->createUserWithCart();

        $response = $this->actingAs($data['user'])->get(route('checkout'));

        $response->assertStatus(200);
    }

    public function test_checkout_with_empty_cart_redirects_back(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        Cart::create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('checkout'));

        $response->assertRedirect();
    }

    public function test_checkout_creates_order_with_cash_payment(): void
    {
        $data = $this->createUserWithCart();

        $response = $this->actingAs($data['user'])->post(route('checkout.process'), [
            'shipping_name' => 'John Doe',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Lima',
            'shipping_phone' => '999888777',
            'payment_method' => 'cash',
            'notes' => 'Test order',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $data['user']->id,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);
    }

    public function test_checkout_creates_order_with_yape_payment(): void
    {
        $data = $this->createUserWithCart();

        $response = $this->actingAs($data['user'])->post(route('checkout.process'), [
            'shipping_name' => 'Jane Doe',
            'shipping_address' => '456 Oak Ave',
            'shipping_city' => 'Arequipa',
            'shipping_phone' => '999777666',
            'payment_method' => 'yape',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $data['user']->id,
            'payment_method' => 'yape',
        ]);
    }

    public function test_checkout_decreases_product_stock(): void
    {
        $data = $this->createUserWithCart();
        $initialStock = $data['product']->stock;

        $this->actingAs($data['user'])->post(route('checkout.process'), [
            'shipping_name' => 'John Doe',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Lima',
            'shipping_phone' => '999888777',
            'payment_method' => 'cash',
        ]);

        $data['product']->refresh();
        $this->assertEquals($initialStock - 2, $data['product']->stock);
    }

    public function test_checkout_clears_cart_after_order(): void
    {
        $data = $this->createUserWithCart();

        $this->actingAs($data['user'])->post(route('checkout.process'), [
            'shipping_name' => 'John Doe',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Lima',
            'shipping_phone' => '999888777',
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $data['cart']->id,
        ]);
    }

    public function test_checkout_creates_payment_record(): void
    {
        $data = $this->createUserWithCart();

        $this->actingAs($data['user'])->post(route('checkout.process'), [
            'shipping_name' => 'John Doe',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Lima',
            'shipping_phone' => '999888777',
            'payment_method' => 'yape',
        ]);

        $order = Order::where('user_id', $data['user']->id)->first();

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'yape',
        ]);
    }

    public function test_checkout_validates_required_fields(): void
    {
        $data = $this->createUserWithCart();

        $response = $this->actingAs($data['user'])->post(route('checkout.process'), []);

        $response->assertSessionHasErrors(['shipping_name', 'shipping_address', 'shipping_city', 'shipping_phone', 'payment_method']);
    }

    public function test_checkout_success_page_shows_order_details(): void
    {
        $data = $this->createUserWithCart();

        $this->actingAs($data['user'])->post(route('checkout.process'), [
            'shipping_name' => 'John Doe',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Lima',
            'shipping_phone' => '999888777',
            'payment_method' => 'cash',
        ]);

        $order = Order::where('user_id', $data['user']->id)->first();

        $response = $this->actingAs($data['user'])->get(route('checkout.success', $order));

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }
}
