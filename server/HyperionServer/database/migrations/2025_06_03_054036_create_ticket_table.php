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
        Schema::create('ticket', function (Blueprint $table) { 
            $table->uuid('ticket_id')->primary();

            $table->unsignedBigInteger('fk_booking_id');

            $table->uuid('fk_user_id');
            $table->uuid('fk_event_id');
            $table->string('ticket_code')->unique();
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            $table->foreign('fk_booking_id')
                  ->references('booking_id')->on('book')
                  ->onDelete('cascade');

            $table->foreign('fk_user_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('fk_event_id')
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
        Schema::dropIfExists('ticket');
        Schema::enableForeignKeyConstraints();
    
    }
};
