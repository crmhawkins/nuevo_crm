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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('services_categories_id')->nullable();
            $table->string('title')->nullable();
            $table->text('concept')->nullable();
            $table->double('price', 20, 2)->nullable();
            $table->tinyInteger('estado')->nullable();
            $table->integer('order')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('services_categories_id')->references('id')->on('services_categories');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
