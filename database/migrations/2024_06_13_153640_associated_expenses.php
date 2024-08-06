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

        Schema::create('associated_expenses', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedBigInteger('budget_id')->nullable();
            $table->unsignedInteger('bank_id')->nullable();
            $table->unsignedInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('title')->collation('utf8_unicode_ci')->nullable();
            $table->float('quantity',10,2)->nullable();
            $table->date('received_date')->nullable();
            $table->date('date')->nullable();
            $table->string('reference')->nullable();
            $table->enum('state',['PAGADO','PENDIENTE']);
            $table->tinyInteger('aceptado_gestor')->nullable();

            $table->foreign('budget_id')->references('id')->on('budgets');
            $table->foreign('bank_id')->references('id')->on('bank_accounts');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_order');
            $table->foreign('payment_method_id')->references('id')->on('payment_method');

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
