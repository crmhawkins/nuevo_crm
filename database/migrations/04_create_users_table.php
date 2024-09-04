<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('access_level_id');
            $table->unsignedBigInteger('admin_user_department_id');
            $table->unsignedBigInteger('admin_user_position_id');
            $table->integer('commercial_id')->nullable();
            
            $table->string('name');
            $table->string('surname')->nullable();
            $table->string('username');
            $table->string('password');
            $table->string('role');
            $table->string('image')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('device_token')->nullable();
            $table->integer('seniority_years')->nullable();
            $table->integer('seniority_months')->nullable();
            $table->double('holidays_days', 3, 1)->nullable();
            $table->tinyInteger('inactive');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('access_level_id')->references('id')->on('admin_user_access_level');
            // $table->foreign('admin_user_department_id')->references('id')->on('admin_user_department');
            // $table->foreign('admin_user_position_id')->references('id')->on('admin_user_position');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
