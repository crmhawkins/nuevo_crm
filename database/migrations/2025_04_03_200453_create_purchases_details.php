<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->string('KD');
            $table->string('marca', 22);
            $table->string('domicilio');
            $table->string('telefono');
            $table->string('email');
            $table->text('historia');
            $table->text('servicios');
            $table->text('redes')->nullable();
            $table->string('dominio');
            $table->timestamps();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_details');
    }
};
