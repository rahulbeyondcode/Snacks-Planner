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
            $table->bigIncrements('snack_plan_id');
            $table->date('snack_date');
            $table->unsignedBigInteger('planned_by');
            $table->decimal('total_amount', 10, 2);         
            $table->timestamps();   
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
