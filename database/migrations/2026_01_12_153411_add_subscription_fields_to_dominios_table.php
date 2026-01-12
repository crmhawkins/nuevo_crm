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
            $table->string('stripe_subscription_id')->nullable()->after('stripe_payment_method_id');
            $table->string('stripe_plan_id')->nullable()->after('stripe_subscription_id');
            $table->unsignedBigInteger('factura_id')->nullable()->after('stripe_plan_id');
            
            $table->foreign('factura_id')->references('id')->on('invoices')->onDelete('set null');
            $table->index('stripe_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dominios', function (Blueprint $table) {
            $table->dropForeign(['factura_id']);
            $table->dropIndex(['stripe_subscription_id']);
            $table->dropColumn(['stripe_subscription_id', 'stripe_plan_id', 'factura_id']);
        });
    }
};
