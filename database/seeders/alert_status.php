<?php

namespace Database\Seeders;

use App\Models\Alerts\AlertStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class alert_status extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['status' => 'Pendiente'],
            ['status' => 'Resuelta'],
        ];

        foreach ($statuses as $status) {
            AlertStatus::create($status);
        }
    }
}
