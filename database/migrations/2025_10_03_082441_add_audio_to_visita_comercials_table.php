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
            $table->string('audio_file')->nullable(); // Ruta del archivo de audio
            $table->integer('audio_duration')->nullable(); // Duración en segundos
            $table->timestamp('audio_recorded_at')->nullable(); // Fecha y hora de grabación
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visita_comercials', function (Blueprint $table) {
            $table->dropColumn(['audio_file', 'audio_duration', 'audio_recorded_at']);
        });
    }
};
