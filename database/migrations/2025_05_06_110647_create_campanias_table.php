<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaniasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campanias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->longText('mensaje'); // Para texto enriquecido (HTML)
            $table->dateTime('fecha_lanzamiento')->nullable();
            $table->json('clientes')->nullable(); // Array de clientes
            $table->boolean('estado')->default(0); // 0 = inactiva, 1 = activa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campanias');
    }
}
