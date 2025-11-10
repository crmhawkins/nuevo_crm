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
        Schema::create('elevenlabs_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name');
            $table->string('api_call_name');
            $table->string('agent_id');
            $table->string('agent_phone_number_id');
            $table->string('agent_phone_number')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->text('initial_prompt')->nullable();
            $table->json('recipients_overview')->nullable();
            $table->string('status')->default('pendiente');
            $table->string('external_batch_id')->nullable();
            $table->unsignedInteger('total_calls')->default(0);
            $table->unsignedInteger('completed_calls')->default(0);
            $table->timestamps();

            $table->foreign('created_by')
                ->references('id')
                ->on('admin_user')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevenlabs_campaigns');
    }
};

