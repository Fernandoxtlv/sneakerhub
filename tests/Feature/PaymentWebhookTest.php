<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_yape_webhook_updates_payment_status(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'yape',
            'payment_status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'yape',
            'amount' => $order->total,
            'status' => 'pending',
            'metadata' => json_encode(['yape_reference' => 'REF123']),
        ]);

        $response = $this->postJson('/api/webhooks/yape', [
            'transaction_id' => 'TXN123456',
            'reference' => 'REF123',
            'amount' => $order->total,
            'status' => 'completed',
        ]);

        $response->assertStatus(200);

        $payment->refresh();
        $order->refresh();

        $this->assertEquals('completed', $payment->status);
        $this->assertEquals('paid', $order->payment_status);
    }

    public function test_yape_webhook_requires_valid_reference(): void
    {
        $response = $this->postJson('/api/webhooks/yape', [
            'transaction_id' => 'TXN123456',
            'reference' => 'INVALID_REF',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        $response->assertStatus(404);
    }

    public function test_yape_webhook_validates_amount(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'yape',
            'payment_status' => 'pending',
            'total' => 200.00,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'yape',
            'amount' => 200.00,
            'status' => 'pending',
            'metadata' => json_encode(['yape_reference' => 'REF456']),
        ]);

        $response = $this->postJson('/api/webhooks/yape', [
            'transaction_id' => 'TXN789',
            'reference' => 'REF456',
            'amount' => 100.00, // Wrong amount
            'status' => 'completed',
        ]);

        $response->assertStatus(400);
    }

    public function test_yape_webhook_handles_failed_payment(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'yape',
            'payment_status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'yape',
            'amount' => $order->total,
            'status' => 'pending',
            'metadata' => json_encode(['yape_reference' => 'REF789']),
        ]);

        $response = $this->postJson('/api/webhooks/yape', [
            'transaction_id' => 'TXN_FAILED',
            'reference' => 'REF789',
            'amount' => $order->total,
            'status' => 'failed',
        ]);

        $response->assertStatus(200);

        $payment->refresh();
        $order->refresh();

        $this->assertEquals('failed', $payment->status);
        $this->assertEquals('failed', $order->payment_status);
    }

    public function test_yape_webhook_stores_transaction_id(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'yape',
            'payment_status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'yape',
            'amount' => $order->total,
            'status' => 'pending',
            'metadata' => json_encode(['yape_reference' => 'REF_STORE']),
        ]);

        $this->postJson('/api/webhooks/yape', [
            'transaction_id' => 'YAPE_TXN_12345',
            'reference' => 'REF_STORE',
            'amount' => $order->total,
            'status' => 'completed',
        ]);

        $payment->refresh();
        $this->assertEquals('YAPE_TXN_12345', $payment->transaction_id);
    }
}
