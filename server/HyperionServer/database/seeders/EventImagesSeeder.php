<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventImages; 
use App\Models\Event; 
use App\Models\Images;

class EventImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all(); 
        $ImagesIds = Images::pluck('image_id')->all();

        // Let's first of all check if the list of ImagesIds is empty 
        if(empty($ImagesIds)){
            $this->command->info('Was not able to find any images to give to the events objects');
            return;
        }

        $events->each(function($event) use ($ImagesIds){
                $ImagesToAttachCount = rand(1,6);
                $randomImageIds = collect($ImagesIds)->random($ImagesToAttachCount)->all();
                $event->Images()->attach($randomImageIds);
        });
    }
}
