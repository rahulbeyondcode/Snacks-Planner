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
        if (!Schema::hasTable('shop_payment_methods')) {
            Schema::create('shop_payment_methods', function (Blueprint $table) {
                $table->bigIncrements('shop_payment_method_id');
                $table->unsignedBigInteger('shop_id');
                $table->enum('payment_method', ['cash', 'bank_transfer', 'card', 'upi']);
                $table->timestamps();
                $table->foreign('shop_id')->references('shop_id')->on('shops')->onDelete('cascade');
                
                // Add unique constraint to prevent duplicate payment methods for the same shop
                $table->unique(['shop_id', 'payment_method']);
            });  
        }        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {       
        Schema::dropIfExists('shop_payment_methods');        
    }
}; 