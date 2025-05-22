<?php

namespace Database\Seeders;

use App\Models\Plataforma\WhatsappLog;
use Database\Factories\Plataforma\WhatsappLogFactory;
use Illuminate\Database\Seeder;

class WhatsappLogSeeder extends Seeder
{
    public function run()
    {
        // Crear 50 logs aleatorios
        WhatsappLog::factory()->count(50)->create();

        // Crear 20 logs de tipo mensaje
        WhatsappLog::factory()->count(20)->message()->create();

        // Crear 10 logs de tipo campaña
        WhatsappLog::factory()->count(10)->campaign()->create();

        // Crear 5 logs de tipo error
        WhatsappLog::factory()->count(5)->error()->create();
    }
}
