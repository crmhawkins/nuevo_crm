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
        Schema::create('commercial_have_employee', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('commercial_id')->nullable();


            // $table->foreign('admin_user_id')->references('id')->on('admin_user');
            // $table->foreign('commercial_id')->references('id')->on('admin_user');

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
