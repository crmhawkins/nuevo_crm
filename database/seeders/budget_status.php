<?php

namespace Database\Seeders;

use App\Models\Budgets\BudgetStatu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class budget_status extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pendiente de confirmar'],
            ['name' => 'Pendiente de aceptar'],
            ['name' => 'Aceptado'],
            ['name' => 'Cancelado'],
            ['name' => 'Finalizado'],
            ['name' => 'Facturado'],
            ['name' => 'Facturado parcialmente'],
        ];

        foreach ($statuses as $status) {
            BudgetStatu::create($status);
        }
    }
}
