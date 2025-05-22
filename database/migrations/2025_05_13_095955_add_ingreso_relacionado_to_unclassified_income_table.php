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
        Schema::table('unclassified_income', function (Blueprint $table) {
            $table->unsignedBigInteger('ingreso_relacionado')->nullable();
            $table->string('tabla_relacionada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unclassified_income', function (Blueprint $table) {
            $table->dropColumn('ingreso_relacionado');
            $table->dropColumn('tabla_relacionada');
        });
    }
};
