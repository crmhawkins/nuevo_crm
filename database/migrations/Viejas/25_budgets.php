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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();

            $table->unsignedBigInteger('reference_autoincrement_id')->nullable();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedBigInteger('budget_status_id')->nullable();
            
            $table->string('concept')->nullable();
            $table->date('creation_date')->nullable();
            $table->text('description')->nullable();
            $table->double('gross', 20, 2)->nullable();
            $table->double('base', 20, 2)->nullable();
            $table->double('iva', 20, 2)->nullable();
            $table->double('iva_percentage', 5, 2)->nullable();
            $table->double('total', 20, 2)->nullable();
            $table->double('discount', 20, 2)->nullable();
            $table->double('beneficio', 20, 2)->nullable();
            $table->tinyInteger('temp');
            $table->date('expiration_date')->nullable();
            $table->date('accepted_date')->nullable();
            $table->date('cancelled_date')->nullable();
            $table->text('note')->nullable();
            $table->double('billed_in_advance', 20, 2)->nullable();
            $table->double('retention_percentage', 5, 2)->nullable();
            $table->double('total_retention', 20, 2)->nullable();
            $table->string('invoiced_advance')->nullable();
            $table->integer('commercial_id')->nullable();
            $table->integer('level_commission')->nullable();
            $table->integer('duracion')->nullable();
            $table->integer('cuotas_mensuales')->nullable();
            $table->integer('order_column')->nullable();
            $table->timestamps();
            $table->softDeletes();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');

    }
};
