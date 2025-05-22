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
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('gestor_id')->nullable();
            $table->unsignedBigInteger('priority_id')->nullable()->default(2);
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('budget_id');
            $table->unsignedBigInteger('budget_concept_id');
            $table->unsignedBigInteger('task_status_id')->default(1);
            $table->unsignedBigInteger('split_master_task_id')->nullable();
            $table->tinyInteger('duplicated')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->time('estimated_time')->nullable();
            $table->time('real_time')->nullable();
            $table->time('total_time_budget')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('admin_user_id')->references('id')->on('admin_user');
            $table->foreign('gestor_id')->references('id')->on('admin_user');
            $table->foreign('priority_id')->references('id')->on('priority');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('budget_id')->references('id')->on('budgets');
            $table->foreign('budget_concept_id')->references('id')->on('budget_concepts');
            $table->foreign('task_status_id')->references('id')->on('task_status');
            $table->foreign('split_master_task_id')->references('id')->on('tasks');
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
