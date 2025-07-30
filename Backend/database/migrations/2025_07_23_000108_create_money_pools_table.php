<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('money_pools', function (Blueprint $table) {
            $table->bigIncrements('money_pool_id');
            $table->unsignedBigInteger('money_pool_setting_id')->index('money_pool_setting_id');
            $table->decimal('total_collected_amount', 12, 2)->default(0)->comment('Monthly total collected amount from the employee');
            $table->decimal('employer_contribution', 12, 2)->default(0)->comment('Monthly employer contribution');
            $table->decimal('total_pool_amount', 12, 2)->default(0)->comment('Total of employee and employer contribution');
            $table->decimal('blocked_amount', 12, 2)->default(0);
            $table->decimal('total_available_amount', 12, 2)->default(0)->comment('total_pool_amount - blocked_amount');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_pools');
    }
};
