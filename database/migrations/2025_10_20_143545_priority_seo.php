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
    Schema::table('seo_programaciones', function (Blueprint $table) {
        $table->integer('priority')->default(0)->after('estado');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_programaciones', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
