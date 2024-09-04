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
            
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('budget_concept_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();

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
