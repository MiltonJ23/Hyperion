<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_images', function (Blueprint $table) {
            $table->uuid('fk1_event_id');
            $table->uuid('fk2_image_id');
            $table->timestamps(); 

            $table->primary(['fk1_event_id', 'fk2_image_id']);

            $table->foreign('fk1_event_id')
                  ->references('event_id')->on('event')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('fk2_image_id')
                  ->references('image_id')->on('image')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->index('fk2_image_id', 'fk_Event_Images_Image1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('event_images');
        Schema::enableForeignKeyConstraints();
    }
};
