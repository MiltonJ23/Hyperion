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
        Schema::create('cart', function (Blueprint $table) { 
            $table->uuid('cart_id')->primary();
            $table->string('cart_state', 10)->nullable(); 
             $table->foreignUuid('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->decimal('cart_total_price', 10, 2)->nullable(); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
