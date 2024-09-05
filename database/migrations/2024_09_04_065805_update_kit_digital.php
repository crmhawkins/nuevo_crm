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
        Schema::table('ayudas', function (Blueprint $table) {

            $table->foreignId('estado')->constrained('ayudas_estados_kit')->onDelete('cascade')->nullable();
            $table->foreignId('servicio_id')->constrained('ayudas_servicios')->onDelete('cascade')->nullable();
            $table->foreignId('cliente_id')->constrained('clients')->onDelete('cascade')->nullable();
            $table->foreignId('comercial_id')->constrained('admin_users')->onDelete('cascade')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
