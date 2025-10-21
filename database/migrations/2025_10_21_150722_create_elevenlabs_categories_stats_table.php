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
        Schema::create('elevenlabs_categories_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('Fecha de las estadísticas');
            $table->string('category')->comment('Categoría');
            $table->integer('count')->default(0)->comment('Cantidad de conversaciones');
            $table->decimal('percentage', 5, 2)->default(0)->comment('Porcentaje del total');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index('date');
            $table->index('category');
            $table->unique(['date', 'category']); // Una entrada por categoría por día
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevenlabs_categories_stats');
    }
};
