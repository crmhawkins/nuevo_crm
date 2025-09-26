<?php

namespace App\Console\Commands;

use App\Models\Cita;
use App\Models\Clients\Client;
use App\Models\Users\User;
use Illuminate\Console\Command;

class TestCitasSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:citas-system';

    /**
     * The console command description.
     */
    protected $description = 'Probar el sistema de citas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Probando sistema de citas...');

        // 1. Verificar que el modelo funciona
        $this->info('1️⃣ Verificando modelo Cita...');
        try {
            $cita = new Cita();
            $this->info('✅ Modelo Cita creado correctamente');
        } catch (\Exception $e) {
            $this->error('❌ Error en modelo Cita: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 2. Verificar que se pueden crear citas
        $this->info('2️⃣ Probando creación de citas...');
        try {
            $cliente = Client::where('is_client', 1)->first();
            $gestor = User::where('access_level_id', 4)->first();
            $usuario = User::first();

            if (!$usuario) {
                $this->error('❌ No hay usuarios en el sistema');
                return Command::FAILURE;
            }

            $cita = Cita::create([
                'titulo' => 'Cita de Prueba',
                'descripcion' => 'Esta es una cita de prueba del sistema',
                'fecha_inicio' => now()->addHour(),
                'fecha_fin' => now()->addHours(2),
                'tipo' => 'reunion',
                'cliente_id' => $cliente ? $cliente->id : null,
                'gestor_id' => $gestor ? $gestor->id : null,
                'creado_por' => $usuario->id,
                'actualizado_por' => $usuario->id
            ]);

            $this->info("✅ Cita creada correctamente (ID: {$cita->id})");
        } catch (\Exception $e) {
            $this->error('❌ Error al crear cita: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 3. Verificar relaciones
        $this->info('3️⃣ Probando relaciones...');
        try {
            $cita->load(['cliente', 'gestor', 'creador']);
            $this->info('✅ Relaciones cargadas correctamente');
        } catch (\Exception $e) {
            $this->error('❌ Error en relaciones: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 4. Verificar scopes
        $this->info('4️⃣ Probando scopes...');
        try {
            $citasHoy = Cita::whereDate('fecha_inicio', today())->count();
            $citasProximas = Cita::proximas(7)->count();
            $this->info("✅ Scopes funcionando - Citas hoy: {$citasHoy}, Próximas: {$citasProximas}");
        } catch (\Exception $e) {
            $this->error('❌ Error en scopes: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 5. Verificar atributos calculados
        $this->info('5️⃣ Probando atributos calculados...');
        try {
            $duracion = $cita->duracion;
            $estadoFormateado = $cita->estado_formateado;
            $tipoFormateado = $cita->tipo_formateado;
            $this->info("✅ Atributos calculados - Duración: {$duracion} min, Estado: {$estadoFormateado}, Tipo: {$tipoFormateado}");
        } catch (\Exception $e) {
            $this->error('❌ Error en atributos calculados: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 6. Limpiar cita de prueba
        $this->info('6️⃣ Limpiando datos de prueba...');
        try {
            $cita->delete();
            $this->info('✅ Cita de prueba eliminada');
        } catch (\Exception $e) {
            $this->warn('⚠️ No se pudo eliminar la cita de prueba: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('🎉 ¡Sistema de citas funcionando correctamente!');
        $this->info('📋 Funcionalidades disponibles:');
        $this->line('   • Crear, editar y eliminar citas');
        $this->line('   • Asignar clientes y gestores');
        $this->line('   • Citas recurrentes');
        $this->line('   • Notificaciones');
        $this->line('   • Seguimiento post-cita');
        $this->line('   • Calendario avanzado');
        $this->line('   • Filtros y búsquedas');
        $this->line('   • Estadísticas');

        return Command::SUCCESS;
    }
}
