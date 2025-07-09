<?php

namespace App\Console\Commands;

use App\Models\Tasks\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FinalizarTareasMaestras extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tareas:finalizar-maestras';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finaliza automáticamente las tareas maestras cuando todas sus subtareas estén finalizadas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de finalización automática de tareas maestras...');

        // Obtener todas las tareas maestras que están pausadas (ID 2)
        // Una tarea es maestra si no tiene split_master_task_id (es null)
        $tareasMaestras = Task::where('task_status_id', 2) // Pausada
            ->whereNull('split_master_task_id') // Son tareas maestras (no tienen tarea padre)
            ->get();

        $tareasFinalizadas = 0;

        foreach ($tareasMaestras as $tareaMaestra) {
            // Obtener todas las subtareas de esta tarea maestra
            // Las subtareas son aquellas que tienen split_master_task_id = id de la tarea maestra
            $subtareas = Task::where('split_master_task_id', $tareaMaestra->id)->get();
            
            if ($subtareas->isEmpty()) {
                continue; // No hay subtareas, saltar
            }

            // Verificar si todas las subtareas están finalizadas (ID 3)
            $todasFinalizadas = $subtareas->every(function ($subtarea) {
                return $subtarea->task_status_id == 3; // Finalizada
            });

            if ($todasFinalizadas) {
                // Finalizar la tarea maestra
                $tareaMaestra->task_status_id = 3; // Finalizada
                $tareaMaestra->save();

                $tareasFinalizadas++;

                $this->info("✅ Tarea maestra '{$tareaMaestra->title}' (ID: {$tareaMaestra->id}) finalizada automáticamente.");
                
                // Log de la acción
                Log::info("Tarea maestra finalizada automáticamente", [
                    'tarea_id' => $tareaMaestra->id,
                    'titulo' => $tareaMaestra->title,
                    'subtareas_count' => $subtareas->count(),
                    'fecha' => now()->toDateTimeString()
                ]);
            }
        }

        if ($tareasFinalizadas > 0) {
            $this->info("🎉 Proceso completado. Se finalizaron {$tareasFinalizadas} tareas maestras.");
        } else {
            $this->info("ℹ️ No se encontraron tareas maestras que cumplan las condiciones para ser finalizadas.");
        }

        return 0;
    }
} 