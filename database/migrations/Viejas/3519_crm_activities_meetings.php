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
        Schema::create('crm_activities_meetings', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('contact_by_id')->nullable();
            $table->string('subject')->nullable();
            $table->tinyInteger('done')->nullable();
            $table->date('date')->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->text('files')->nullable();

            // $table->foreign('admin_user_id')->references('id')->on('admin_user');
            // $table->foreign('client_id')->references('id')->on('clients');
            // $table->foreign('contact_by_id')->references('id')->on('contact_by');

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
