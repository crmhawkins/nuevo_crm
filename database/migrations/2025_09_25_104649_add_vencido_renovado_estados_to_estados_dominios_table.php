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
        // Insertar los nuevos estados
        DB::table('estados_dominios')->insert([
            ['name' => 'Vencido'],
            ['name' => 'Renovado']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar los estados agregados
        DB::table('estados_dominios')->whereIn('name', ['Vencido', 'Renovado'])->delete();
    }
};
