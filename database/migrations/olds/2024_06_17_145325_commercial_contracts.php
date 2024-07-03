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
        Schema::create('commercial_contracts', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->text('address')->nullable();
            $table->text('nif')->nullable();
            $table->text('nationality')->nullable();
            $table->tinyInteger('consent')->nullable();
 
            $table->foreign('admin_user_id')->references('id')->on('admin_users');
 
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
