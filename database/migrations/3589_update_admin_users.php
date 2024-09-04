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
        Schema::table('admin_user', function (Blueprint $table) {
           $table->foreignId('commercial_id')->nullable()->constrained('admin_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user', function (Blueprint $table) {
            $table->dropColumn('commercial_id');

        });
    }
};
