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
        Schema::create('petitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained('admin_user')->onDelete('cascade');  // Relación con el modelo User
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');      // Relación con el modelo Client
            $table->text('note')->nullable();             // Campo para notas
            $table->boolean('finished')->default(false);  // Estado de finalización, por defecto es falso

            $table->timestamps();
            $table->softDeletes();  // Incorpora el campo deleted_at para soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petitions');
    }
};
