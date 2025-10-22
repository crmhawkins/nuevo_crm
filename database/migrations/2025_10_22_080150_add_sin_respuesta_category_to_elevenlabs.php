<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE elevenlabs_conversations MODIFY COLUMN category ENUM('contento', 'descontento', 'pregunta', 'necesita_asistencia', 'queja', 'baja', 'sin_respuesta') NULL COMMENT 'Categoría de la conversación'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE elevenlabs_conversations MODIFY COLUMN category ENUM('contento', 'descontento', 'pregunta', 'necesita_asistencia', 'queja', 'baja') NULL COMMENT 'Categoría de la conversación'");
    }
};
