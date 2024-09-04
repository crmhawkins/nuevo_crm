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
        Schema::table('commercial_commission', function (Blueprint $table) {

            $table->foreign('commercial_level_id')->references('id')->on('commercial_level');
            $table->foreign('commercial_product_id')->references('id')->on('commercial_products');

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
