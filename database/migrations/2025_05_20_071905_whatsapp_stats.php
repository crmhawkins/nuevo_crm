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
        Schema::create('whatsapp_stats', function (Blueprint $table) {
            $table->id();
            $table->string('messages_sent');
            $table->string('messages_received');
            $table->string('messages_failed');
            $table->string('messages_read');
            $table->string('response_received');
            $table->string('accepted_campania');
            $table->string('rejected_campania');
            $table->string('sent_campania');
            $table->timestamps();
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
