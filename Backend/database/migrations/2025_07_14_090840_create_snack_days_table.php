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
        Schema::create('snack_days', function (Blueprint $table) {
    $table->id();
    $table->date('date');
    $table->boolean('is_holiday')->default(false);
    $table->unsignedBigInteger('planned_by')->nullable();
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('team_assignment_id');
    $table->timestamps();

    $table->foreign('planned_by')->references('id')->on('users')->onDelete('set null');
    $table->foreign('team_assignment_id')->references('id')->on('team_assignments')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snack_days');
    }
};
