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
        Schema::table('ingresos', function (Blueprint $table) {
   
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('bank_id')->references('id')->on('bank_accounts');
            $table->foreign('categoria_id')->references('id')->on('categoria_ingresos');
            $table->foreign('estado_id')->references('id')->on('estados_ingresos');

            // $table->foreignId('categoria_id')->nullable()->constrained('categoria_ingresos')->onDelete('cascade');
            // $table->foreignId('estado_id')->nullable()->constrained('estados_ingresos')->onDelete('cascade');


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
