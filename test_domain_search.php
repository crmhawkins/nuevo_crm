<?php

/**
 * Script de prueba para búsqueda flexible de dominios en conceptos de facturas
 * 
 * Este script busca dominios en los campos 'title' y 'concept' de la tabla invoice_concepts
 * usando búsqueda flexible (no estricta) para encontrar coincidencias parciales.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\Dominios\Dominio;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\Invoice;

// Configuración de la base de datos
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

/**
 * Función para limpiar y normalizar un dominio
 */
function normalizeDomain($domain) {
    // Remover protocolos (http://, https://, www.)
    $domain = preg_replace('/^https?:\/\//', '', $domain);
    $domain = preg_replace('/^www\./', '', $domain);
    
    // Convertir a minúsculas
    $domain = strtolower($domain);
    
    // Remover espacios en blanco
    $domain = trim($domain);
    
    return $domain;
}

/**
 * Función para extraer dominios de un texto
 */
function extractDomainsFromText($text) {
    if (empty($text)) return [];
    
    $domains = [];
    
    // Patrón para encontrar dominios (más flexible)
    $pattern = '/(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.(?:[a-zA-Z]{2,}))/i';
    
    preg_match_all($pattern, $text, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $match) {
            $normalized = normalizeDomain($match);
            if (!in_array($normalized, $domains)) {
                $domains[] = $normalized;
            }
        }
    }
    
    return $domains;
}

/**
 * Función para buscar coincidencias flexibles entre dominios
 */
function findFlexibleMatches($domain1, $domain2) {
    $d1 = normalizeDomain($domain1);
    $d2 = normalizeDomain($domain2);
    
    // Coincidencia exacta
    if ($d1 === $d2) {
        return ['type' => 'exact', 'score' => 100];
    }
    
    // Coincidencia sin www
    $d1_no_www = preg_replace('/^www\./', '', $d1);
    $d2_no_www = preg_replace('/^www\./', '', $d2);
    if ($d1_no_www === $d2_no_www) {
        return ['type' => 'no_www', 'score' => 95];
    }
    
    // Coincidencia de subdominio
    if (strpos($d1, $d2) !== false || strpos($d2, $d1) !== false) {
        return ['type' => 'subdomain', 'score' => 80];
    }
    
    // Coincidencia parcial (contiene)
    if (strpos($d1, $d2) !== false || strpos($d2, $d1) !== false) {
        return ['type' => 'partial', 'score' => 60];
    }
    
    // Coincidencia de palabras clave
    $d1_parts = explode('.', $d1);
    $d2_parts = explode('.', $d2);
    
    $common_parts = array_intersect($d1_parts, $d2_parts);
    if (count($common_parts) > 0) {
        $score = (count($common_parts) / max(count($d1_parts), count($d2_parts))) * 50;
        return ['type' => 'keyword', 'score' => $score];
    }
    
    return null;
}

/**
 * Función principal de búsqueda
 */
function searchDomainsInInvoices() {
    echo "🔍 Iniciando búsqueda de dominios en conceptos de facturas...\n\n";
    
    // Obtener todos los dominios
    $dominios = Dominio::all();
    echo "📊 Total de dominios encontrados: " . $dominios->count() . "\n\n";
    
    // Obtener todos los conceptos de facturas
    $conceptos = InvoiceConcepts::with('invoice')->get();
    echo "📊 Total de conceptos de facturas encontrados: " . $conceptos->count() . "\n\n";
    
    $matches = [];
    $total_checked = 0;
    
    foreach ($dominios as $dominio) {
        echo "🔍 Buscando coincidencias para: " . $dominio->dominio . "\n";
        
        foreach ($conceptos as $concepto) {
            $total_checked++;
            
            // Buscar en título
            $title_domains = extractDomainsFromText($concepto->title);
            foreach ($title_domains as $title_domain) {
                $match = findFlexibleMatches($dominio->dominio, $title_domain);
                if ($match && $match['score'] >= 60) {
                    $matches[] = [
                        'dominio' => $dominio->dominio,
                        'concepto_dominio' => $title_domain,
                        'concepto_id' => $concepto->id,
                        'invoice_id' => $concepto->invoice_id,
                        'campo' => 'title',
                        'contenido' => $concepto->title,
                        'match_type' => $match['type'],
                        'score' => $match['score']
                    ];
                }
            }
            
            // Buscar en concepto
            $concept_domains = extractDomainsFromText($concepto->concept);
            foreach ($concept_domains as $concept_domain) {
                $match = findFlexibleMatches($dominio->dominio, $concept_domain);
                if ($match && $match['score'] >= 60) {
                    $matches[] = [
                        'dominio' => $dominio->dominio,
                        'concepto_dominio' => $concept_domain,
                        'concepto_id' => $concepto->id,
                        'invoice_id' => $concepto->invoice_id,
                        'campo' => 'concept',
                        'contenido' => $concepto->concept,
                        'match_type' => $match['type'],
                        'score' => $match['score']
                    ];
                }
            }
        }
    }
    
    echo "\n📊 Total de comparaciones realizadas: " . $total_checked . "\n";
    echo "🎯 Coincidencias encontradas: " . count($matches) . "\n\n";
    
    // Mostrar resultados
    if (!empty($matches)) {
        echo "✅ COINCIDENCIAS ENCONTRADAS:\n";
        echo str_repeat("=", 80) . "\n";
        
        foreach ($matches as $match) {
            echo "🏷️  Dominio: " . $match['dominio'] . "\n";
            echo "🔗 Concepto Dominio: " . $match['concepto_dominio'] . "\n";
            echo "📄 Campo: " . $match['campo'] . "\n";
            echo "🎯 Tipo de coincidencia: " . $match['match_type'] . "\n";
            echo "📊 Puntuación: " . $match['score'] . "%\n";
            echo "🆔 Concepto ID: " . $match['concepto_id'] . "\n";
            echo "🧾 Factura ID: " . $match['invoice_id'] . "\n";
            echo "📝 Contenido: " . substr($match['contenido'], 0, 100) . "...\n";
            echo str_repeat("-", 80) . "\n";
        }
    } else {
        echo "❌ No se encontraron coincidencias.\n";
    }
    
    return $matches;
}

