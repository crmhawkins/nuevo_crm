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
        Schema::table('company_details', function (Blueprint $table) {
            $table->string('certificado')->nullable();
            $table->string('contrasena')->nullable();
            $table->string('province')->nullable();
            $table->string('town')->nullable();
            $table->string('postCode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn('certificado');
            $table->dropColumn('contrasena');
            $table->dropColumn('province');
            $table->dropColumn('town');
            $table->dropColumn('postCode');
        });
    }
};
