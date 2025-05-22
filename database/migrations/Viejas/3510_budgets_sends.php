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
        Schema::create('budgets_sends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('budget_id')->nullable();
            $table->unsignedBigInteger('budget_reference')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->text('file_name')->nullable();
            $table->tinyInteger('acceptance_conds')->nullable();
            $table->dateTime('date_send')->nullable();
            $table->string('IP')->nullable();
            $table->tinyInteger('file_delete')->nullable();
            $table->text('emails')->nullable();

            // $table->foreign('admin_user_id')->references('id')->on('admin_user');
            // $table->foreign('budget_id')->references('id')->on('budgets');
            // $table->foreign('budget_reference')->references('id')->on('budgets_reference');
            // $table->foreign('client_id')->references('id')->on('clients');

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
