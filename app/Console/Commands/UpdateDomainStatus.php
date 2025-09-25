<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Models\Invoices\InvoiceConcepts;
use Illuminate\Support\Facades\DB;

class UpdateDomainStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dominios:update-status 
                            {--year= : Año específico para verificar (por defecto año actual)}
                            {--dry-run : Solo mostrar qué cambios se harían sin actualizar}
                            {--limit= : Límite de dominios a procesar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de los dominios basándose en el año y facturas asociadas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->option('year') ?: now()->year;
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit');

        $this->info('🔄 Actualizando estados de dominios...');
        $this->line("  - Año de verificación: {$year}");
        $this->line("  - Modo: " . ($dryRun ? 'DRY RUN (solo mostrar)' : 'ACTUALIZAR estados'));
        $this->line("  - Límite: " . ($limit ?: 'Sin límite'));

        try {
            // Obtener dominios
            $query = Dominio::with(['cliente']);
            
            if ($limit) {
                $query->limit($limit);
            }
            
            $dominios = $query->get();
            
            $this->info("📊 Procesando " . $dominios->count() . " dominios...");
            
            $vigenteCount = 0;
            $impagadoCount = 0;
            $sinCambioCount = 0;
            $errorCount = 0;
            
            $progressBar = $this->output->createProgressBar($dominios->count());
            $progressBar->start();
            
            foreach ($dominios as $dominio) {
                try {
                    $nuevoEstado = $this->determinarEstado($dominio, $year);
                    $estadoActual = $dominio->estado_id;
                    
                    if ($nuevoEstado !== $estadoActual) {
                        if ($dryRun) {
                            $this->line("\n🔍 Dominio: {$dominio->dominio}");
                            $this->line("  - Estado actual: {$estadoActual}");
                            $this->line("  - Nuevo estado: {$nuevoEstado}");
                            $this->line("  - Razón: " . $this->obtenerRazonEstado($dominio, $year));
                        } else {
                            $dominio->update(['estado_id' => $nuevoEstado]);
                            
                            if ($nuevoEstado == 3) { // Vigente
                                $vigenteCount++;
                            } elseif ($nuevoEstado == 1) { // Impagado
                                $impagadoCount++;
                            }
                        }
                    } else {
                        $sinCambioCount++;
                    }
                    
                    $progressBar->advance();
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->line("\n❌ Error procesando {$dominio->dominio}: " . $e->getMessage());
                    $progressBar->advance();
                }
            }
            
            $progressBar->finish();
            $this->newLine();
            
            // Mostrar resumen
            $this->info('📊 Resumen de la actualización:');
            $this->line("  - Dominios procesados: " . $dominios->count());
            
            if ($dryRun) {
                $this->line("  - Modo DRY RUN: No se actualizaron estados");
                $this->line("  - Errores: {$errorCount}");
            } else {
                $this->line("  - Dominios cambiados a VIGENTE: {$vigenteCount}");
                $this->line("  - Dominios cambiados a IMPAGADO: {$impagadoCount}");
                $this->line("  - Dominios sin cambios: {$sinCambioCount}");
                $this->line("  - Errores: {$errorCount}");
            }
            
            if ($dryRun) {
                $this->info('🔍 Modo DRY RUN: No se realizaron cambios.');
                $this->info('💡 Para aplicar los cambios, ejecuta sin --dry-run');
            } else {
                $this->info('✅ Actualización de estados completada exitosamente.');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error general: ' . $e->getMessage());
        }
    }
    
    /**
     * Determinar el estado del dominio basándose en el año y facturas
     */
    private function determinarEstado($dominio, $year)
    {
        // Verificar si el dominio es de este año
        $esDeEsteAño = $this->esDominioDeEsteAño($dominio, $year);
        
        // Verificar si tiene facturas asociadas de este año
        $tieneFacturasEsteAño = $this->tieneFacturasEsteAño($dominio, $year);
        
        // Si cumple alguna condición, es VIGENTE (estado 3)
        if ($esDeEsteAño || $tieneFacturasEsteAño) {
            return 3; // Vigente
        }
        
        // Si no cumple ninguna condición, es IMPAGADO (estado 1)
        return 1; // Impagado
    }
    
    /**
     * Verificar si el dominio es de este año
     */
    private function esDominioDeEsteAño($dominio, $year)
    {
        // Verificar fecha de inicio
        if ($dominio->date_start) {
            $fechaInicio = \Carbon\Carbon::parse($dominio->date_start);
            if ($fechaInicio->year == $year) {
                return true;
            }
        }
        
        // Verificar fecha de activación IONOS
        if ($dominio->fecha_activacion_ionos) {
            $fechaActivacion = \Carbon\Carbon::parse($dominio->fecha_activacion_ionos);
            if ($fechaActivacion->year == $year) {
                return true;
            }
        }
        
        // Verificar fecha de registro calculada
        if ($dominio->fecha_registro_calculada) {
            $fechaRegistro = \Carbon\Carbon::parse($dominio->fecha_registro_calculada);
            if ($fechaRegistro->year == $year) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verificar si el dominio tiene facturas asociadas de este año
     */
    private function tieneFacturasEsteAño($dominio, $year)
    {
        // Buscar facturas del cliente con la palabra "dominio" en el año especificado
        $facturas = InvoiceConcepts::whereHas('invoice.budget', function($query) use ($dominio) {
            $query->where('client_id', $dominio->client_id);
        })
        ->whereYear('created_at', $year)
        ->where(function($query) {
            $query->where('title', 'like', '%dominio%')
                  ->orWhere('concept', 'like', '%dominio%')
                  ->orWhere('title', 'like', '%Dominio%')
                  ->orWhere('concept', 'like', '%Dominio%')
                  ->orWhere('title', 'like', '%DOMINIO%')
                  ->orWhere('concept', 'like', '%DOMINIO%');
        })
        ->exists();
        
        return $facturas;
    }
    
    /**
     * Obtener la razón del estado
     */
    private function obtenerRazonEstado($dominio, $year)
    {
        $razones = [];
        
        if ($this->esDominioDeEsteAño($dominio, $year)) {
            $razones[] = "Dominio de {$year}";
        }
        
        if ($this->tieneFacturasEsteAño($dominio, $year)) {
            $razones[] = "Tiene facturas de {$year}";
        }
        
        if (empty($razones)) {
            return "No cumple condiciones para VIGENTE";
        }
        
        return implode(' + ', $razones);
    }
}
