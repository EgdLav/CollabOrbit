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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('preview')->default('default.png');
            $table->json('files')->nullable();
            $table->foreignId('workspace_id')->constrained('workspaces');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('executor_id')->constrained('users');
            $table->date('due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
