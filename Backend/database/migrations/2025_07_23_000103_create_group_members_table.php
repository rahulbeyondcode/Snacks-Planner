<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->id('group_member_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('user_id');          
            $table->timestamp('joined_at')->useCurrent();
            $table->softDeletes();  
            $table->foreign('group_id')->references('group_id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
