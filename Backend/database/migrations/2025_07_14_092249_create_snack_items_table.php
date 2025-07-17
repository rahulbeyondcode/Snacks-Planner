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
        Schema::create('snack_items', function (Blueprint $table) {
            $table->id();   
            $table->string('snack_name');
            $table->string('snack_description')->nullable();             
            $table->enum('snack_size', ['small', 'medium', 'large'])->default('medium');    
            $table->timestamps();    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snack_items');
    }
};
