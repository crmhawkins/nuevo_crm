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
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('budget_concept_id')->nullable()->constrained('budget_concepts')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_method')->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');

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
