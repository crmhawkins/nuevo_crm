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
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade')->nullable();
            $table->foreignId('budget_concept_id')->constrained('budget_concepts')->onDelete('cascade')->nullable();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade')->nullable();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->nullable();
            $table->foreignId('payment_method_id')->constrained('payment_method')->onDelete('cascade')->nullable();
            $table->foreignId('bank_id')->constrained('bank_accounts')->onDelete('cascade')->nullable();

            $table->integer('units')->nullable();
            $table->double('amount',10,2)->nullable();
            $table->date('shipping_date')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('sent')->nullable();
            $table->tinyInteger('cancelled')->nullable();
            $table->integer('status')->nullable();

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
