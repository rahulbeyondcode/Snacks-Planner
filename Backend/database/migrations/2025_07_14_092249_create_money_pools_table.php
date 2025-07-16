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
        Schema::create('money_pools', function (Blueprint $table) {
    $table->id();
    $table->string('month');
    $table->decimal('per_person_amount', 10, 2);
    $table->unsignedInteger('multiplier')->default(1);
    $table->decimal('total_collected', 12, 2)->default(0);
    $table->decimal('final_pool', 12, 2)->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_pools');
    }
};
