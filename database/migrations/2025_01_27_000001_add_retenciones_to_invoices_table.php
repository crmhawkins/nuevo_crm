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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('retenciones_porcentaje', 5, 2)->nullable()->after('iva_percentage');
            $table->decimal('retenciones_valor', 10, 2)->nullable()->after('retenciones_porcentaje');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['retenciones_porcentaje', 'retenciones_valor']);
        });
    }
};
