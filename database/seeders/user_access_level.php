<?php

namespace Database\Seeders;

use App\Models\Users\UserAccessLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class user_access_level extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accesslevels = [
            ['name' => 'Full Administrator'],
            ['name' => 'Gerente'],
            ['name' => 'Contable'],
            ['name' => 'Gestor'],
            ['name' => 'Personal'],
            ['name' => 'Comercial'],
        ];

        foreach ($accesslevels as $level) {
            UserAccessLevel::create($level);
        }
    }
}
