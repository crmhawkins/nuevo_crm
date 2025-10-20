<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\SeoProgramacion;
use Carbon\Carbon;

class TestSeoScheduling extends Command
{
    protected $signature = 'autoseo:test-scheduling {client_id? : ID del cliente para probar}';
    protected $description = 'Prueba la funcionalidad de programación periódica de SEO';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        
        if ($clientId) {
            $this->testClientScheduling($clientId);
        } else {
            $this->testAllClients();
        }
    }

    private function testClientScheduling($clientId)
    {
        $client = Autoseo::find($clientId);
        
        if (!$client) {
            $this->error("❌ Cliente con ID {$clientId} no encontrado");
            return;
        }

        $this->info("🔍 Probando programación para: {$client->client_name}");
        
        // Mostrar programaciones existentes
        $programaciones = SeoProgramacion::where('autoseo_id', $clientId)
            ->orderBy('fecha_programada')
            ->get();

        if ($programaciones->isEmpty()) {
            $this->warn("⚠️ No hay programaciones para este cliente");
            return;
        }

        $this->info("📅 Programaciones encontradas: " . $programaciones->count());
        
        $this->table(
            ['ID', 'Fecha Programada', 'Estado', 'Días Restantes'],
            $programaciones->map(function ($prog) {
                $fecha = Carbon::parse($prog->fecha_programada);
                $diasRestantes = $fecha->diffInDays(Carbon::now(), false);
                
                return [
                    $prog->id,
                    $fecha->format('d/m/Y'),
                    $prog->estado,
                    $diasRestantes > 0 ? "En {$diasRestantes} días" : ($diasRestantes < 0 ? "Hace " . abs($diasRestantes) . " días" : "Hoy")
                ];
            })
        );

        // Mostrar próximas programaciones
        $proximas = SeoProgramacion::where('autoseo_id', $clientId)
            ->where('fecha_programada', '>=', Carbon::today())
            ->where('estado', 'pendiente')
            ->orderBy('fecha_programada')
            ->take(5)
            ->get();

        if ($proximas->isNotEmpty()) {
            $this->info("\n📋 Próximas 5 programaciones:");
            foreach ($proximas as $prog) {
                $fecha = Carbon::parse($prog->fecha_programada);
                $this->line("  - {$fecha->format('d/m/Y')} ({$fecha->diffForHumans()})");
            }
        }
    }

    private function testAllClients()
    {
        $this->info("🔍 Verificando programaciones de todos los clientes...");
        
        $clients = Autoseo::with('programaciones')->get();
        
        $this->table(
            ['ID', 'Cliente', 'Programaciones', 'Pendientes', 'Completadas', 'Próxima Fecha'],
            $clients->map(function ($client) {
                $programaciones = $client->programaciones;
                $pendientes = $programaciones->where('estado', 'pendiente')->count();
                $completadas = $programaciones->where('estado', 'completado')->count();
                
                $proximaFecha = $programaciones
                    ->where('fecha_programada', '>=', Carbon::today())
                    ->where('estado', 'pendiente')
                    ->sortBy('fecha_programada')
                    ->first();
                
                return [
                    $client->id,
                    $client->client_name,
                    $programaciones->count(),
                    $pendientes,
                    $completadas,
                    $proximaFecha ? Carbon::parse($proximaFecha->fecha_programada)->format('d/m/Y') : 'N/A'
                ];
            })
        );

        // Estadísticas generales
        $totalProgramaciones = SeoProgramacion::count();
        $pendientes = SeoProgramacion::where('estado', 'pendiente')->count();
        $completadas = SeoProgramacion::where('estado', 'completado')->count();
        $hoy = SeoProgramacion::where('fecha_programada', Carbon::today())
            ->where('estado', 'pendiente')
            ->count();

        $this->info("\n📊 Estadísticas Generales:");
        $this->line("  Total programaciones: {$totalProgramaciones}");
        $this->line("  Pendientes: {$pendientes}");
        $this->line("  Completadas: {$completadas}");
        $this->line("  Programadas para hoy: {$hoy}");

        if ($hoy > 0) {
            $this->warn("\n⚠️ Hay {$hoy} programaciones pendientes para hoy!");
        }
    }
}