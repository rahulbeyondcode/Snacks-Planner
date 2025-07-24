<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('snack_plan_details', function (Blueprint $table) {
            $table->id('snack_plan_detail_id');
            $table->unsignedBigInteger('snack_plan_id');
            $table->unsignedBigInteger('snack_item_id');
            $table->unsignedBigInteger('shop_id');
            $table->integer('quantity');
            $table->enum('category', ['veg', 'non-veg', 'chicken-only']);
            $table->decimal('price_per_item', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->enum('payment_mode', ['cash', 'card', 'upi', 'wallet']);
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('delivery_charge', 10, 2)->nullable();
            $table->text('upload_receipt')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('snack_plan_id')->references('snack_plan_id')->on('snack_plans')->onDelete('cascade');
            $table->foreign('snack_item_id')->references('snack_item_id')->on('snack_items')->onDelete('cascade');
            $table->foreign('shop_id')->references('shop_id')->on('shops')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snack_plan_details');
    }
};
