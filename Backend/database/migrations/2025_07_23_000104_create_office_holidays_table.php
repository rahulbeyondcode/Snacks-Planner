<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_holidays', function (Blueprint $table) {
            $table->id('holiday_id');
            $table->unsignedBigInteger('user_id'); // references users.user_id (should have role 'account')
            $table->date('holiday_date');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_holidays');
    }
};
