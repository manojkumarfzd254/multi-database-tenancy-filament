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
        Schema::create('project_sprints', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('project_id');
            $table->string('sprint_title');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled', 'on_hold'])->nullable();
            $table->timestamps();
        });
    }
    /**
        *Planned: Before the sprint starts.
        *In Progress: When the sprint starts and the team is working on it.
        *Completed: When all tasks within the sprint are done and closed.
        *Cancelled: If the sprint is abandoned for any reason.
        *On Hold: When work is paused due to external factors.
    */
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_sprints');
    }
};
