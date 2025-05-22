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

        Schema::table('associated_expenses', function (Blueprint $table) {

            $table->text('documents')->nullable();
            
            $table->foreign('budget_id')->references('id')->on('budgets');
            $table->foreign('bank_id')->references('id')->on('bank_accounts');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_order');
            $table->foreign('payment_method_id')->references('id')->on('payment_method');
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
