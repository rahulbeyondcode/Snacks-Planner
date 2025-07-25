<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_weekly_operation_details', function (Blueprint $table) {
            $table->id('group_weekly_operation_detail_id');
            $table->unsignedBigInteger('group_weekly_operation_id');
            $table->text('task_description')->nullable();
            $table->enum('status', ['pending', 'completed', 'in-progress'])->default('pending');
            $table->timestamps();
            $table->softDeletes();  
            $table->foreign('group_weekly_operation_id')->references('group_weekly_operation_id')->on('group_weekly_operations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_weekly_operation_details');
    }
};
