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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->string('module'); // e.g., 'groups', 'users', 'snacks', 'reports'
            $table->string('action'); // e.g., 'create', 'read', 'update', 'delete', 'list'
            $table->string('resource')->nullable(); // e.g., 'all', 'own', 'team'
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Ensure unique combination of module, action, and resource
            $table->unique(['module', 'action', 'resource'], 'unique_permission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
}; 