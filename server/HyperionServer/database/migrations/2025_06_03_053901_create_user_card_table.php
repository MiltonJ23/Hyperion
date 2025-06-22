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
        Schema::create('user_card', function (Blueprint $table) {
            $table->uuid('fk1_user_id'); 
            $table->uuid('fk2_bank_card_id'); 

            $table->primary(['fk1_user_id', 'fk2_bank_card_id']); 

            $table->foreign('fk1_user_id')
                  ->references('user_id')->on('users') 
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('fk2_bank_card_id')
                  ->references('bank_card_id')->on('card') 
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_card');
    }
};
