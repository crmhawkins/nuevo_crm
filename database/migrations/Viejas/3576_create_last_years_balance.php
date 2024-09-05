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
        Schema::create('last_years_balance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_id')->nullable();

            // $table->foreignId('bank_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->string('year')->nullable();
            $table->float('quantity',10,2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('last_years_balance');
    }
};
