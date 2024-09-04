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
            $table->foreignId('reference_autoincrement_id')->nullable()->constrained('budget_reference_autoincrements')->onDelete('cascade');
            $table->foreignId('admin_user_id')->constrained('admin_user')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_method')->onDelete('cascade');
            $table->foreignId('budget_status_id')->nullable()->constrained('budget_status')->onDelete('cascade');
            $table->string('concept')->nullable();
            $table->date('creation_date')->nullable();
            $table->text('description')->nullable();
            $table->double('gross', 20, 2)->nullable();
            $table->double('base', 20, 2)->nullable();
            $table->double('iva', 20, 2)->nullable();
            $table->double('iva_percentage', 5, 2)->nullable();
            $table->double('total', 20, 2)->nullable();
            $table->double('discount', 20, 2)->nullable();
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
