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
        $statuses = [
            'Pendiente',
            'No cobrada',
            'Cobrada',
            'Cobrada parcialmente',
            'Cancelada',
        ];

        foreach ($statuses as $status) {
            DB::table('invoice_status')->insert([
                'name' => $status,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
