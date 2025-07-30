<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('working_days', function (Blueprint $table) {
            $table->id('id');
            $table->json('working_days'); // e.g., ["monday","tuesday",...]
            $table->unsignedBigInteger('user_id'); // who set it
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_days');
    }
};
