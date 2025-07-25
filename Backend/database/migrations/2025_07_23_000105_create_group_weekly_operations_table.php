<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_weekly_operations', function (Blueprint $table) {
            $table->id('group_weekly_operation_id');
            $table->unsignedBigInteger('group_id');
            $table->date('week_start_date');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('assigned_by');
            $table->timestamps();
            $table->softDeletes();  
            $table->foreign('group_id')->references('group_id')->on('groups')->onDelete('cascade');
            $table->foreign('employee_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_weekly_operations');
    }
};
