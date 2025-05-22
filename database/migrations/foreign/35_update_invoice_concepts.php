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
        Schema::table('invoice_concepts', function (Blueprint $table) {

            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('concept_type_id')->references('id')->on('budget_concept_type');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('services_category_id')->references('id')->on('services_categories');

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
