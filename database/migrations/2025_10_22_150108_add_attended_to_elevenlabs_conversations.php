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
            $table->boolean('attended')->default(false)->after('processing_status')->comment('Si la conversación ha sido atendida/revisada');
            $table->timestamp('attended_at')->nullable()->after('attended')->comment('Fecha de atención');
            $table->unsignedBigInteger('attended_by')->nullable()->after('attended_at')->comment('ID del usuario que atendió');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->dropColumn(['attended', 'attended_at', 'attended_by']);
        });
    }
};
