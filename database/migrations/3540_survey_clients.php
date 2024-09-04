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
        Schema::create('survey_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();

            $table->text('quest1')->nullable();
            $table->text('quest2')->nullable();
            $table->text('quest3')->nullable();
            $table->text('quest4')->nullable();
            $table->text('quest5')->nullable();
            $table->text('quest6')->nullable();
            $table->text('quest7')->nullable();
            $table->text('quest8')->nullable();
            $table->text('valoracion_final')->nullable();

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
