<?php

namespace Database\Seeders;

use App\Models\Holidays\HolidaysStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class holiday_status extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Aceptadas'],
            ['name' => 'Denegadas'],
            ['name' => 'Pendientes'],
        ];

        foreach ($statuses as $status) {
            HolidaysStatus::create($status);
        }
    }
}
