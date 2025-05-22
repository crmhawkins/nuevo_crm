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
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('newsletter_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->text('campaign')->nullable();
            $table->text('email')->nullable();
            $table->tinyInteger('sent')->nullable();
            $table->tinyInteger('open')->nullable();
            $table->integer('times_opened')->nullable();
            $table->timestamp('date_sent')->nullable();

            // $table->foreign('newsletter_id')->references('id')->on('newsletters_manual');
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
