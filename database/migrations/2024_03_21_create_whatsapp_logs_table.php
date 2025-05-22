<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('Tipo de mensaje o acción');
            $table->json('clients')->comment('Array de IDs de clientes o información de clientes');
            $table->text('message')->comment('Mensaje enviado o recibido');
            $table->text('response')->nullable()->comment('Respuesta recibida o estado de la operación');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
