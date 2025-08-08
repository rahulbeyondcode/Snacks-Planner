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
        Schema::table('snack_plans', function (Blueprint $table) {
            // Change snack_date from unsignedBigInteger to date
            $table->date('snack_date')->change();           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snack_plans', function (Blueprint $table) {
            // Revert back to unsignedBigInteger
            $table->unsignedBigInteger('snack_date')->change();          
        });
    }
}; 