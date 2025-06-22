<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ticket; 
use App\Models\Event; 


class TicketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();
        $users = User::all();

        if ($users->isEmpty() || $events->isEmpty()) {
            $this->command->info('Please seed users and events before seeding tickets.');
            return;
        }

        // Loop through each event to create tickets for it
        $events->each(function ($event) use ($users) {
            // For each event, create tickets and assign a random existing user
            Ticket::factory()
                ->count(rand(50, 150)) // Create 50 to 150 tickets per event
                ->create([
                    'fk_event_id' => $event->event_id,
                    'fk_user_id' => $users->random()->user_id,
                ]);
        });

        $this->command->info('Tickets have been successfully created and assigned to events and users.');
    }
}
