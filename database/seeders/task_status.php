<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tasks\TaskStatus;

class task_status extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Reanudada'],
            ['name' => 'Pausada'],
            ['name' => 'Finalizada'],
            ['name' => 'Cancelada'],
            ['name' => 'Revisión'],
        ];

        foreach ($statuses as $status) {
            TaskStatus::create($status);
        }
    }
}
