<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\SeoProgramacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DailySeoMaintenance extends Command
{
    protected $signature = 'autoseo:daily-maintenance';
    protected $description = 'Mantenimiento diario del sistema SEO: ejecuta SEO programado y renueva programaciones';

    public function handle()
    {
        $this->info("🔄 Iniciando mantenimiento diario SEO...");
        
        // 1. Ejecutar SEO programado para hoy
        $this->executeTodaySeo();
        
        // 2. Renovar programaciones que están próximas a expirar
        $this->renewExpiringSchedules();
        
        // 3. Limpiar programaciones muy antiguas
        $this->cleanOldSchedules();
        
        $this->info("✅ Mantenimiento diario completado");
    }

    /**
     * Ejecuta SEO para clientes programados para hoy
     */
    private function executeTodaySeo()
    {
        $today = Carbon::today();
        
        $programacionesHoy = SeoProgramacion::where('fecha_programada', $today)
            ->where('estado', 'pendiente')
            ->with('autoseo')
            ->get();

        if ($programacionesHoy->isEmpty()) {
            $this->info("📅 No hay SEO programado para hoy");
            return;
        }

        $this->info("📅 Ejecutando SEO para {$programacionesHoy->count()} clientes programados para hoy");

        foreach ($programacionesHoy as $programacion) {
            $this->executeSeoForClient($programacion);
        }
    }

    /**
     * Ejecuta SEO para un cliente específico
     */
    private function executeSeoForClient($programacion)
    {
        $client = $programacion->autoseo;
        
        try {
            $this->line("🔍 Ejecutando SEO para: {$client->client_name}");
            
            // Aquí iría la lógica para ejecutar el SEO
            // Por ahora solo marcamos como completado
            $programacion->estado = 'completado';
            $programacion->save();
            
            $this->line("✅ SEO completado para: {$client->client_name}");
            
            Log::info("SEO ejecutado para cliente ID {$client->id} - Programación ID {$programacion->id}");
            
        } catch (\Exception $e) {
            $programacion->estado = 'error';
            $programacion->save();
            
            $this->error("❌ Error ejecutando SEO para {$client->client_name}: " . $e->getMessage());
            
            Log::error("Error ejecutando SEO para cliente ID {$client->id}: " . $e->getMessage());
        }
    }

    /**
     * Renueva programaciones que están próximas a expirar
     */
    private function renewExpiringSchedules()
    {
        $this->info("🔄 Verificando programaciones próximas a expirar...");
        
        // Buscar clientes con programaciones que expiran en los próximos 3 meses
        $clientsToRenew = Autoseo::whereHas('programaciones', function($query) {
            $query->where('estado', 'pendiente');
        })->with(['programaciones' => function($query) {
            $query->where('estado', 'pendiente')
                  ->orderBy('fecha_programada', 'desc');
        }])->get();

        $renewed = 0;
        foreach ($clientsToRenew as $client) {
            $lastProgramacion = $client->programaciones->first();
            
            if (!$lastProgramacion) {
                continue;
            }

            $lastDate = Carbon::parse($lastProgramacion->fecha_programada);
            $monthsUntilExpiry = $lastDate->diffInMonths(Carbon::now(), false);

            // Si la última programación está a menos de 3 meses, renovar
            if ($monthsUntilExpiry <= 3) {
                $this->renewClientSchedule($client, $lastDate);
                $renewed++;
            }
        }

        if ($renewed > 0) {
            $this->info("🔄 Renovadas programaciones para {$renewed} clientes");
        } else {
            $this->info("✅ No hay programaciones que necesiten renovación");
        }
    }

    /**
     * Renueva las programaciones de un cliente específico
     */
    private function renewClientSchedule($client, $lastDate)
    {
        // Detectar frecuencia
        $frequency = $this->detectFrequency($client);
        
        // Crear nuevas fechas
        $newDates = $this->createNewScheduleDates($frequency, $lastDate);
        
        $created = 0;
        foreach ($newDates as $date) {
            $exists = SeoProgramacion::where('autoseo_id', $client->id)
                ->where('fecha_programada', $date)
                ->exists();

            if (!$exists) {
                SeoProgramacion::create([
                    'autoseo_id' => $client->id,
                    'fecha_programada' => $date,
                    'estado' => 'pendiente',
                ]);
                $created++;
            }
        }

        Log::info("Programaciones renovadas para cliente ID {$client->id}: {$created} nuevas fechas");
    }

    /**
     * Detecta la frecuencia de programación
     */
    private function detectFrequency($client)
    {
        $programaciones = $client->programaciones()
            ->where('estado', 'pendiente')
            ->orderBy('fecha_programada')
            ->take(10)
            ->get();

        if ($programaciones->count() < 2) {
            return 'monthly';
        }

        $dates = $programaciones->pluck('fecha_programada')->map(function($date) {
            return Carbon::parse($date);
        });

        $differences = [];
        for ($i = 1; $i < $dates->count(); $i++) {
            $diff = $dates[$i]->diffInDays($dates[$i-1]);
            $differences[] = $diff;
        }

        $avgDifference = array_sum($differences) / count($differences);

        if ($avgDifference <= 8) {
            return 'weekly';
        } elseif ($avgDifference <= 15) {
            return 'biweekly';
        } elseif ($avgDifference <= 35) {
            return 'monthly';
        } elseif ($avgDifference <= 70) {
            return 'bimonthly';
        } else {
            return 'quarterly';
        }
    }

    /**
     * Crea nuevas fechas de programación
     */
    private function createNewScheduleDates($frequency, $startDate)
    {
        $dates = [];
        $current = $startDate->copy();

        switch ($frequency) {
            case 'weekly':
                for ($i = 1; $i <= 52; $i++) {
                    $dates[] = $current->addWeek()->format('Y-m-d');
                }
                break;
                
            case 'biweekly':
                for ($i = 1; $i <= 26; $i++) {
                    $dates[] = $current->addWeeks(2)->format('Y-m-d');
                }
                break;
                
            case 'monthly':
                for ($i = 1; $i <= 12; $i++) {
                    $dates[] = $current->addMonth()->format('Y-m-d');
                }
                break;
                
            case 'bimonthly':
                for ($i = 1; $i <= 6; $i++) {
                    $dates[] = $current->addMonths(2)->format('Y-m-d');
                }
                break;
                
            case 'quarterly':
                for ($i = 1; $i <= 4; $i++) {
                    $dates[] = $current->addMonths(3)->format('Y-m-d');
                }
                break;
        }

        return $dates;
    }

    /**
     * Limpia programaciones muy antiguas (más de 2 años)
     */
    private function cleanOldSchedules()
    {
        $cutoffDate = Carbon::now()->subYears(2);
        
        $deleted = SeoProgramacion::where('fecha_programada', '<', $cutoffDate)
            ->where('estado', 'completado')
            ->delete();

        if ($deleted > 0) {
            $this->info("🧹 Limpiadas {$deleted} programaciones antiguas");
            Log::info("Limpiadas {$deleted} programaciones SEO antiguas");
        }
    }
}