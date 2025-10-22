<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar ENUM a VARCHAR para soportar categorías dinámicas por agente
        DB::statement("ALTER TABLE elevenlabs_conversations MODIFY COLUMN category VARCHAR(100) NULL COMMENT 'Categoría de la conversación (dinámica por agente)'");
    }

    public function down(): void
    {
        // Volver a ENUM con las categorías originales
        DB::statement("ALTER TABLE elevenlabs_conversations MODIFY COLUMN category ENUM('contento', 'descontento', 'pregunta', 'necesita_asistencia', 'queja', 'baja', 'sin_respuesta') NULL COMMENT 'Categoría de la conversación'");
    }
};
