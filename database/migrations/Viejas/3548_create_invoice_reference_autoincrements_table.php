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
        Schema::create('invoice_reference_autoincrements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reference_autoincrement');
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedInteger('year');
            $table->unsignedInteger('month_num');
            $table->string('month', 10);
            $table->string('month_full', 20);
            $table->unsignedInteger('day');
            $table->string('letter_months', 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_reference_autoincrements');
    }
};
