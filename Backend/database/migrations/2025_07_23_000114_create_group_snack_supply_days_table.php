<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_snack_supply_days', function (Blueprint $table) {
            $table->id('group_snack_supply_day_id');
            $table->unsignedBigInteger('group_id');
            $table->date('supply_date');
            $table->unsignedBigInteger('set_by');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('group_id')->references('group_id')->on('groups')->onDelete('cascade');
            $table->foreign('set_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_snack_supply_days');
    }
};
