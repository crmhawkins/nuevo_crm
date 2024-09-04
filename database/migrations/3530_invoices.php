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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 191)->nullable();
            $table->unsignedBigInteger('budget_id')->nullable();
            $table->unsignedBigInteger('reference_autoincrement_id')->nullable();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedBigInteger('invoice_status_id')->default(1);

            $table->string('concept', 191)->nullable();
            $table->text('description')->nullable();
            $table->double('gross', 10, 2)->nullable();
            $table->double('base', 10, 2)->nullable();
            $table->double('iva', 10, 2)->nullable();
            $table->double('iva_percentage', 5, 2)->nullable();
            $table->double('discount', 10, 2)->nullable();
            $table->boolean('discount_percentage')->default(0);
            $table->double('total', 10, 2)->nullable();
            $table->date('paid_date')->nullable();
            $table->double('paid_amount', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('billed_in_advance')->default(0);
            $table->date('expiration_date')->nullable();
            $table->date('seen_date')->nullable();
            $table->date('cancelled_date')->nullable();
            $table->boolean('partial')->default(0);
            $table->boolean('rectification')->default(0);
            $table->integer('partial_number')->nullable();
            $table->integer('show_summary')->default(1);
            $table->integer('empresa_factura_id')->default(1);

            // $table->foreign('budget_id')->references('id')->on('budgets');
            // $table->foreign('admin_user_id')->references('id')->on('admin_user');
            // $table->foreign('client_id')->references('id')->on('clients');
            // $table->foreign('project_id')->references('id')->on('projects');
            // $table->foreign('payment_method_id')->references('id')->on('payment_method');
            // $table->foreign('invoice_status_id')->references('id')->on('invoice_status');

            $table->timestamps();
            $table->softDeletes();
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
