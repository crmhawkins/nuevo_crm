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
        Schema::create('productividades_mensuales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained('admin_user')->onDelete('cascade'); // Cambiar "users" por "admin_user"
            $table->integer('mes'); // 1-12 para el mes
            $table->integer('año'); // Para el año

            $table->decimal('productividad', 5, 2); // Para almacenar el porcentaje de productividad con dos decimales
            $table->timestamps();
            $table->softDeletes(); // Para el campo deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productividades_mensuales');
    }
};
