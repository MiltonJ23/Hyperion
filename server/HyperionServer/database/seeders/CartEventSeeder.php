<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Event;

class CartEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carts = Cart::where('cart_state', 'active')->get(); 
        $eventIds = Event::pluck('event_id')->all();
        if (empty($eventIds)) {
            $this->command->info('No events found to attach to carts. Please run the EventSeeder first.');
            return;
        }
        $carts->each(function ($cart) use ($eventIds) {
            $eventsToAttach = collect($eventIds)->random(rand(1, 6))->all();

            $cart->events()->attach($eventsToAttach);
        });
    }
}
