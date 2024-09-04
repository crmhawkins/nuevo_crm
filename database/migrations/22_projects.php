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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('admin_user_id')->references('id')->on('admin_user');
            // $table->foreign('priority_id')->references('id')->on('priority');
            // $table->foreign('client_id')->references('id')->on('clients');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');

    }
};
