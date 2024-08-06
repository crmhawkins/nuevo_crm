<?php

namespace Database\Seeders;

use App\Models\PaymentMethods\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class payments_method extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Al contado'],
            ['name' => 'Domiciliado'],
            ['name' => '30 días vista'],
            ['name' => '60 días vista'],
            ['name' => '90 días vista'],
            ['name' => '120 días vista'],
            ['name' => '100% En entrega del trabajo'],
            ['name' => 'A convenir'],
            ['name' => '50% inicio 50% fin'],
            ['name' => 'Confirming'],
            ['name' => 'Pagaré'],
            ['name' => 'Transferencia bancaria'],
        ];

        foreach ($statuses as $status) {
            PaymentMethod::create($status);
        }
    }
}
