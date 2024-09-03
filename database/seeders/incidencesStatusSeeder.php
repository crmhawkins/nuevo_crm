<?php

namespace Database\Seeders;

use App\Models\Incidence\IncidenceStatus;
use Illuminate\Database\Seeder;

class incidencesStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pendiente'],
            ['name' => 'Resuelta'],
        ];

        foreach ($statuses as $status) {
            IncidenceStatus::create($status);
        }
    }
}
