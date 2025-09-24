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
        Schema::table('dominios', function (Blueprint $table) {
            $table->decimal('precio_compra', 10, 2)->nullable()->after('estado_id');
            $table->decimal('precio_venta', 10, 2)->nullable()->after('precio_compra');
            $table->string('iban', 34)->nullable()->after('precio_venta');
            $table->boolean('sincronizado')->default(false)->after('iban');
            $table->timestamp('ultima_sincronizacion')->nullable()->after('sincronizado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dominios', function (Blueprint $table) {
            $table->dropColumn(['precio_compra', 'precio_venta', 'iban', 'sincronizado', 'ultima_sincronizacion']);
        });
    }
};
