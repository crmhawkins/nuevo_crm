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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id');
            $table->unsignedBigInteger('civil_status_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('name')->nullable();
            $table->string('dni')->nullable();
            $table->string('email')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('sex')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('address')->nullable();
            $table->string('zipcode')->nullable();
            $table->text('academic_info')->nullable();
            $table->string('academic_title')->nullable();
            $table->string('work_activity')->nullable();
            $table->string('fax')->nullable();
            $table->string('phone')->nullable();
            $table->string('web')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
            $table->string('pinterest')->nullable();
            $table->tinyInteger('privacy_policy_accepted');
            $table->tinyInteger('cookies_accepted');
            $table->tinyInteger('newsletters_sending_accepted');
            $table->text('notes')->nullable();
            $table->date('last_survey')->nullable();
            $table->date('last_newsletter')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('admin_user_id')->references('id')->on('admin_user');
            // $table->foreign('civil_status_id')->references('id')->on('civil_status');
            // $table->foreign('client_id')->references('id')->on('clients');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');

    }
};
