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
            $table->foreignId('admin_user_id')->nullable()->constrained('admin_user')->onDelete('cascade');
            $table->foreignId('gestor_id')->nullable()->constrained('admin_user')->onDelete('cascade');
            $table->foreignId('priority_id')->nullable()->constrained('priority')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('budget_id')->nullable()->constrained('budgets')->onDelete('cascade');
            $table->foreignId('budget_concept_id')->nullable()->constrained('budget_concepts')->onDelete('cascade');
            $table->foreignId('task_status_id')->nullable()->constrained('task_status')->onDelete('cascade');
            $table->foreignId('split_master_task_id')->nullable()->constrained('tasks')->onDelete('cascade');

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
