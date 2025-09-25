<?php

namespace App\Console\Commands;

use App\Models\Dominios\Dominio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dominios:remove-duplicates 
                            {--dry-run : Solo mostrar los duplicados sin eliminarlos}
                            {--force : Forzar la eliminación sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina dominios duplicados más recientes usando soft delete';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Analizando dominios duplicados...');
        
        // Buscar dominios duplicados (mismo nombre de dominio)
        $duplicates = $this->findDuplicateDomains();
        
        if ($duplicates->isEmpty()) {
            $this->info('✅ No se encontraron dominios duplicados.');
            return 0;
        }
        
        $this->info("📊 Se encontraron {$duplicates->count()} grupos de dominios duplicados.");
        
        // Mostrar resumen
        $this->displayDuplicateSummary($duplicates);
        
        if ($this->option('dry-run')) {
            $this->info('🔍 Modo dry-run: No se eliminarán dominios.');
            return 0;
        }
        
        // Confirmar eliminación
        if (!$this->option('force')) {
            if (!$this->confirm('¿Deseas proceder con la eliminación de los duplicados más recientes?')) {
                $this->info('❌ Operación cancelada.');
                return 0;
            }
        }
        
        // Eliminar duplicados
        $deletedCount = $this->removeDuplicateDomains($duplicates);
        
        $this->info("✅ Se eliminaron {$deletedCount} dominios duplicados.");
        
        return 0;
    }
    
    /**
     * Encuentra dominios duplicados agrupados por nombre
     */
    private function findDuplicateDomains()
    {
        return DB::table('dominios')
            ->select('dominio', DB::raw('COUNT(*) as count'))
            ->whereNull('deleted_at')
            ->groupBy('dominio')
            ->having('count', '>', 1)
            ->get()
            ->map(function ($duplicate) {
                // Obtener todos los dominios con este nombre
                $domains = Dominio::where('dominio', $duplicate->dominio)
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return [
                    'dominio' => $duplicate->dominio,
                    'count' => $duplicate->count,
                    'domains' => $domains
                ];
            });
    }
    
    /**
     * Muestra un resumen de los duplicados encontrados
     */
    private function displayDuplicateSummary($duplicates)
    {
        $this->newLine();
        $this->info('📋 Resumen de duplicados encontrados:');
        $this->newLine();
        
        $headers = ['Dominio', 'Total', 'Más Reciente', 'Más Antiguo', 'Clientes'];
        $rows = [];
        
        foreach ($duplicates as $duplicate) {
            $domains = $duplicate['domains'];
            $newest = $domains->first();
            $oldest = $domains->last();
            
            $clients = $domains->pluck('client_id')->unique()->implode(', ');
            
            $rows[] = [
                $duplicate['dominio'],
                $duplicate['count'],
                $newest->created_at->format('d/m/Y H:i'),
                $oldest->created_at->format('d/m/Y H:i'),
                $clients
            ];
        }
        
        $this->table($headers, $rows);
    }
    
    /**
     * Elimina los dominios duplicados más recientes
     */
    private function removeDuplicateDomains($duplicates)
    {
        $deletedCount = 0;
        $progressBar = $this->output->createProgressBar($duplicates->count());
        $progressBar->start();
        
        foreach ($duplicates as $duplicate) {
            $domains = $duplicate['domains'];
            
            // Mantener el más antiguo (primero en la lista ordenada desc)
            $keepDomain = $domains->last(); // El más antiguo
            $deleteDomains = $domains->where('id', '!=', $keepDomain->id);
            
            foreach ($deleteDomains as $domain) {
                $domain->delete(); // Soft delete
                $deletedCount++;
                
                $this->line("\n🗑️  Eliminado: {$domain->dominio} (ID: {$domain->id}) - Creado: {$domain->created_at->format('d/m/Y H:i')}");
            }
            
            $this->line("\n✅ Mantenido: {$keepDomain->dominio} (ID: {$keepDomain->id}) - Creado: {$keepDomain->created_at->format('d/m/Y H:i')}");
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        return $deletedCount;
    }
}
