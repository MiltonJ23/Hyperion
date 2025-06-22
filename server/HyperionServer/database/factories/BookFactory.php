<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Book;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fk1_user_id'=> User::factory(),
            'fk2_event_id' => Event::factory(),
            'status'=>$this->faker->randomElement([ 'pending', 'attended','canceled']),
        ];
    }
}
