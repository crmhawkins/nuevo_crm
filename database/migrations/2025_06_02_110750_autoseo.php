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
        Schema::create('autoseo', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_email');
            $table->string('json_home');
            $table->string('json_nosotros');
            $table->string('url');
            $table->string('last_seo');
            $table->string('company_name')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('locality')->nullable();
            $table->string('admin_district')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_region', 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autoseo');
    }
};
