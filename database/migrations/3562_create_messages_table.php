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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('todo_id')->nullable();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            
            // $table->foreignId('todo_id')->constrained('to-do')->onDelete('cascade');
            // $table->foreignId('admin_user_id')->constrained('admin_user')->onDelete('cascade');
            $table->longText('mensaje');
            $table->string('archivo')->nullable();
            $table->timestamps();
            $table->softDeletes();


        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
