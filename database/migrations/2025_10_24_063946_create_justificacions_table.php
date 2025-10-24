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
        Schema::create('justificacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id');
            $table->string('nombre_justificacion'); // Ej: "Segunda Justificacion Presencia Basica"
            $table->string('tipo_justificacion'); // Tipo de justificación seleccionado en el modal
            $table->text('archivos'); // JSON con las rutas de todos los archivos subidos
            $table->text('metadata')->nullable(); // JSON para almacenar campos adicionales como URL u otros inputs
            $table->timestamps();
            
            $table->foreign('admin_user_id')->references('id')->on('admin_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('justificacions');
    }
};
