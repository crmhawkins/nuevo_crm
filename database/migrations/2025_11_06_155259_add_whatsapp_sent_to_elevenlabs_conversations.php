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
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->boolean('whatsapp_incidencia_enviado')->default(false)->after('processed_at');
            $table->timestamp('whatsapp_incidencia_enviado_at')->nullable()->after('whatsapp_incidencia_enviado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_incidencia_enviado', 'whatsapp_incidencia_enviado_at']);
        });
    }
};
