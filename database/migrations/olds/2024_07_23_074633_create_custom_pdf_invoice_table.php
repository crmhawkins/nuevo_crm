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
        Schema::create('custom_pdf_invoice', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('head_string_1')->nullable();
            $table->string('head_string_2')->nullable();
            $table->string('head_string_3')->nullable();
            $table->string('nif')->nullable();
            $table->string('logo_image')->nullable();
            $table->string('footer_string_1')->nullable();
            $table->string('footer_string_2')->nullable();
            $table->text('terms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_pdf_invoice');
    }
};
