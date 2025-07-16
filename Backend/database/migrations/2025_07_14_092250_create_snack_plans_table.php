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
        Schema::create('snack_plans', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('snack_day_id');
    $table->unsignedBigInteger('snack_item_id');
    $table->integer('quantity');
    $table->decimal('delivery_charge', 10, 2)->nullable();
    $table->decimal('total', 12, 2);
    $table->string('receipt')->nullable();
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('planned_by');
    $table->timestamps();

    $table->foreign('snack_day_id')->references('id')->on('snack_days')->onDelete('cascade');
    $table->foreign('snack_item_id')->references('id')->on('snack_items')->onDelete('cascade');
    $table->foreign('planned_by')->references('id')->on('users')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snack_plans');
    }
};
