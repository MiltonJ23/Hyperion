<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'fk_user_id' => User::factory(),
            'event_name' => $this->faker->sentence(4),
            'event_desc' => $this->faker->paragraphs(3, true),
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+6 months'),
            'event_time' => $this->faker->time('H:i'),
            'event_venue' => $this->faker->randomElement(['Palais des Congrès', 'Hilton Hotel', 'Djeuga Palace', 'Local Community Hall']),
            'event_location' => $this->faker->randomElement(['Yaoundé, Cameroon', 'Douala, Cameroon', 'Bamenda, Cameroon', 'Garoua, Cameroon','Buea, Cameroon', 'Maroua, Cameroon']),
            'event_status' => $this->faker->randomElement(['Waiting', 'In Progress', 'Finished']),
            'event_price' => $this->faker->randomFloat(2, 5000, 45000),
        ];
    }
}
