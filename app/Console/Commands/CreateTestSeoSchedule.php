<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\SeoProgramacion;
use Carbon\Carbon;

class CreateTestSeoSchedule extends Command
{
    protected $signature = 'autoseo:create-test-schedule {client_id} {frequency=monthly}';
    protected $description = 'Crea programaciones de prueba para un cliente específico';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        $frequency = $this->argument('frequency');

        $client = Autoseo::find($clientId);
        
        if (!$client) {
            $this->error("❌ Cliente con ID {$clientId} no encontrado");
            return;
        }

        $this->info("🔧 Creando programaciones de prueba para: {$client->client_name}");
        $this->info("📅 Frecuencia: {$frequency}");

        // Eliminar programaciones existentes
        $existing = SeoProgramacion::where('autoseo_id', $clientId)->count();
        if ($existing > 0) {
            SeoProgramacion::where('autoseo_id', $clientId)->delete();
            $this->info("🗑️ Eliminadas {$existing} programaciones existentes");
        }

        // Crear configuración de prueba
        $config = [
            'seo_frequency' => $frequency,
            'seo_day_of_month' => '15',
            'seo_day_of_week' => 'friday',
            'seo_time' => '09:00',
        ];

        // Crear programaciones
        $dates = $this->calculateScheduleDates($frequency, $config);
        
        foreach ($dates as $date) {
            SeoProgramacion::create([
                'autoseo_id' => $clientId,
                'fecha_programada' => $date,
                'estado' => 'pendiente',
            ]);
        }

        $this->info("✅ Creadas " . count($dates) . " programaciones");
        
        // Mostrar las primeras 5 fechas
        $this->info("\n📋 Primeras 5 programaciones:");
        foreach (array_slice($dates, 0, 5) as $date) {
            $fecha = Carbon::parse($date);
            $this->line("  - {$fecha->format('d/m/Y')} ({$fecha->diffForHumans()})");
        }

        $this->info("\n🎉 Programaciones de prueba creadas exitosamente!");
    }

    /**
     * Calcula las fechas de programación según la frecuencia configurada
     */
    private function calculateScheduleDates($frequency, $config)
    {
        $dates = [];
        $now = Carbon::now();
        
        switch ($frequency) {
            case 'weekly':
                $dayOfWeek = $config['seo_day_of_week'] ?? 'friday';
                $startDate = $now->next($dayOfWeek);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 52; $i++) {
                    $dates[] = $startDate->copy()->addWeeks($i)->format('Y-m-d');
                }
                break;
                
            case 'biweekly':
                $dayOfWeek = $config['seo_day_of_week'] ?? 'friday';
                $startDate = $now->next($dayOfWeek);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 26; $i++) {
                    $dates[] = $startDate->copy()->addWeeks($i * 2)->format('Y-m-d');
                }
                break;
                
            case 'monthly':
                $dayOfMonth = $config['seo_day_of_month'] ?? '15';
                $startDate = $this->getNextMonthlyDate($now, $dayOfMonth);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 12; $i++) {
                    $dates[] = $startDate->copy()->addMonths($i)->format('Y-m-d');
                }
                break;
                
            case 'bimonthly':
                $dayOfMonth = $config['seo_day_of_month'] ?? '15';
                $startDate = $this->getNextMonthlyDate($now, $dayOfMonth);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 6; $i++) {
                    $dates[] = $startDate->copy()->addMonths($i * 2)->format('Y-m-d');
                }
                break;
                
            case 'quarterly':
                $dayOfMonth = $config['seo_day_of_month'] ?? '15';
                $startDate = $this->getNextMonthlyDate($now, $dayOfMonth);
                
                // Crear programaciones para los próximos 12 meses
                for ($i = 0; $i < 4; $i++) {
                    $dates[] = $startDate->copy()->addMonths($i * 3)->format('Y-m-d');
                }
                break;
        }
        
        return $dates;
    }

    /**
     * Calcula la próxima fecha mensual según el día especificado
     */
    private function getNextMonthlyDate($now, $dayOfMonth)
    {
        if ($dayOfMonth === 'last') {
            $nextMonth = $now->copy()->addMonth();
            return $nextMonth->endOfMonth();
        }
        
        $day = (int) $dayOfMonth;
        $nextDate = $now->copy()->day($day);
        
        // Si ya pasó este mes, ir al próximo mes
        if ($nextDate->isPast()) {
            $nextDate->addMonth();
        }
        
        // Ajustar si el día no existe en el mes (ej: 31 en febrero)
        if (!$nextDate->isValid()) {
            $nextDate->endOfMonth();
        }
        
        return $nextDate;
    }
}