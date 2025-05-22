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
        Schema::table('crm_activities_meetings_comments', function (Blueprint $table) {

            $table->foreign('admin_user_id')->references('id')->on('admin_user');
            $table->foreign('meeting_id')->references('id')->on('crm_activities_meetings');

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
