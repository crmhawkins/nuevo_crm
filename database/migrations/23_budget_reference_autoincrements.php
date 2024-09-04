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
        Schema::create('budget_reference_autoincrements', function (Blueprint $table) {
            $table->id();
            $table->integer('reference_autoincrement')->nullable();
            $table->string('year')->nullable();
            $table->string('month_num')->nullable();
            $table->string('month')->nullable();
            $table->string('month_full')->nullable();
            $table->string('day')->nullable();
            $table->string('letter_months')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_reference_autoincrements');

    }
};
