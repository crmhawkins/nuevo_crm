<?php

/**
 * Script simple de prueba para búsqueda de dominios en conceptos de facturas
 * Usa Artisan para ejecutar comandos de Laravel
 */

// Cargar el autoloader de Laravel
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Dominios\Dominio;
use App\Models\Invoices\InvoiceConcepts;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

/**
 * Función para normalizar dominios
 */
function normalizeDomain($domain) {
    $domain = strtolower(trim($domain));
    $domain = preg_replace('/^https?:\/\//', '', $domain);
    $domain = preg_replace('/^www\./', '', $domain);
    return $domain;
}

/**
 * Función para buscar coincidencias flexibles
 */
function findMatches($domain, $text) {
    if (empty($text)) return [];
    
    $matches = [];
    $normalized_domain = normalizeDomain($domain);
    
    // Patrón para encontrar dominios en el texto
    $pattern = '/(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.(?:[a-zA-Z]{2,}))/i';
    preg_match_all($pattern, $text, $matches_found);
    
    if (!empty($matches_found[1])) {
        foreach ($matches_found[1] as $found_domain) {
            $normalized_found = normalizeDomain($found_domain);
            
            // Coincidencia exacta
            if ($normalized_domain === $normalized_found) {
                $matches[] = [
                    'domain' => $found_domain,
                    'type' => 'exact',
                    'score' => 100
                ];
            }
            // Coincidencia parcial
            elseif (strpos($normalized_domain, $normalized_found) !== false || 
                    strpos($normalized_found, $normalized_domain) !== false) {
                $matches[] = [
                    'domain' => $found_domain,
                    'type' => 'partial',
                    'score' => 80
                ];
            }
        }
    }
    
    return $matches;
}

/**
 * Función principal de búsqueda
 */
function searchDomains() {
    echo "🔍 BÚSQUEDA DE DOMINIOS EN CONCEPTOS DE FACTURAS\n";
    echo str_repeat("=", 50) . "\n\n";
    
    try {
        // Obtener algunos dominios de ejemplo
        $dominios = Dominio::take(5)->get();
        echo "📊 Dominios a buscar: " . $dominios->count() . "\n";
        
        // Obtener conceptos de facturas
        $conceptos = InvoiceConcepts::with('invoice')->take(10)->get();
        echo "📊 Conceptos a revisar: " . $conceptos->count() . "\n\n";
        
        $total_matches = 0;
        
        foreach ($dominios as $dominio) {
            echo "🔍 Buscando: " . $dominio->dominio . "\n";
            $domain_matches = 0;
            
            foreach ($conceptos as $concepto) {
                // Buscar en título
                $title_matches = findMatches($dominio->dominio, $concepto->title);
                foreach ($title_matches as $match) {
                    if ($match['score'] >= 80) {
                        echo "  ✅ TÍTULO: " . $match['domain'] . " (Score: " . $match['score'] . "%)\n";
                        echo "     Contenido: " . substr($concepto->title, 0, 50) . "...\n";
                        echo "     Factura ID: " . $concepto->invoice_id . "\n";
                        $domain_matches++;
                        $total_matches++;
                    }
                }
                
                // Buscar en concepto
                $concept_matches = findMatches($dominio->dominio, $concepto->concept);
                foreach ($concept_matches as $match) {
                    if ($match['score'] >= 80) {
                        echo "  ✅ CONCEPTO: " . $match['domain'] . " (Score: " . $match['score'] . "%)\n";
                        echo "     Contenido: " . substr($concepto->concept, 0, 50) . "...\n";
                        echo "     Factura ID: " . $concepto->invoice_id . "\n";
                        $domain_matches++;
                        $total_matches++;
                    }
                }
            }
            
            if ($domain_matches === 0) {
                echo "  ❌ Sin coincidencias\n";
            }
            echo "\n";
        }
        
        echo "🎯 TOTAL DE COINCIDENCIAS: " . $total_matches . "\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

/**
 * Función para probar con un dominio específico
 */
function testSpecificDomain($domain) {
    echo "🧪 PROBANDO DOMINIO: " . $domain . "\n";
    echo str_repeat("=", 40) . "\n\n";
    
    try {
        $conceptos = InvoiceConcepts::with('invoice')->get();
        $matches = 0;
        
        foreach ($conceptos as $concepto) {
            // Buscar en título
            $title_matches = findMatches($domain, $concepto->title);
            foreach ($title_matches as $match) {
                if ($match['score'] >= 80) {
                    echo "✅ TÍTULO: " . $match['domain'] . " (Score: " . $match['score'] . "%)\n";
                    echo "   Contenido: " . substr($concepto->title, 0, 100) . "...\n";
                    echo "   Factura ID: " . $concepto->invoice_id . "\n\n";
                    $matches++;
                }
            }
            
            // Buscar en concepto
            $concept_matches = findMatches($domain, $concepto->concept);
            foreach ($concept_matches as $match) {
                if ($match['score'] >= 80) {
                    echo "✅ CONCEPTO: " . $match['domain'] . " (Score: " . $match['score'] . "%)\n";
                    echo "   Contenido: " . substr($concepto->concept, 0, 100) . "...\n";
                    echo "   Factura ID: " . $concepto->invoice_id . "\n\n";
                    $matches++;
                }
            }
        }
        
        if ($matches === 0) {
            echo "❌ No se encontraron coincidencias para: " . $domain . "\n";
        } else {
            echo "🎯 Total de coincidencias: " . $matches . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

// Ejecutar el script
if (isset($argv[1]) && $argv[1] === 'test') {
    $domain = $argv[2] ?? 'example.com';
    testSpecificDomain($domain);
} else {
    searchDomains();
}

echo "\n🏁 Script finalizado.\n";
