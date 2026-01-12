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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('token_verificacion_dominios')->nullable()->unique()->after('stripe_customer_id');
            $table->timestamp('token_verificacion_expires_at')->nullable()->after('token_verificacion_dominios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['token_verificacion_dominios', 'token_verificacion_expires_at']);
        });
    }
};
