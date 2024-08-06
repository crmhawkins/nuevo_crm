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
            $table->unsignedBigInteger('admin_user_id');  // Relación con el modelo User
            $table->unsignedBigInteger('client_id');      // Relación con el modelo Client
            $table->text('note')->nullable();             // Campo para notas
            $table->boolean('finished')->default(false);  // Estado de finalización, por defecto es falso

            $table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

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
