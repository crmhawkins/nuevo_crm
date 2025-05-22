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
        Schema::create('newsletters_manual', function (Blueprint $table) {
            $table->id();            
            $table->text('clients_array_id')->nullable();
            $table->tinyInteger('category')->nullable();
            $table->dateTime('date_sent')->nullable();
            $table->text('first_title_newsletter')->nullable();
            $table->text('banner_description')->nullable();
            $table->text('second_title_newsletter')->nullable();
            $table->text('images_promo')->nullable();
            $table->text('urls')->nullable();

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
