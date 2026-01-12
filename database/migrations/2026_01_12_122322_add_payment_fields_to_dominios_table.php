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
            $table->enum('metodo_pago_preferido', ['iban', 'stripe'])->nullable()->after('iban');
            $table->string('stripe_payment_method_id')->nullable()->after('metodo_pago_preferido');
            $table->boolean('iban_validado')->default(false)->after('stripe_payment_method_id');
            $table->timestamp('ultima_notificacion_caducidad')->nullable()->after('iban_validado');
            $table->integer('dias_antes_notificar')->default(30)->after('ultima_notificacion_caducidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dominios', function (Blueprint $table) {
            $table->dropColumn([
                'metodo_pago_preferido',
                'stripe_payment_method_id',
                'iban_validado',
                'ultima_notificacion_caducidad',
                'dias_antes_notificar'
            ]);
        });
    }
};
