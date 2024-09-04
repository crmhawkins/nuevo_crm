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
        Schema::table('to-do', function (Blueprint $table) {

            $table->foreign('admin_user_id')->references('id')->on('admin_user');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('budget_id')->references('id')->on('budgets');
            $table->foreign('task_id')->references('id')->on('tasks');


        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('to-do');
    }
};
