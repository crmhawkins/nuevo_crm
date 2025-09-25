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
        Schema::table('dominios', function (Blueprint $table) {
            $table->timestamp('fecha_registro_calculada')->nullable()->after('fecha_renovacion_ionos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dominios', function (Blueprint $table) {
            $table->dropColumn('fecha_registro_calculada');
        });
    }
};
