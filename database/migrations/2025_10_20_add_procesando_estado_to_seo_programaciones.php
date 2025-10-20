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
        // Modificar el ENUM para incluir 'procesando'
        DB::statement("ALTER TABLE seo_programaciones MODIFY COLUMN estado ENUM('pendiente', 'procesando', 'completado', 'error') DEFAULT 'pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir al ENUM original
        DB::statement("ALTER TABLE seo_programaciones MODIFY COLUMN estado ENUM('pendiente', 'completado', 'error') DEFAULT 'pendiente'");
    }
};

