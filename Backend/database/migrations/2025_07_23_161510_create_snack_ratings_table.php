<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('snack_ratings', function (Blueprint $table) {
            $table->id('snack_rating_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('snack_item_id');
            $table->tinyInteger('rating'); // 1-5 stars
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('snack_item_id')->references('snack_item_id')->on('snack_items')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('snack_ratings');
    }
};
