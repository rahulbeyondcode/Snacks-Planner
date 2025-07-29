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
        Schema::create('money_pool_settings', function (Blueprint $table) {
            $table->bigIncrements('money_pool_setting_id');
            $table->decimal('per_month_amount', 10, 2);
            $table->integer('multiplier');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_pool_settings');
    }
};
