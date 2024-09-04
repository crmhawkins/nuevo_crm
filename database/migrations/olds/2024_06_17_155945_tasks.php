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
            $table->foreignId('admin_user_id')->constrained('admin_user')->onDelete('cascade')->nullable();
            $table->foreignId('gestor_id')->constrained('admin_user')->onDelete('cascade')->nullable();
            $table->foreignId('priority_id')->constrained('priority')->onDelete('cascade')->nullable();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->nullable();
            $table->foreignId('budget_id')->constrained('budgets')->onDelete('cascade')->nullable();
            $table->foreignId('budget_concept_id')->constrained('budget_concepts')->onDelete('cascade')->nullable();
            $table->foreignId('task_status_id')->constrained('task_status')->onDelete('cascade')->nullable();
            $table->foreignId('split_master_task_id')->constrained('tasks')->onDelete('cascade')->nullable();

            $table->tinyInteger('duplicated')->nullable();
            $table->text('description')->nullable();
            $table->time('estimated_time')->nullable();
            $table->time('real_time')->nullable();
            $table->string('title')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
