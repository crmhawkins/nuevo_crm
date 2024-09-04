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
        Schema::table('incidences', function (Blueprint $table) {
  

            $table->foreign('budget_id')->references('id')->on('budgets');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('gestor_id')->references('id')->on('admin_user');
            $table->foreign('admin_user_id')->references('id')->on('admin_user');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('status')->references('id')->on('incidences_status');

        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidences');
    }
};
