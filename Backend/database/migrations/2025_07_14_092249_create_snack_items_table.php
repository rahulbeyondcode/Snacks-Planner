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
    $table->string('category');
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->unsignedBigInteger('shop_id');
    $table->timestamps();
    $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
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
