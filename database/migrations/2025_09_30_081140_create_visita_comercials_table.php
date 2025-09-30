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
        Schema::create('visita_comercials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comercial_id'); // ID del comercial que hace la visita
            $table->unsignedBigInteger('cliente_id')->nullable(); // ID del cliente (si es existente)
            $table->string('nombre_cliente')->nullable(); // Nombre del cliente (si es nuevo)
            $table->enum('tipo_visita', ['presencial', 'telefonico']); // Tipo de visita
            $table->integer('valoracion'); // Valoración de 1 a 10
            $table->text('comentarios')->nullable(); // Comentarios de la visita
            $table->boolean('requiere_seguimiento')->default(false); // Si requiere seguimiento
            $table->datetime('fecha_seguimiento')->nullable(); // Fecha del próximo seguimiento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visita_comercials');
    }
};