/**
 * Función para probar con un dominio específico
 */
function testSpecificDomain($domainToTest) {
    echo "🧪 Probando búsqueda para: " . $domainToTest . "\n\n";
    
    $conceptos = InvoiceConcepts::with('invoice')->get();
    $matches = [];
    
    foreach ($conceptos as $concepto) {
        // Buscar en título
        $title_domains = extractDomainsFromText($concepto->title);
        foreach ($title_domains as $title_domain) {
            $match = findFlexibleMatches($domainToTest, $title_domain);
            if ($match && $match['score'] >= 60) {
                $matches[] = [
                    'dominio' => $domainToTest,
                    'concepto_dominio' => $title_domain,
                    'concepto_id' => $concepto->id,
                    'invoice_id' => $concepto->invoice_id,
                    'campo' => 'title',
                    'contenido' => $concepto->title,
                    'match_type' => $match['type'],
                    'score' => $match['score']
                ];
            }
        }
        
        // Buscar en concepto
        $concept_domains = extractDomainsFromText($concepto->concept);
        foreach ($concept_domains as $concept_domain) {
            $match = findFlexibleMatches($domainToTest, $concept_domain);
            if ($match && $match['score'] >= 60) {
                $matches[] = [
                    'dominio' => $domainToTest,
                    'concepto_dominio' => $concept_domain,
                    'concepto_id' => $concepto->id,
                    'invoice_id' => $concepto->invoice_id,
                    'campo' => 'concept',
                    'contenido' => $concepto->concept,
                    'match_type' => $match['type'],
                    'score' => $match['score']
                ];
            }
        }
    }
    
    echo "🎯 Coincidencias encontradas: " . count($matches) . "\n\n";
    
    if (!empty($matches)) {
        echo "✅ COINCIDENCIAS ENCONTRADAS:\n";
        echo str_repeat("=", 80) . "\n";
        
        foreach ($matches as $match) {
            echo "🏷️  Dominio: " . $match['dominio'] . "\n";
            echo "🔗 Concepto Dominio: " . $match['concepto_dominio'] . "\n";
            echo "📄 Campo: " . $match['campo'] . "\n";
            echo "🎯 Tipo de coincidencia: " . $match['match_type'] . "\n";
            echo "📊 Puntuación: " . $match['score'] . "%\n";
            echo "🆔 Concepto ID: " . $match['concepto_id'] . "\n";
            echo "🧾 Factura ID: " . $match['invoice_id'] . "\n";
            echo "📝 Contenido: " . substr($match['contenido'], 0, 100) . "...\n";
            echo str_repeat("-", 80) . "\n";
        }
    } else {
        echo "❌ No se encontraron coincidencias para este dominio.\n";
    }
    
    return $matches;
}

// Ejecutar el script
try {
    echo "🚀 SCRIPT DE BÚSQUEDA DE DOMINIOS EN CONCEPTOS DE FACTURAS\n";
    echo str_repeat("=", 60) . "\n\n";
    
    // Verificar argumentos de línea de comandos
    if (isset($argv[1]) && $argv[1] === 'test') {
        // Modo de prueba con dominio específico
        $domainToTest = $argv[2] ?? 'example.com';
        testSpecificDomain($domainToTest);
    } else {
        // Modo completo - buscar todos los dominios
        searchDomainsInInvoices();
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Script finalizado.\n";
