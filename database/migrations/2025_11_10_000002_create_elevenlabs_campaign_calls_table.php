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
        Schema::create('elevenlabs_campaign_calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->string('uid')->unique();
            $table->unsignedInteger('client_id')->nullable()->index();
            $table->string('phone_number');
            $table->string('status')->default('pendiente');
            $table->string('sentiment_category')->nullable();
            $table->string('specific_category')->nullable();
            $table->decimal('confidence_score', 5, 4)->nullable();
            $table->text('summary')->nullable();
            $table->text('custom_prompt')->nullable();
            $table->json('metadata')->nullable();
            $table->string('eleven_conversation_id')->nullable();
            $table->unsignedBigInteger('eleven_conversation_internal_id')->nullable()->index();
            $table->unsignedInteger('managed_by')->nullable()->index();
            $table->timestamp('managed_at')->nullable();
            $table->timestamps();

            $table->foreign('campaign_id')
                ->references('id')
                ->on('elevenlabs_campaigns')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevenlabs_campaign_calls');
    }
};

