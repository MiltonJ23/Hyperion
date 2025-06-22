<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

     public function up(): void
    {
        Schema::create('event', function (Blueprint $table) { 
            $table->uuid('event_id')->primary();
            $table->uuid('fk_user_id'); 
            $table->text('event_name');
            $table->text('event_desc')->nullable();
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('event_venue', 200);
            $table->string('event_location', 255)->nullable();
            $table->enum('event_status', ['Waiting', 'In Progress', 'Finished']);
            $table->decimal('event_price', 10, 2);
            $table->timestamps();

            $table->foreign('fk_user_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index('fk_user_id', 'fk_Event_User1_idx'); 
        });
    }


  
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('event');
        Schema::enableForeignKeyConstraints();
    }
};
