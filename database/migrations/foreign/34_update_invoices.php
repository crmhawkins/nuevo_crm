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
        Schema::table('invoices', function (Blueprint $table) {

            $table->foreign('budget_id')->references('id')->on('budgets');
            $table->foreign('admin_user_id')->references('id')->on('admin_user');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('payment_method_id')->references('id')->on('payment_method');
            $table->foreign('invoice_status_id')->references('id')->on('invoice_status');
            $table->foreign('invoice_status_id')->references('id')->on('invoice_status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
