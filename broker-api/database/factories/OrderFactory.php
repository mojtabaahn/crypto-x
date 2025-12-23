<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'symbol' => fake()->randomElement(['BTC', 'ETH', 'DOGE']),
            'side' => fake()->randomElement(['buy', 'sell']),
            'price' => fake()->randomFloat(2, 10, 50000),
            'amount' => fake()->randomFloat(2, 0.1, 100),
            'status' => fake()->randomElement(\App\OrderStatus::cases())->value,
        ];
    }
}
