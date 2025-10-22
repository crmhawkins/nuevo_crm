<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->datetime('scheduled_call_datetime')->nullable()->after('specific_category')->comment('Fecha y hora de llamada agendada');
            $table->text('scheduled_call_notes')->nullable()->after('scheduled_call_datetime')->comment('Notas adicionales de la cita agendada');
            $table->index('scheduled_call_datetime');
        });
    }

    public function down(): void
    {
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->dropColumn(['scheduled_call_datetime', 'scheduled_call_notes']);
        });
    }
};
