<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 500);
        $tax = $subtotal * 0.18;
        $deliveryFee = 10.00;

        return [
            'order_number' => 'SNEAK-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'paid', 'processing', 'shipped', 'completed']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'delivery_fee' => $deliveryFee,
            'total' => $subtotal + $tax + $deliveryFee,
            'payment_method' => fake()->randomElement(['cash', 'yape']),
            'payment_status' => fake()->randomElement(['pending', 'paid']),
            'shipping_address' => json_encode([
                'name' => fake()->name(),
                'address' => fake()->streetAddress(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
            ]),
            'notes' => fake()->optional()->sentence(),
            'created_by' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'paid',
            'payment_status' => 'paid',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }

    public function yape(): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_method' => 'yape',
        ]);
    }

    public function cash(): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_method' => 'cash',
        ]);
    }
}
