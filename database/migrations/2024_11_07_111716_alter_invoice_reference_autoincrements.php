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
        Schema::table('invoice_reference_autoincrements', function (Blueprint $table) {
            $table->boolean('ceuta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_reference_autoincrements', function (Blueprint $table) {
            $table->dropColumn('ceuta');
        });
    }
};
