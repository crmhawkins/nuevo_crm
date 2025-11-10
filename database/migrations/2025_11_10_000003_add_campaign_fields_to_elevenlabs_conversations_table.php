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
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_id')->nullable()->after('attended_by');
            $table->unsignedBigInteger('campaign_call_id')->nullable()->after('campaign_id');
            $table->text('campaign_initial_prompt')->nullable()->after('campaign_call_id');
            $table->index(['campaign_id', 'campaign_call_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elevenlabs_conversations', function (Blueprint $table) {
            $table->dropColumn(['campaign_id', 'campaign_call_id', 'campaign_initial_prompt']);
        });
    }
};

