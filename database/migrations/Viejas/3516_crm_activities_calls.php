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
        Schema::create('crm_activities_calls', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->tinyInteger('fail')->nullable();
            $table->string('subject')->nullable();
            $table->date('date')->nullable();
            $table->text('description')->nullable();

            // $table->foreign('admin_user_id')->references('id')->on('admin_user');
            // $table->foreign('client_id')->references('id')->on('clients');

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
