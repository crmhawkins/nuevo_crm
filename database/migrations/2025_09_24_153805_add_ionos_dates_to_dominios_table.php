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
            $table->timestamp('fecha_activacion_ionos')->nullable()->after('ultima_sincronizacion');
            $table->timestamp('fecha_renovacion_ionos')->nullable()->after('fecha_activacion_ionos');
            $table->boolean('sincronizado_ionos')->default(false)->after('fecha_renovacion_ionos');
            $table->timestamp('ultima_sincronizacion_ionos')->nullable()->after('sincronizado_ionos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dominios', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_activacion_ionos',
                'fecha_renovacion_ionos', 
                'sincronizado_ionos',
                'ultima_sincronizacion_ionos'
            ]);
        });
    }
};
