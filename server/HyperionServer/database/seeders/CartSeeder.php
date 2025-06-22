<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $users->each(function ($user) {
            Cart::factory()->create([
                'user_id' => $user->user_id, 
                'cart_state' => 'active',    
            ]);
        });
        
        $this->command->info('An active cart has been created for each user.');
    }
}
