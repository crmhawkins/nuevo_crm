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
        Schema::create('unclassified_expenses', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('pdf_file_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('bank')->nullable();
            $table->string('iban')->nullable();
            $table->float('ammount',10,2)->nullable();
            $table->date('received_date')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('order_number')->nullable();
            $table->tinyInteger('accepted')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unclassified_expenses');
    }
};
