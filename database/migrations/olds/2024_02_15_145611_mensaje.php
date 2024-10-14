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
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->string('id_mensaje')->nullable();
            $table->string('id_three')->nullable();
            $table->string('remitente')->nullable();
            $table->text('mensaje')->nullable();
            $table->text('respuesta')->nullable();
            $table->string('status_mensaje')->nullable();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->date('date')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
