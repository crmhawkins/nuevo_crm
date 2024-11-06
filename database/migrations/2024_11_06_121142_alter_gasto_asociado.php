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
        Schema::table('associated_expenses', function (Blueprint $table) {
            $table->date('date_aceptado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('associated_expenses', function (Blueprint $table) {
            $table->dropColumn('date_aceptado');
        });
    }
};
