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
        Schema::create('cliente_servicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('autoseo_id');
            $table->string('nombre_servicio');
            $table->boolean('principal')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->foreign('autoseo_id')->references('id')->on('autoseo')->onDelete('cascade');
            $table->index('autoseo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_servicios');
    }
};
