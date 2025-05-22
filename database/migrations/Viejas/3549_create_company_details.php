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
        Schema::create('company_details', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('company_name');
            $table->string('nif')->unique();
            $table->text('address')->nullable();
            $table->text('bank_account_data')->nullable();
            $table->decimal('price_hour', 8, 2)->nullable();
            $table->string('paypal')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('pinterest')->nullable();
            $table->timestamps();
            $table->softDeletes();  // Esto añade la columna deleted_at para el soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_details');
    }
};
