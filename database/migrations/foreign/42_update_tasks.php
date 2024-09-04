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
        Schema::table('tasks', function (Blueprint $table) {

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
        //
    }
};
