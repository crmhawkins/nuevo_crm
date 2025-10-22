<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            // Renombrar category a sentiment_category (contento/descontento)
            $table->renameColumn('category', 'sentiment_category');
        });
        
        // Agregar nueva columna para categoría específica
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->string('specific_category', 100)->nullable()->after('sentiment_category')->comment('Categoría específica del agente');
            $table->index('specific_category');
        });
    }

    public function down(): void
    {
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->dropColumn('specific_category');
        });
        
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->renameColumn('sentiment_category', 'category');
        });
    }
};
