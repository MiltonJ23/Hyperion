<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Card; 


class UserCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $users = User::all();
       $cards = Card::pluck('bank_card_id')->all(); 


       if(empty($cards)){
        $this->command->info("There are no cards to assign to each users");
        return ;
       }

         $users->each(function($user) use ($cards){
                $cardsToAttachCount = rand(1,3);
                $randomCardIds = collect($cards)->random($cardsToAttachCount)->all();
                $user->Cards()->attach($randomCardIds);
         });
    }
}
