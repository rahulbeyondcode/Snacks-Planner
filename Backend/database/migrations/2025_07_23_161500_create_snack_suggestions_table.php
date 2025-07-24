<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('snack_suggestions', function (Blueprint $table) {
            $table->id('snack_suggestion_id');
            $table->unsignedBigInteger('user_id');
            $table->string('snack_name');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('snack_suggestions');
    }
};
