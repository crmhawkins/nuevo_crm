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
            $table->decimal('discount_percentage', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_concepts', function (Blueprint $table) {
            $table->tinyInteger('discount_percentage')->nullable()->change();
        });
    }
};
