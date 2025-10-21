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
        Schema::create('elevenlabs_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id')->unique()->comment('ID de Eleven Labs');
            $table->unsignedBigInteger('client_id')->nullable()->comment('FK a tabla clients');
            $table->dateTime('conversation_date')->comment('Fecha y hora de la conversación');
            $table->integer('duration_seconds')->default(0)->comment('Duración en segundos');
            $table->longText('transcript')->nullable()->comment('Transcripción completa');
            $table->enum('category', ['contento', 'descontento', 'pregunta', 'necesita_asistencia', 'queja', 'baja'])->nullable()->comment('Categoría de la conversación');
            $table->decimal('confidence_score', 5, 4)->nullable()->comment('Nivel de confianza de la categorización');
            $table->text('summary_es')->nullable()->comment('Resumen en español');
            $table->json('metadata')->nullable()->comment('Datos adicionales de Eleven Labs');
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->comment('Estado de procesamiento');
            $table->dateTime('processed_at')->nullable()->comment('Fecha de procesamiento');
            $table->timestamps();
            
            // Índices para optimizar búsquedas
            $table->index('conversation_date');
            $table->index('category');
            $table->index('processing_status');
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevenlabs_conversations');
    }
};
