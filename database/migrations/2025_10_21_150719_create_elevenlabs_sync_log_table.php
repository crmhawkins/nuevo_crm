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
        Schema::create('elevenlabs_sync_log', function (Blueprint $table) {
            $table->id();
            $table->dateTime('sync_started_at')->comment('Inicio de sincronización');
            $table->dateTime('sync_finished_at')->nullable()->comment('Fin de sincronización');
            $table->integer('conversations_synced')->default(0)->comment('Total de conversaciones sincronizadas');
            $table->integer('conversations_new')->default(0)->comment('Conversaciones nuevas');
            $table->integer('conversations_updated')->default(0)->comment('Conversaciones actualizadas');
            $table->enum('status', ['running', 'completed', 'failed'])->default('running')->comment('Estado de la sincronización');
            $table->text('error_message')->nullable()->comment('Mensaje de error si falla');
            $table->timestamps();
            
            // Índice para búsquedas rápidas
            $table->index('sync_started_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevenlabs_sync_log');
    }
};
