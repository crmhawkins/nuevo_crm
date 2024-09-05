<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('diario_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gasto_id')->nullable()->constrained('gastos');
            $table->foreignId('ingreso_id')->nullable()->constrained('ingresos');
            $table->foreignId('cuenta_id')->nullable();
            $table->foreignId('formas_pago_id')->nullable()->constrained('payment_method');
            $table->string('asiento_contable')->nullable();
            $table->string('tipo', 254);
            $table->date('date')->nullable();
            $table->string('concepto', 254)->nullable();
            $table->double('debe')->nullable();
            $table->double('haber')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diario_caja');
    }
};
