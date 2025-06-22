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
        Schema::create('card', function (Blueprint $table) { 
            $table->uuid('bank_card_id')->primary();
            $table->string('bank_sequence', 16)->nullable();
            $table->string('bank_provider', 20)->nullable();
            $table->date('date_peremption')->nullable(); 
            $table->string('cvv', 3)->nullable(); 
            $table->string('bank_card_number')->unique(); 
            $table->enum('bank_card_type', ['Debit', 'Credit']); 
            $table->string('bank_card_holder_name'); 
            $table->string('bank_card_issuer')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card');
    }
};
