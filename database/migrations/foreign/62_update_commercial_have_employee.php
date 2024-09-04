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
        Schema::table('commercial_have_employee', function (Blueprint $table) {
    

            $table->foreign('admin_user_id')->references('id')->on('admin_user');
            $table->foreign('commercial_id')->references('id')->on('admin_user');

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
