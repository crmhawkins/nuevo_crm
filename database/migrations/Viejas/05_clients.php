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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id');
            $table->integer('client_id')->nullable();
            $table->integer('commercial_id')->nullable();
            $table->integer('contact_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('activity')->nullable();
            $table->string('identifier')->nullable();
            $table->string('cif')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('country')->nullable();
            $table->string('industry')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('fax')->nullable();
            $table->string('phone')->nullable();
            $table->string('web')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
            $table->string('pinterest')->nullable();
            $table->tinyInteger('is_client');
            $table->tinyInteger('privacy_policy_accepted')->nullable();
            $table->tinyInteger('cookies_accepted')->nullable();
            $table->tinyInteger('newsletters_sending_accepted')->nullable();
            $table->text('notes')->nullable();
            $table->date('last_survey')->nullable();
            $table->date('last_newsletter')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('admin_user_id')->references('id')->on('admin_user');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');

    }
};
