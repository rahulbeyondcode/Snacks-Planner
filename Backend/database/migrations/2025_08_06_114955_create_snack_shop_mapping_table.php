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
        Schema::create('snack_shop_mapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('snack_item_id');
            $table->unsignedBigInteger('shop_id');
            $table->decimal('snack_price', 8, 2);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('snack_item_id')->references('snack_item_id')->on('snack_items')->onDelete('cascade');
            $table->foreign('shop_id')->references('shop_id')->on('shops')->onDelete('cascade');

            // Unique constraint to prevent duplicate snack-shop combinations
            $table->unique(['snack_item_id', 'shop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snack_shop_mapping');
    }
};
