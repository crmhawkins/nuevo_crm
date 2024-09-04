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
        Schema::create('budget_concepts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_id')->nullable();
            $table->unsignedBigInteger('concept_type_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('services_category_id')->nullable();
            $table->string('title')->nullable();
            $table->text('concept')->nullable();
            $table->integer('units')->nullable();
            $table->double('purchase_price', 20, 2)->nullable();
            $table->double('benefit_margin', 20, 2)->nullable();
            $table->double('sale_price', 20, 2)->nullable();
            $table->double('discount', 5, 2)->nullable();
            $table->double('total', 20, 2)->nullable();
            $table->double('total_no_discount', 20, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('budget_id')->references('id')->on('budgets');
            // $table->foreign('concept_type_id')->references('id')->on('budget_concept_type');
            // $table->foreign('service_id')->references('id')->on('services');
            // $table->foreign('services_category_id')->references('id')->on('services_categories');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');

    }
};
