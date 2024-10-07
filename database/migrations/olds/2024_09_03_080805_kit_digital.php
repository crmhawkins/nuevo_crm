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
        Schema::create('ayudas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estado');
            $table->unsignedBigInteger('servicio_id');
            $table->unsignedBigInteger('comercial_id');
            $table->unsignedBigInteger('cliente_id');
            $table->integer('mensaje_interpretado');
            $table->string('mensaje');
            $table->string('empresa');
            $table->string('segmento');
            $table->string('cliente');
            $table->string('expediente');
            $table->string('contratos');
            $table->string('gestor');
            $table->date('fecha_actualizacion');
            $table->string('importe');
            $table->tinyInteger('estado_factura');
            $table->date('banco');
            $table->date('fecha_acuerdo');
            $table->date('plazo_maximo_entrega');
            $table->string('contacto');
            $table->integer('empleados');
            $table->integer('interesado');
            $table->string('pendiente');
            $table->string('telefono');
            $table->string('nombre');
            $table->string('nif');
            $table->string('comunidad');
            $table->integer('cp');
            $table->string('cnae');
            $table->integer('anno_inicio');
            $table->string('email');
            $table->integer('pago_aprovi');
            $table->text('comentario');
            $table->text('nuevo_comentario');
            $table->text('nota');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
            
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
