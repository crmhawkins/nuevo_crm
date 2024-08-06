<?php

namespace Database\Seeders;

use App\Models\Users\UserDepartament;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class user_department extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departaments = [
            ['name' => 'Administración'],
            ['name' => 'Programación'],
            ['name' => 'Diseño gráfico'],
            ['name' => 'Marketing online'],
            ['name' => 'Diseño 3D'],
            ['name' => 'Comerciales - Ventas'],
            ['name' => 'Directivos - Managers'],
            ['name' => 'Red Ventas'],
        ];

        foreach ($departaments as $departament) {
            UserDepartament::create($departament);
        }
    }
}
