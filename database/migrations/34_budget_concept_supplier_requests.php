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
        Schema::create('budget_concept_supplier_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_concept_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('mail')->nullable();
            $table->integer('option_number')->nullable();
            $table->double('price', 20, 2)->nullable();
            $table->tinyInteger('selected')->nullable();
            $table->tinyInteger('accepted')->nullable();
            $table->date('sent_date')->nullable();
            $table->date('accepted_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('budget_concept_id')->references('id')->on('budget_concepts');
            // $table->foreign('supplier_id')->references('id')->on('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_concept_supplier_requests');

    }
};
