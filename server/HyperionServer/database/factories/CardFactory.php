<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Card;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_card_number' => $this->faker->unique()->creditCardNumber(),
            'bank_card_holder_name' => $this->faker->name(),
            'bank_card_type' => $this->faker->randomElement(['Debit', 'Credit']),
            'bank_provider' => $this->faker->creditCardType(),
            'date_peremption' => $this->faker->creditCardExpirationDate(),
            'cvv' => $this->faker->numerify('###'),
            'bank_card_issuer' => $this->faker->randomElement(['UBA', 'Afriland First Bank', 'Société Générale', 'BICEC']),
            'bank_sequence' => $this->faker->numerify('################'),
    
        ];
    }
}
