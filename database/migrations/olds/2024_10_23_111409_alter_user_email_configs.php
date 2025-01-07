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
        Schema::table('user_email_configs', function (Blueprint $table) {
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_email_configs', function (Blueprint $table) {
            $table->dropColumn('smtp_host');
            $table->dropColumn('smtp_port');
        });
    }
};
