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
        Schema::create('gastos', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('title')->collation('utf8_unicode_ci')->nullable();
            $table->float('quantity',10,2)->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedInteger('bank_id')->nullable();
            $table->date('budget_date')->nullable();
            $table->date('date')->nullable();
 
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('bank_id')->references('id')->on('bank_accounts');
 
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
