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
        Schema::create('dominio_notificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dominio_id');
            $table->unsignedInteger('client_id');
            $table->enum('tipo_notificacion', ['email', 'whatsapp']);
            $table->timestamp('fecha_envio');
            $table->enum('estado', ['enviado', 'fallido', 'pendiente'])->default('pendiente');
            $table->string('token_enlace')->nullable();
            $table->enum('metodo_pago_solicitado', ['iban', 'stripe', 'ambos'])->default('ambos');
            $table->date('fecha_caducidad');
            $table->text('error_mensaje')->nullable();
            $table->timestamps();

            $table->index(['dominio_id', 'client_id']);
            $table->index('fecha_envio');
        });

        // Crear foreign keys después de crear los índices
        Schema::table('dominio_notificaciones', function (Blueprint $table) {
            $table->foreign('dominio_id')->references('id')->on('dominios')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dominio_notificaciones');
    }
};
