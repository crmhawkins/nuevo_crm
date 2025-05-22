<?php

namespace Database\Seeders;

use App\Models\Users\UserPosition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class user_position extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['name' => 'CEO'],
            ['name' => 'Directora de cuentas'],
            ['name' => 'Ejecutivo de cuentas'],
            ['name' => 'Gestor - Contable'],
            ['name' => 'Empleado'],
            ['name' => 'Asesor Hawkins'],
        ];

        foreach ($positions as $position) {
            UserPosition::create($position);
        }
    }
}
