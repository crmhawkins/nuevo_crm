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
        Schema::table('admin_user', function (Blueprint $table) {
            $table->boolean('is_dark')->default(false);
            
            $table->foreign('access_level_id')->references('id')->on('admin_user_access_level');
            $table->foreign('admin_user_department_id')->references('id')->on('admin_user_department');
            $table->foreign('admin_user_position_id')->references('id')->on('admin_user_position');


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
