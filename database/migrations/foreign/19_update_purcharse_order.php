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
        Schema::table('purchase_order', function (Blueprint $table) {


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
