<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('money_pool_blocks', function (Blueprint $table) {
            $table->id('block_id');
            $table->unsignedBigInteger('money_pool_id');
            $table->decimal('amount', 12, 2);
            $table->text('reason')->nullable();
            $table->date('block_date');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('money_pool_id')->references('money_pool_id')->on('money_pools')->onDelete('cascade');
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_pool_blocks');
    }
};
