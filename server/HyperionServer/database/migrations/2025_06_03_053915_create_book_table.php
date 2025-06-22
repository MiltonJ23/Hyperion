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
        Schema::create('book', function (Blueprint $table) {
            $table->id('booking_id'); 
            
            $table->uuid('fk1_user_id');
            $table->uuid('fk2_event_id');

            
            $table->decimal('price_at_booking', 10, 2)->default(0.00);
            
            $table->string('status')->default('pending'); 
            $table->timestamps(); 

            $table->foreign('fk1_user_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('fk2_event_id')
                  ->references('event_id')->on('event')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('book');
        Schema::enableForeignKeyConstraints();
    }
};
