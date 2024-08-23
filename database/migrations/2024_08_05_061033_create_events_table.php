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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained('admin_users')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade')->nullable();
            $table->foreignId('project_id')->constrained('budgets')->onDelete('cascade')->nullable();
            $table->foreignId('budget_id')->constrained('projects')->onDelete('cascade')->nullable();
            $table->string('title');
            $table->string('descripcion')->nullable();
            $table->string('color')->nullable();
            $table->timestamp('start');
            $table->timestamp('end')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
