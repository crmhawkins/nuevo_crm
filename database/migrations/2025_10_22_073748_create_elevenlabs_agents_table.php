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
        Schema::create('elevenlabs_agents', function (Blueprint $table) {
            $table->id();
            $table->string('agent_id')->unique()->comment('ID del agente en Eleven Labs');
            $table->string('name')->comment('Nombre del agente');
            $table->boolean('archived')->default(false)->comment('Si el agente está archivado');
            $table->integer('last_call_time_unix_secs')->nullable()->comment('Timestamp de la última llamada');
            $table->json('metadata')->nullable()->comment('Datos adicionales del agente');
            $table->timestamps();
            
            $table->index('agent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevenlabs_agents');
    }
};
