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
            $table->increments('id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('budget_concept_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->unsignedInteger('bank_id')->nullable();

            $table->integer('units');
            $table->float('amount',10,2)->nullable();
            $table->date('shipping_date');
            $table->text('note')->nullable();
            $table->boolean('sent')->default(0);
            $table->boolean('cancelled')->default(0);
            $table->integer('status')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('budget_concept_id')->references('id')->on('budget_concepts');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('payment_method_id')->references('id')->on('payment_method');
            $table->foreign('bank_id')->references('id')->on('bank_accounts');
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
