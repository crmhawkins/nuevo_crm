<?php

namespace Database\Seeders;

use App\Models\Invoices\InvoiceStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class invoice_status extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pendiente'],
            ['name' => 'No cobrada'],
            ['name' => 'Cobrada'],
            ['name' => 'Cobrada parcialmente'],
            ['name' => 'Cancelada'],
        ];

        foreach ($statuses as $status) {
            InvoiceStatus::create($status);
        }
    }
}
