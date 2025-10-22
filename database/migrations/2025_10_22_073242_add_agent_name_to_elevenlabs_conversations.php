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
            $table->string('agent_id')->nullable()->after('conversation_id')->comment('ID del agente de Eleven Labs');
            $table->string('agent_name')->nullable()->after('agent_id')->comment('Nombre del agente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->dropColumn(['agent_id', 'agent_name']);
        });
    }
};
