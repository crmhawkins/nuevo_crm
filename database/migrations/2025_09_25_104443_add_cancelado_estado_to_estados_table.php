<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insertar el estado CANCELADO
        DB::table('estados_dominios')->insert([
            'name' => 'CANCELADO'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar el estado CANCELADO
        DB::table('estados_dominios')->where('name', 'CANCELADO')->delete();
    }
};
