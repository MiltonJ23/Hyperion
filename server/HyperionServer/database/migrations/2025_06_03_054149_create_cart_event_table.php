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
        Schema::create('cart_event', function (Blueprint $table) {
            $table->uuid('fk1_cart_id');
            $table->uuid('fk2_event_id');
            $table->timestamps(); 
            $table->primary(['fk1_cart_id', 'fk2_event_id']);

            $table->foreign('fk1_cart_id')
                  ->references('cart_id')->on('cart')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('fk2_event_id')
                  ->references('event_id')->on('event')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
             $table->index('fk2_event_id', 'fk_Cart_Event_Event1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_event');
    }
};
