<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book; 
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::all();
        $events = Event::pluck('event_id')->all();
        

        if (empty($events)) {
            $this->command->info("There are no events to assign to each book");
            return;
        }
        $user->each(function($user) use ($events) {
            $booksToAttachCount = rand(1, count($events));
            $randomEventIds = collect($events)->random($booksToAttachCount)->all();
            $user->bookedEvents()->attach($randomEventIds);
        });
    }
}
