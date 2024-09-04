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
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('budget_concept_id')->constrained('budget_concepts')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade')->nullable();;
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_method')->onDelete('cascade');
            $table->foreignId('bank_id')->constrained('bank_accounts')->onDelete('cascade')->nullable();;

            $table->integer('units');
            $table->float('amount',10,2)->nullable();
            $table->date('shipping_date');
            $table->text('note')->nullable();
            $table->boolean('sent')->default(0);
            $table->boolean('cancelled')->default(0);
            $table->integer('status')->default(0);

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
