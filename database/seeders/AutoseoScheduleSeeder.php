<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\SeoProgramacion;
use App\Models\Autoseo\ClienteServicio;
use Carbon\Carbon;

class AutoseoScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar un cliente autoseo existente o crear uno de prueba
        $autoseo = Autoseo::first();

        if (!$autoseo) {
            // Si no hay ninguno, crear uno de ejemplo
            $autoseo = Autoseo::create([
                'client_name' => 'Cliente de Prueba SEO',
                'client_email' => 'cliente@ejemplo.com',
                'url' => 'https://ejemplo.com',
                'username' => 'admin',
                'password' => 'password123',
                'user_app' => 'app_user',
                'password_app' => 'app_pass',
                'pin' => '1234',
                'company_context' => 'Empresa de servicios profesionales especializada en consultoría y desarrollo tecnológico.',
                'Locality' => 'Madrid',
                'AdminDistrict' => 'Comunidad de Madrid',
            ]);

            $this->command->info("✅ Cliente autoseo de prueba creado: ID {$autoseo->id}");
        }

        // Crear programaciones para hoy
        $today = Carbon::today();
        
        // Verificar si ya existe una programación para hoy
        $existingProgramacion = SeoProgramacion::where('autoseo_id', $autoseo->id)
            ->where('fecha_programada', $today)
            ->first();

        if (!$existingProgramacion) {
            $programacion = SeoProgramacion::create([
                'autoseo_id' => $autoseo->id,
                'fecha_programada' => $today,
                'estado' => 'pendiente'
            ]);

            $this->command->info("✅ Programación creada para hoy: ID {$programacion->id}");
        } else {
            $this->command->info("ℹ️ Ya existe una programación para hoy: ID {$existingProgramacion->id}");
        }

        // Crear servicios para el cliente
        $servicios = [
            'Consultoría empresarial',
            'Desarrollo de software',
            'Marketing digital',
            'Diseño gráfico',
            'Soporte técnico'
        ];

        // Eliminar servicios existentes
        ClienteServicio::where('autoseo_id', $autoseo->id)->delete();

        // Crear nuevos servicios
        foreach ($servicios as $index => $servicio) {
            ClienteServicio::create([
                'autoseo_id' => $autoseo->id,
                'nombre_servicio' => $servicio,
                'principal' => true,
                'orden' => $index + 1
            ]);
        }

        $this->command->info("✅ {$autoseo->id} servicios creados para el cliente ID {$autoseo->id}");
        $this->command->info("🎉 Datos de prueba creados exitosamente!");
        $this->command->info("");
        $this->command->info("📋 Puedes probar los endpoints con:");
        $this->command->info("   GET /api/autoseo/seotoday");
        $this->command->info("   GET /api/autoseo/servicios/{$autoseo->id}");
    }
}
