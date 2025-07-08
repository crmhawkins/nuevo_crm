<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('autoseo', function (Blueprint $table) {
            $table->dateTime('last_report')->nullable()->after('json_storage');
            $table->dateTime('first_report')->nullable()->after('last_report');
        });
    }

    public function down()
    {
        Schema::table('autoseo', function (Blueprint $table) {
            $table->dropColumn(['last_report', 'first_report']);
        });
    }
};
