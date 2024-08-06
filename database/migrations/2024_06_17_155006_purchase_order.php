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
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('budget_concept_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedInteger('bank_id')->nullable();

            $table->integer('units')->nullable();
            $table->double('amount',10,2)->nullable();
            $table->date('shipping_date')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('sent')->nullable();
            $table->tinyInteger('cancelled')->nullable();
            $table->integer('status')->nullable();


            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('budget_concept_id')->references('id')->on('budget_concepts');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('payment_method_id')->references('id')->on('payment_method');
            $table->foreign('bank_id')->references('id')->on('bank_accounts');

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
