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
        Schema::table('citas', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('gestor_id')->references('id')->on('admin_users')->onDelete('set null');
            $table->foreign('creado_por')->references('id')->on('admin_users')->onDelete('cascade');
            $table->foreign('actualizado_por')->references('id')->on('admin_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['gestor_id']);
            $table->dropForeign(['creado_por']);
            $table->dropForeign(['actualizado_por']);
        });
    }
};