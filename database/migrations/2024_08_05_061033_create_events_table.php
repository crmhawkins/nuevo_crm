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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_user_id')->unsigned();
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('color')->nullable();
            $table->timestamp('start');
            $table->timestamp('end')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
