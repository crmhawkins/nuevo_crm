<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('autoseo', function (Blueprint $table) {
            $table->json('json_storage')->nullable()->after('url');
            $table->json('reports')->nullable()->after('json_storage');
            $table->string('pin')->nullable()->after('reports');
        });
    }

    public function down()
    {
        Schema::table('autoseo', function (Blueprint $table) {
            $table->dropColumn(['json_storage', 'reports', 'pin']);
        });
    }
};
