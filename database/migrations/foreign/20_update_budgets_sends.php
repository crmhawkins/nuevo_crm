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
        Schema::table('budgets_sends', function (Blueprint $table) {

            $table->foreign('admin_user_id')->references('id')->on('admin_user');
            $table->foreign('budget_id')->references('id')->on('budgets');
            $table->foreign('budget_reference')->references('id')->on('budgets_reference');
            $table->foreign('client_id')->references('id')->on('clients');

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
