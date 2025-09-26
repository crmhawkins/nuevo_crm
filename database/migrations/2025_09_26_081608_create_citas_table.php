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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->datetime('fecha_inicio');
            $table->datetime('fecha_fin');
            $table->string('color', 7)->default('#3b82f6'); // Color hexadecimal para el calendario
            $table->enum('estado', ['programada', 'confirmada', 'en_progreso', 'completada', 'cancelada'])->default('programada');
            $table->enum('tipo', ['reunion', 'llamada', 'visita', 'presentacion', 'seguimiento', 'otro'])->default('reunion');
            $table->string('ubicacion')->nullable();
            $table->text('notas_internas')->nullable(); // Notas que solo ven los gestores
            $table->text('recordatorios')->nullable(); // Recordatorios automáticos
            
            // Relaciones
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('gestor_id')->nullable();
            $table->unsignedBigInteger('creado_por');
            $table->unsignedBigInteger('actualizado_por')->nullable();
            
            // Campos de seguimiento
            $table->boolean('es_recurrente')->default(false);
            $table->string('patron_recurrencia')->nullable(); // daily, weekly, monthly, yearly
            $table->date('fecha_fin_recurrencia')->nullable();
            $table->json('configuracion_recurrencia')->nullable(); // Configuración específica de recurrencia
            
            // Campos de notificaciones
            $table->boolean('notificar_cliente')->default(false);
            $table->boolean('notificar_gestor')->default(true);
            $table->integer('minutos_recordatorio')->default(15); // Minutos antes de la cita
            
            // Campos de seguimiento post-cita
            $table->text('resultados')->nullable(); // Resultados de la cita
            $table->text('acciones_siguientes')->nullable(); // Acciones a realizar después
            $table->boolean('requiere_seguimiento')->default(false);
            $table->date('fecha_seguimiento')->nullable();
            
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index(['cliente_id', 'fecha_inicio']);
            $table->index(['gestor_id', 'fecha_inicio']);
            $table->index(['estado', 'fecha_inicio']);
        });

        // Foreign keys se añadirán en una migración separada
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};