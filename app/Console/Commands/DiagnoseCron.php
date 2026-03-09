<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DiagnoseCron extends Command
{
    protected $signature = 'cron:diagnose';
    protected $description = 'Diagnostica problemas con los cron jobs de Laravel';

    public function handle()
    {
        $this->info('🔍 DIAGNÓSTICO DE CRON JOBS');
        $this->line(str_repeat('=', 60));
        $this->newLine();

        $issues = [];
        $warnings = [];
        $success = [];

        // 1. Verificar que storage tiene permisos
        $this->info('1️⃣ Verificando permisos de storage...');
        $storagePath = storage_path();
        if (!is_writable($storagePath)) {
            $issues[] = "❌ Storage no tiene permisos de escritura: {$storagePath}";
            $this->error("   Storage no tiene permisos de escritura");
        } else {
            $success[] = "✅ Permisos de storage correctos";
            $this->info("   ✅ Permisos correctos");
        }
        $this->newLine();

        // 2. Verificar archivos de bloqueo (withoutOverlapping)
        $this->info('2️⃣ Verificando archivos de bloqueo...');
        $frameworkPath = storage_path('framework');
        $lockFiles = glob($frameworkPath . '/schedule-*');
        if (count($lockFiles) > 0) {
            $this->warn("   ⚠️  Se encontraron " . count($lockFiles) . " archivos de bloqueo:");
            foreach ($lockFiles as $file) {
                $age = time() - filemtime($file);
                $ageHours = round($age / 3600, 1);
                if ($ageHours > 24) {
                    $warnings[] = "Archivo de bloqueo antiguo (>24h): " . basename($file);
                    $this->warn("      - " . basename($file) . " (antiguo: {$ageHours}h)");
                } else {
                    $this->line("      - " . basename($file) . " (reciente: {$ageHours}h)");
                }
            }
        } else {
            $success[] = "✅ No hay archivos de bloqueo";
            $this->info("   ✅ No hay archivos de bloqueo");
        }
        $this->newLine();

        // 3. Verificar configuración de queue
        $this->info('3️⃣ Verificando configuración de queue...');
        $queueConnection = config('queue.default');
        $this->line("   Queue connection: {$queueConnection}");
        
        if ($queueConnection === 'sync') {
            $warnings[] = "Queue está en modo 'sync' - los jobs se ejecutan síncronamente";
            $this->warn("   ⚠️  Queue en modo 'sync' (puede ser lento para jobs largos)");
        } elseif ($queueConnection === 'database') {
            // Verificar que existe la tabla jobs
            try {
                \DB::table('jobs')->count();
                $success[] = "✅ Tabla 'jobs' existe y es accesible";
                $this->info("   ✅ Tabla 'jobs' accesible");
            } catch (\Exception $e) {
                $issues[] = "Tabla 'jobs' no existe o no es accesible: " . $e->getMessage();
                $this->error("   ❌ Error accediendo a tabla 'jobs': " . $e->getMessage());
            }
        }
        $this->newLine();

        // 4. Verificar logs recientes
        $this->info('4️⃣ Verificando logs recientes...');
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            $logSize = File::size($logPath);
            $logSizeMB = round($logSize / 1024 / 1024, 2);
            $this->line("   Tamaño del log: {$logSizeMB} MB");
            
            if ($logSizeMB > 100) {
                $warnings[] = "El archivo de log es muy grande ({$logSizeMB} MB) - considera rotarlo";
                $this->warn("   ⚠️  Log muy grande ({$logSizeMB} MB)");
            }
            
            // Buscar errores recientes en el log
            $logContent = File::get($logPath);
            $errorCount = substr_count($logContent, 'ERROR');
            $this->line("   Errores encontrados en log: {$errorCount}");
        } else {
            $this->warn("   ⚠️  No se encontró el archivo de log");
        }
        $this->newLine();

        // 5. Verificar comandos programados
        $this->info('5️⃣ Verificando comandos programados...');
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $events = $schedule->events();
        $this->line("   Comandos programados: " . count($events));
        
        foreach ($events as $event) {
            $command = $event->command ?? $event->description ?? 'Closure';
            $expression = $event->expression;
            $this->line("      - {$command} ({$expression})");
        }
        $this->newLine();

        // 6. Verificar si hay procesos queue:work ejecutándose
        $this->info('6️⃣ Verificando procesos queue:work...');
        $processes = shell_exec("ps aux | grep 'queue:work' | grep -v grep");
        if ($processes) {
            $processCount = substr_count($processes, "\n");
            $this->info("   ✅ Encontrados {$processCount} proceso(s) queue:work ejecutándose");
            $success[] = "Queue worker está ejecutándose";
        } else {
            if ($queueConnection !== 'sync') {
                $warnings[] = "No hay procesos queue:work ejecutándose (necesario si usas colas)";
                $this->warn("   ⚠️  No hay procesos queue:work ejecutándose");
            } else {
                $this->info("   ℹ️  No necesario (queue en modo sync)");
            }
        }
        $this->newLine();

        // 7. Verificar crontab (solo muestra si está configurado, no puede verificar directamente)
        $this->info('7️⃣ Verificando crontab...');
        $this->line("   ⚠️  IMPORTANTE: Verifica manualmente que el crontab tenga:");
        $this->line("      * * * * * cd " . base_path() . " && php artisan schedule:run >> /dev/null 2>&1");
        $this->newLine();
        $this->warn("   Ejecuta: crontab -l  (para ver el crontab actual)");
        $this->newLine();

        // Resumen
        $this->line(str_repeat('=', 60));
        $this->info('📊 RESUMEN');
        $this->line(str_repeat('=', 60));
        
        if (count($success) > 0) {
            $this->info("\n✅ Correcto:");
            foreach ($success as $msg) {
                $this->line("   {$msg}");
            }
        }
        
        if (count($warnings) > 0) {
            $this->warn("\n⚠️  Advertencias:");
            foreach ($warnings as $msg) {
                $this->warn("   {$msg}");
            }
        }
        
        if (count($issues) > 0) {
            $this->error("\n❌ Problemas críticos:");
            foreach ($issues as $msg) {
                $this->error("   {$msg}");
            }
            return Command::FAILURE;
        }
        
        if (count($warnings) > 0) {
            $this->warn("\n⚠️  Hay advertencias pero no problemas críticos");
            return Command::SUCCESS;
        }
        
        $this->info("\n✅ Todo parece estar correcto!");
        return Command::SUCCESS;
    }
}
