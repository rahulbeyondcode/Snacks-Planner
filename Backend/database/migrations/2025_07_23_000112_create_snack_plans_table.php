<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('snack_plans', function (Blueprint $table) {
            $table->id('snack_plan_id');
            $table->unsignedBigInteger('snack_date');
            $table->unsignedBigInteger('user_id');
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
            $table->softDeletes();  
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snack_plans');
    }
};
