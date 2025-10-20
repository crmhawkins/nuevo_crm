<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\SeoProgramacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RenewSeoSchedules extends Command
{
    protected $signature = 'autoseo:renew-schedules {--dry-run : Solo mostrar qué se haría sin ejecutar}';
    protected $description = 'Renueva automáticamente las programaciones SEO que están próximas a expirar';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info("🔍 MODO DRY-RUN: Solo mostrando qué se haría");
        }

        $this->info("🔄 Renovando programaciones SEO automáticas...");

        // Buscar clientes que necesitan renovación
        $clientsToRenew = $this->findClientsNeedingRenewal();
        
        if ($clientsToRenew->isEmpty()) {
            $this->info("✅ No hay clientes que necesiten renovación de programaciones");
            return;
        }

        $this->info("📋 Clientes que necesitan renovación: " . $clientsToRenew->count());

        foreach ($clientsToRenew as $client) {
            $this->renewClientSchedule($client, $isDryRun);
        }

        $this->info("🎉 Proceso de renovación completado!");
    }

    /**
     * Encuentra clientes que necesitan renovación de programaciones
     */
    private function findClientsNeedingRenewal()
    {
        // Buscar clientes que tienen programaciones pero la última está próxima a expirar
        $clients = Autoseo::whereHas('programaciones', function($query) {
            $query->where('estado', 'pendiente');
        })->with(['programaciones' => function($query) {
            $query->where('estado', 'pendiente')
                  ->orderBy('fecha_programada', 'desc');
        }])->get();

        $clientsNeedingRenewal = collect();

        foreach ($clients as $client) {
            $lastProgramacion = $client->programaciones->first();
            
            if (!$lastProgramacion) {
                continue;
            }

            $lastDate = Carbon::parse($lastProgramacion->fecha_programada);
            $monthsUntilExpiry = $lastDate->diffInMonths(Carbon::now(), false);

            // Si la última programación está a menos de 3 meses, necesita renovación
            if ($monthsUntilExpiry <= 3) {
                $clientsNeedingRenewal->push([
                    'client' => $client,
                    'last_date' => $lastDate,
                    'months_until_expiry' => $monthsUntilExpiry
                ]);
            }
        }

        return $clientsNeedingRenewal;
    }

    /**
     * Renueva las programaciones de un cliente específico
     */
    private function renewClientSchedule($clientData, $isDryRun = false)
    {
        $client = $clientData['client'];
        $lastDate = $clientData['last_date'];
        $monthsUntilExpiry = $clientData['months_until_expiry'];

        $this->line("\n📅 Cliente: {$client->client_name}");
        $this->line("   Última programación: {$lastDate->format('d/m/Y')}");
        $this->line("   Meses hasta expiración: {$monthsUntilExpiry}");

        // Determinar la frecuencia basándose en las programaciones existentes
        $frequency = $this->detectFrequency($client);
        $this->line("   Frecuencia detectada: {$frequency}");

        if ($isDryRun) {
            $this->line("   🔍 DRY-RUN: Se crearían nuevas programaciones con frecuencia {$frequency}");
            return;
        }

        // Crear nuevas programaciones
        $newDates = $this->createNewScheduleDates($frequency, $lastDate);
        
        $created = 0;
        foreach ($newDates as $date) {
            // Verificar que no exista ya una programación para esa fecha
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

        $this->line("   ✅ Creadas {$created} nuevas programaciones");
        
        Log::info("🔄 Programaciones renovadas para cliente ID {$client->id}: {$created} nuevas fechas");
    }

    /**
     * Detecta la frecuencia de programación basándose en las fechas existentes
     */
    private function detectFrequency($client)
    {
        $programaciones = $client->programaciones()
            ->where('estado', 'pendiente')
            ->orderBy('fecha_programada')
            ->take(10)
            ->get();

        if ($programaciones->count() < 2) {
            return 'monthly'; // Default
        }

        $dates = $programaciones->pluck('fecha_programada')->map(function($date) {
            return Carbon::parse($date);
        });

        // Calcular diferencias entre fechas consecutivas
        $differences = [];
        for ($i = 1; $i < $dates->count(); $i++) {
            $diff = $dates[$i]->diffInDays($dates[$i-1]);
            $differences[] = $diff;
        }

        $avgDifference = array_sum($differences) / count($differences);

        // Determinar frecuencia basándose en la diferencia promedio
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
     * Crea nuevas fechas de programación basándose en la frecuencia y fecha de inicio
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
}