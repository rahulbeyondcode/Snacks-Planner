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
        Schema::table('office_holidays', function (Blueprint $table) {
            $table->enum('type', ['office_holiday', 'no_snacks_day'])->default('office_holiday')->after('user_id');
            $table->unsignedBigInteger('group_id')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_holidays', function (Blueprint $table) {
            $table->dropColumn(['type', 'group_id']);
        });
    }
};