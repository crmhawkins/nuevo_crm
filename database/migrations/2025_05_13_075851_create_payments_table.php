<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('unclassified_income', function (Blueprint $table) {
            $table->id();
            $table->string('pdf_file_name')->nullable(); // Ruta al PDF
            $table->string('company_name')->nullable();
            $table->string('bank')->nullable();
            $table->string('iban')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('received_date')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('order_number')->nullable();
            $table->boolean('accepted')->default(false);
            $table->text('message')->nullable();
            $table->string('documents')->nullable(); // Ruta a otros documentos
            $table->tinyInteger('status')->default(0);
            $table->unsignedBigInteger('ingreso_relacionado')->nullable();
            $table->string('tabla_relacionada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unclassified_income');
    }
}
