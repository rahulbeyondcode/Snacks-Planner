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
        Schema::create('snack_plan_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('snack_plan_id');
            $table->unsignedBigInteger('snack_item_id');
            $table->unsignedBigInteger('shop_id');
            $table->integer('quantity');           
            $table->enum('category', ['veg', 'non-veg', 'other']);
            $table->decimal('price_per_item', 8, 2);
            $table->decimal('discount', 8, 2)->nullable();
            $table->decimal('delivery_charge', 8, 2)->nullable();
            $table->string('notes')->nullable();
            $table->string('upload_receipt')->nullable();
            $table->timestamps();

            $table->foreign('snack_plan_id')->references('snack_plan_id')->on('snack_plans')->onDelete('cascade');
            $table->foreign('snack_item_id')->references('id')->on('snack_items')->onDelete('cascade');
            $table->foreign('shop_id')->references('shop_id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snack_plan_details');
    }
}; 