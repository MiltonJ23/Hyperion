<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'cart_state' => $this->faker->randomElement(['active', 'completed', 'abandoned']),

            
            'cart_total_price' => $this->faker->randomFloat(2, 10000, 200000),
        ];
    }
}
