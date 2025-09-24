<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\Invoice;

class SearchDomainsInInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:search-in-invoices 
                            {--domain= : Dominio específico a buscar}
                            {--limit=10 : Límite de dominios a procesar}
                            {--concept-limit=50 : Límite de conceptos a revisar}
                            {--score=80 : Puntuación mínima para mostrar coincidencias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca dominios en los conceptos de facturas usando búsqueda flexible';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 BÚSQUEDA DE DOMINIOS EN CONCEPTOS DE FACTURAS');
        $this->line(str_repeat('=', 50));
        
        $domain = $this->option('domain');
        $limit = (int) $this->option('limit');
        $conceptLimit = (int) $this->option('concept-limit');
        $minScore = (int) $this->option('score');
        
        if ($domain) {
            $this->searchSpecificDomain($domain, $conceptLimit, $minScore);
        } else {
            $this->searchAllDomains($limit, $conceptLimit, $minScore);
        }
    }
    
    /**
     * Buscar un dominio específico
     */
    private function searchSpecificDomain($domain, $conceptLimit, $minScore)
    {
        $this->info("🧪 Buscando coincidencias para: {$domain}");
        $this->line(str_repeat('-', 40));
        
        $conceptos = InvoiceConcepts::with('invoice')
            ->limit($conceptLimit)
            ->get();
            
        $this->info("📊 Revisando {$conceptos->count()} conceptos...");
        
        $matches = 0;
        $progressBar = $this->output->createProgressBar($conceptos->count());
        
        foreach ($conceptos as $concepto) {
            $progressBar->advance();
            
            // Buscar en título
            $titleMatches = $this->findMatches($domain, $concepto->title);
            foreach ($titleMatches as $match) {
                if ($match['score'] >= $minScore) {
                    $this->newLine();
                    $this->line("✅ TÍTULO: {$match['domain']} (Score: {$match['score']}%)");
                    $this->line("   Contenido: " . substr($concepto->title, 0, 100) . "...");
                    $this->line("   Factura ID: {$concepto->invoice_id}");
                    $matches++;
                }
            }
            
            // Buscar en concepto
            $conceptMatches = $this->findMatches($domain, $concepto->concept);
            foreach ($conceptMatches as $match) {
                if ($match['score'] >= $minScore) {
                    $this->newLine();
                    $this->line("✅ CONCEPTO: {$match['domain']} (Score: {$match['score']}%)");
                    $this->line("   Contenido: " . substr($concepto->concept, 0, 100) . "...");
                    $this->line("   Factura ID: {$concepto->invoice_id}");
                    $matches++;
                }
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        if ($matches === 0) {
            $this->warn("❌ No se encontraron coincidencias para: {$domain}");
        } else {
            $this->info("🎯 Total de coincidencias: {$matches}");
        }
    }
    
    /**
     * Buscar todos los dominios
     */
    private function searchAllDomains($limit, $conceptLimit, $minScore)
    {
        $dominios = Dominio::limit($limit)->get();
        $this->info("📊 Dominios a buscar: {$dominios->count()}");
        
        $conceptos = InvoiceConcepts::with('invoice')
            ->limit($conceptLimit)
            ->get();
        $this->info("📊 Conceptos a revisar: {$conceptos->count()}");
        $this->newLine();
        
        $totalMatches = 0;
        $progressBar = $this->output->createProgressBar($dominios->count());
        
        foreach ($dominios as $dominio) {
            $progressBar->advance();
            $domainMatches = 0;
            
            foreach ($conceptos as $concepto) {
                // Buscar en título
                $titleMatches = $this->findMatches($dominio->dominio, $concepto->title);
                foreach ($titleMatches as $match) {
                    if ($match['score'] >= $minScore) {
                        $this->newLine();
                        $this->line("✅ {$dominio->dominio} → TÍTULO: {$match['domain']} (Score: {$match['score']}%)");
                        $this->line("   Factura ID: {$concepto->invoice_id}");
                        $domainMatches++;
                        $totalMatches++;
                    }
                }
                
                // Buscar en concepto
                $conceptMatches = $this->findMatches($dominio->dominio, $concepto->concept);
                foreach ($conceptMatches as $match) {
                    if ($match['score'] >= $minScore) {
                        $this->newLine();
                        $this->line("✅ {$dominio->dominio} → CONCEPTO: {$match['domain']} (Score: {$match['score']}%)");
                        $this->line("   Factura ID: {$concepto->invoice_id}");
                        $domainMatches++;
                        $totalMatches++;
                    }
                }
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        $this->info("🎯 TOTAL DE COINCIDENCIAS: {$totalMatches}");
    }
    
    /**
     * Función para normalizar dominios
     */
    private function normalizeDomain($domain)
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        return $domain;
    }
    
    /**
     * Función para buscar coincidencias flexibles
     */
    private function findMatches($domain, $text)
    {
        if (empty($text)) return [];
        
        $matches = [];
        $normalizedDomain = $this->normalizeDomain($domain);
        
        // Patrón para encontrar dominios en el texto
        $pattern = '/(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.(?:[a-zA-Z]{2,}))/i';
        preg_match_all($pattern, $text, $matchesFound);
        
        if (!empty($matchesFound[1])) {
            foreach ($matchesFound[1] as $foundDomain) {
                $normalizedFound = $this->normalizeDomain($foundDomain);
                
                // Coincidencia exacta
                if ($normalizedDomain === $normalizedFound) {
                    $matches[] = [
                        'domain' => $foundDomain,
                        'type' => 'exact',
                        'score' => 100
                    ];
                }
                // Coincidencia parcial
                elseif (strpos($normalizedDomain, $normalizedFound) !== false || 
                        strpos($normalizedFound, $normalizedDomain) !== false) {
                    $matches[] = [
                        'domain' => $foundDomain,
                        'type' => 'partial',
                        'score' => 80
                    ];
                }
            }
        }
        
        return $matches;
    }
}
