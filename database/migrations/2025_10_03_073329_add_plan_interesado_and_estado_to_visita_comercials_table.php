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
        Schema::table('visita_comercials', function (Blueprint $table) {
            $table->string('plan_interesado')->nullable(); // Plan que le interesa al cliente
            $table->decimal('precio_plan', 8, 2)->nullable(); // Precio del plan
            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado', 'en_proceso'])->default('pendiente');
            $table->text('observaciones_plan')->nullable(); // Observaciones sobre el plan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visita_comercials', function (Blueprint $table) {
            $table->dropColumn(['plan_interesado', 'precio_plan', 'estado', 'observaciones_plan']);
        });
    }
};
