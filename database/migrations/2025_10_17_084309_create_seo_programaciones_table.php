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
        Schema::create('seo_programaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('autoseo_id');
            $table->date('fecha_programada');
            $table->enum('estado', ['pendiente', 'completado', 'error'])->default('pendiente');
            $table->timestamps();

            $table->foreign('autoseo_id')->references('id')->on('autoseo')->onDelete('cascade');
            $table->index(['fecha_programada', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_programaciones');
    }
};
