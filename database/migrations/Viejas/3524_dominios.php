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
        //dominios
        Schema::create('dominios', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('dominio')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->tinyInteger('estado')->nullable();
            $table->text('comentario')->nullable();

            // $table->foreign('client_id')->references('id')->on('clients');

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
