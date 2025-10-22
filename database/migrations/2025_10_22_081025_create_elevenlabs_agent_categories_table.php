<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Primero agregar descripción a los agentes
        Schema::table('elevenlabs_agents', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name')->comment('Descripción de qué hace el agente');
            $table->json('custom_categories')->nullable()->after('description')->comment('Categorías personalizadas del agente');
        });

        // Crear tabla de categorías por agente
        Schema::create('elevenlabs_agent_categories', function (Blueprint $table) {
            $table->id();
            $table->string('agent_id')->comment('FK a elevenlabs_agents');
            $table->string('category_key')->comment('Clave de la categoría (ej: contento, problema_tecnico)');
            $table->string('category_label')->comment('Etiqueta visible');
            $table->text('category_description')->nullable()->comment('Descripción de cuándo usar esta categoría');
            $table->string('color')->default('#6B7280')->comment('Color hexadecimal');
            $table->string('icon')->default('fa-circle')->comment('Ícono FontAwesome');
            $table->boolean('is_default')->default(false)->comment('Si es categoría fija (contento, descontento, sin_respuesta)');
            $table->integer('order')->default(0)->comment('Orden de visualización');
            $table->timestamps();
            
            $table->foreign('agent_id')->references('agent_id')->on('elevenlabs_agents')->onDelete('cascade');
            $table->unique(['agent_id', 'category_key']);
            $table->index('agent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elevenlabs_agent_categories');
        
        Schema::table('elevenlabs_agents', function (Blueprint $table) {
            $table->dropColumn(['description', 'custom_categories']);
        });
    }
};
