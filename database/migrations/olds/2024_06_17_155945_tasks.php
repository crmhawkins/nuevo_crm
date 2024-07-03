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
            $table->increments('id')->unsigned();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('gestor_id')->nullable();
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('budget_id')->nullable();
            $table->unsignedBigInteger('budget_concept_id')->nullable();
            $table->unsignedBigInteger('task_status_id')->nullable();
            $table->unsignedBigInteger('split_master_task_id')->nullable();

            $table->tinyInteger('duplicated')->nullable();
            $table->text('description')->nullable();
            $table->time('estimated_time')->nullable();
            $table->time('real_time')->nullable();
            $table->string('title')->nullable();

            $table->foreign('admin_user_id')->references('id')->on('admin_users');
            $table->foreign('gestor_id')->references('id')->on('admin_users');
            $table->foreign('priority_id')->references('id')->on('priority');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('budget_id')->references('id')->on('budgets');
            $table->foreign('budget_concept_id')->references('id')->on('budget_concepts');
            $table->foreign('task_status_id')->references('id')->on('task_status');
            $table->foreign('split_master_task_id')->references('id')->on('tasks');
 
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
