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
        Schema::create('plataforma_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('mensaje');
            $table->string('tipo_contenido');
            $table->string('contenido');
            $table->text('botones');
            $table->string('status')->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->string('template_id')->nullable();
            $table->string('category')->nullable();
            $table->string('language')->default('es');
            $table->string('namespace')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plataforma_templates');
    }
};
