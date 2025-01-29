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
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('project_sprints_id');
            $table->string('task_title',500);
            $table->text('task_desc')->nullable();
            $table->json('task_checklist')->nullable();
            $table->string('task_attachment')->nullable();
            $table->enum('task_label', ['bug', 'improvement', 'featured'])->default('featured');
            $table->enum('task_priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled','on_hold'])->default('pending');
            $table->mediumInteger('estimated_hours')->comment('time is in minutes')->nullable();
            $table->unsignedBigInteger('parent_task_id')->nullable();
            $table->date('due_date')->nullable();
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
