<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Models\Invoices\InvoiceConcepts;
use Carbon\Carbon;

class UpdateDomainStatuses extends Command
{
    protected $signature = 'domains:update-statuses {--dry-run : Solo mostrar los cambios sin aplicarlos}';
    protected $description = 'Actualiza automáticamente los estados de los dominios basándose en sus fechas de renovación.';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info("🔄 Actualizando estados de dominios automáticamente...");
        $this->line("  - Modo: " . ($isDryRun ? 'DRY RUN (solo mostrar)' : 'ACTUALIZAR estados'));

        $today = Carbon::now();
        $vencidoCount = 0;
        $renovadoCount = 0;
        $noChangeCount = 0;
        $errors = 0;

        // Obtener todos los dominios que no están cancelados
        $dominios = Dominio::where('estado_id', '!=', 2)->get();

        $this->line("📊 Dominios a procesar: " . $dominios->count());

        $progressBar = $this->output->createProgressBar($dominios->count());
        $progressBar->start();

        foreach ($dominios as $dominio) {
            $newStatus = null;
            $reason = '';

            // Verificar si el dominio está vencido
            if ($dominio->fecha_renovacion_ionos) {
                $fechaRenovacion = Carbon::parse($dominio->fecha_renovacion_ionos);
                $añoVencimiento = $fechaRenovacion->year;
                $añoActual = $today->year;
                
                if ($fechaRenovacion->isPast()) {
                    // Dominio vencido
                    $newStatus = 7; // Vencido
                    $reason = "Vencido desde " . $fechaRenovacion->format('d/m/Y');
                } else {
                    // Para dominios futuros, buscar factura del año actual
                    // Para dominios del año actual, buscar factura del año de vencimiento
                    $añoBuscar = $añoVencimiento <= $añoActual ? $añoVencimiento : $añoActual;
                    
                    $tieneFactura = $this->tieneFacturaDelAño($dominio, $añoBuscar);
                    
                    if ($tieneFactura) {
                        $newStatus = 8; // Renovado
                        $reason = "Renovado hasta " . $fechaRenovacion->format('d/m/Y') . " (con factura {$añoBuscar})";
                    } else {
                        $newStatus = 1; // No pagado
                        $reason = "Sin factura del año {$añoBuscar}";
                    }
                }
            } elseif ($dominio->date_end) {
                // Usar fecha_end como fallback
                $fechaEnd = Carbon::parse($dominio->date_end);
                $añoVencimiento = $fechaEnd->year;
                $añoActual = $today->year;
                
                if ($fechaEnd->isPast()) {
                    $newStatus = 7; // Vencido
                    $reason = "Vencido desde " . $fechaEnd->format('d/m/Y') . " (fecha_end)";
                } else {
                    // Para dominios futuros, buscar factura del año actual
                    // Para dominios del año actual, buscar factura del año de vencimiento
                    $añoBuscar = $añoVencimiento <= $añoActual ? $añoVencimiento : $añoActual;
                    
                    $tieneFactura = $this->tieneFacturaDelAño($dominio, $añoBuscar);
                    
                    if ($tieneFactura) {
                        $newStatus = 8; // Renovado
                        $reason = "Renovado hasta " . $fechaEnd->format('d/m/Y') . " (con factura {$añoBuscar})";
                    } else {
                        $newStatus = 1; // No pagado
                        $reason = "Sin factura del año {$añoBuscar}";
                    }
                }
            }

            if ($newStatus && $dominio->estado_id != $newStatus) {
                if ($isDryRun) {
                    $this->line("\n🔍 Dominio: {$dominio->dominio}");
                    $this->line("  - Estado actual: {$dominio->estado_id}");
                    $this->line("  - Nuevo estado: {$newStatus}");
                    $this->line("  - Razón: {$reason}");
                } else {
                    try {
                        $dominio->update(['estado_id' => $newStatus]);
                        if ($newStatus == 7) {
                            $vencidoCount++;
                        } else {
                            $renovadoCount++;
                        }
                    } catch (\Exception $e) {
                        $this->error("Error al actualizar dominio {$dominio->dominio}: " . $e->getMessage());
                        $errors++;
                    }
                }
            } else {
                $noChangeCount++;
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("📊 Resumen de la actualización:");
        $this->line("  - Dominios procesados: " . $dominios->count());
        if ($isDryRun) {
            $this->line("  - Modo DRY RUN: No se actualizaron estados");
        } else {
            $this->line("  - Dominios marcados como Vencidos: {$vencidoCount}");
            $this->line("  - Dominios marcados como Renovados: {$renovadoCount}");
            $this->line("  - Dominios sin cambios: {$noChangeCount}");
        }
        $this->line("  - Errores: {$errors}");

        if ($isDryRun) {
            $this->warn("🔍 Modo DRY RUN: No se realizaron cambios.");
            $this->info("💡 Para aplicar los cambios, ejecuta sin --dry-run");
        } else {
            $this->info("✅ Actualización de estados completada exitosamente.");
        }
    }

    private function tieneFacturaDelAño($dominio, $año)
    {
        // Normalizar el nombre del dominio para la búsqueda
        $normalizedDomain = $this->normalizeDomainName($dominio->dominio);
        
        // Buscar en conceptos de facturas del mismo cliente del año especificado
        $tieneFactura = InvoiceConcepts::whereHas('invoice.budget', function($query) use ($dominio, $año) {
            $query->where('client_id', $dominio->client_id);
        })
        ->whereHas('invoice', function($query) use ($año) {
            $query->whereYear('created_at', $año);
        })
        ->where(function($query) use ($normalizedDomain) {
            $query->where('title', 'like', "%{$normalizedDomain}%")
                  ->orWhere('concept', 'like', "%{$normalizedDomain}%")
                  ->orWhere('title', 'like', '%dominio%')
                  ->orWhere('concept', 'like', '%dominio%')
                  ->orWhere('title', 'like', '%Dominio%')
                  ->orWhere('concept', 'like', '%Dominio%')
                  ->orWhere('title', 'like', '%DOMINIO%')
                  ->orWhere('concept', 'like', '%DOMINIO%');
        })
        ->exists();

        return $tieneFactura;
    }

    private function normalizeDomainName($domainName)
    {
        $domainName = strtolower(trim($domainName));
        // Eliminar http://, https://, y barras finales
        $domainName = preg_replace('/^https?:\/\//', '', $domainName);
        $domainName = rtrim($domainName, '/');
        return $domainName;
    }
}
