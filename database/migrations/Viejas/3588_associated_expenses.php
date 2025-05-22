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

        Schema::create('associated_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_id')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();

            // $table->foreignId('budget_id')->nullable()->constrained('budgets')->onDelete('cascade');
            // $table->foreignId('bank_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            // $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_order')->onDelete('cascade');
            // $table->foreignId('payment_method_id')->nullable()->constrained('payment_method')->onDelete('cascade');
            $table->string('title')->collation('utf8_unicode_ci')->nullable();
            $table->float('quantity',10,2)->nullable();
            $table->date('received_date')->nullable();
            $table->date('date')->nullable();
            $table->string('reference')->nullable();
            $table->enum('state',['PAGADO','PENDIENTE']);
            $table->tinyInteger('aceptado_gestor')->nullable();
            $table->integer('aprobado')->nullable();


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
