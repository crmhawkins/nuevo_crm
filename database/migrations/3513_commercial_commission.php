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
        Schema::create('commercial_commission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_level_id')->nullable();
            $table->foreignId('commercial_product_id')->nullable();
            $table->integer('concept_type')->nullable();

            $table->float('quantity',10,2)->nullable();
            $table->float('quantity_plus',10,2)->nullable();
            $table->float('quantity_propio',10,2)->nullable();
            $table->float('quantity_plus_propio',10,2)->nullable();

            // $table->foreign('commercial_level_id')->references('id')->on('commercial_level');
            // $table->foreign('commercial_product_id')->references('id')->on('commercial_products');

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
