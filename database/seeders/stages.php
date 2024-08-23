<?php

namespace Database\Seeders;

use App\Models\Alerts\Stage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class stages extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $stages = [
            ['stage' => 'Peticion Creada'],
            ['stage' => 'Presupuesto Pendiente Confirmar'],
            ['stage' => ''],
        ];

        foreach ($stages as $stage) {
            Stage::create($stage);
        }
    }
}
