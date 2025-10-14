<?php

namespace App\Services;

use App\Models\Autoseo\Autoseo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RealisticSeoAnalysisService
{
    private $apiKey;
    private $baseUrl = "https://serpapi.com/search";

    public function __construct()
    {
        $this->apiKey = env('SERPAPI_KEY');
        if (!$this->apiKey) {
            throw new \Exception('SERPAPI_KEY no configurada en .env');
        }
    }

    /**
     * Genera análisis SEO realista usando la estructura real de datos históricos
     */
    public function generateRealisticAnalysis($autoseo, $historicalData = [])
    {
        Log::info("Generando análisis SEO realista", [
            'autoseo_id' => $autoseo->id ?? 'standalone',
            'domain' => $autoseo->url,
            'historical_count' => count($historicalData)
        ]);

        $domain = $autoseo->url;
        $domainName = $this->extractDomainName($domain);

        // 1. Obtener keywords relevantes basadas en datos históricos
        $relevantKeywords = $this->extractKeywordsFromHistory($historicalData, $domainName);
        
        // 2. Analizar posiciones actuales
        $currentAnalysis = $this->analyzeCurrentPositions($relevantKeywords, $domain);
        
        // 3. Generar comparación histórica
        $historicalComparison = $this->generateHistoricalComparison($historicalData, $currentAnalysis);
        
        // 4. Generar insights y recomendaciones
        $insights = $this->generateInsights($currentAnalysis, $historicalComparison);

        return [
            'dominio' => $domain,
            'uploaded_at' => now()->toDateTimeString(),
            'short_tail' => $this->categorizeKeywords($currentAnalysis, 'short'),
            'long_tail' => $this->categorizeKeywords($currentAnalysis, 'long'),
            'people_also_ask' => $this->getPeopleAlsoAsk($domainName),
            'detalles_keywords' => $currentAnalysis,
            'historical_comparison' => $historicalComparison,
            'insights' => $insights,
            'summary' => $this->generateSummary($currentAnalysis, $historicalComparison)
        ];
    }

    /**
     * Extrae keywords relevantes de los datos históricos
     */
    private function extractKeywordsFromHistory($historicalData, $domainName)
    {
        $allKeywords = [];
        
        // Recopilar todas las keywords históricas
        foreach ($historicalData as $data) {
            if (isset($data['detalles_keywords'])) {
                foreach ($data['detalles_keywords'] as $keyword) {
                    $allKeywords[] = $keyword['keyword'];
                }
            }
            if (isset($data['short_tail'])) {
                $allKeywords = array_merge($allKeywords, $data['short_tail']);
            }
            if (isset($data['long_tail'])) {
                $allKeywords = array_merge($allKeywords, $data['long_tail']);
            }
        }
        
        // Eliminar duplicados y mantener orden de frecuencia
        $keywordFrequency = array_count_values($allKeywords);
        arsort($keywordFrequency);
        
        // Tomar las keywords más frecuentes (máximo 15)
        $relevantKeywords = array_slice(array_keys($keywordFrequency), 0, 15);
        
        // Si no hay datos históricos, generar keywords básicas
        if (empty($relevantKeywords)) {
            $relevantKeywords = $this->generateBasicKeywords($domainName);
        }
        
        return $relevantKeywords;
    }

    /**
     * Genera keywords básicas cuando no hay historial
     */
    private function generateBasicKeywords($domainName)
    {
        return [
            $domainName,
            $domainName . ' servicios',
            $domainName . ' empresa',
            $domainName . ' contacto',
            $domainName . ' precios',
            $domainName . ' opiniones'
        ];
    }

    /**
     * Analiza las posiciones actuales de las keywords
     */
    private function analyzeCurrentPositions($keywords, $domain)
    {
        $analysis = [];
        
        foreach ($keywords as $keyword) {
            try {
                $position = $this->searchKeywordPosition($keyword, $domain);
                
                $analysis[] = [
                    'keyword' => $keyword,
                    'position' => $position,
                    'total_results' => $this->getTotalResults($keyword)
                ];
                
                // Pausa para no sobrecargar la API
                usleep(300000); // 0.3 segundos
                
            } catch (\Exception $e) {
                Log::warning("Error analizando keyword: {$keyword}", [
                    'error' => $e->getMessage()
                ]);
                
                $analysis[] = [
                    'keyword' => $keyword,
                    'position' => null,
                    'total_results' => null
                ];
            }
        }
        
        return $analysis;
    }

    /**
     * Busca la posición de una keyword
     */
    private function searchKeywordPosition($keyword, $domain)
    {
        $response = Http::timeout(30)
            ->withoutVerifying()
            ->get($this->baseUrl, [
                'api_key' => $this->apiKey,
                'q' => $keyword,
                'engine' => 'google',
                'gl' => 'es',
                'hl' => 'es',
                'num' => 50
            ]);

        if (!$response->successful()) {
            throw new \Exception("Error en búsqueda: " . $response->status());
        }

        $data = $response->json();
        $organicResults = $data['organic_results'] ?? [];

        // Buscar el dominio en los resultados
        foreach ($organicResults as $index => $result) {
            if (isset($result['link']) && Str::contains($result['link'], $domain)) {
                return $index + 1; // Posición (1-based)
            }
        }

        return null; // No encontrado
    }

    /**
     * Obtiene el total de resultados para una keyword
     */
    private function getTotalResults($keyword)
    {
        try {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->get($this->baseUrl, [
                    'api_key' => $this->apiKey,
                    'q' => $keyword,
                    'engine' => 'google',
                    'gl' => 'es',
                    'hl' => 'es',
                    'num' => 10
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['search_information']['total_results'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning("Error obteniendo total de resultados para: {$keyword}");
        }

        return null;
    }

    /**
     * Genera comparación histórica
     */
    private function generateHistoricalComparison($historicalData, $currentAnalysis)
    {
        $comparison = [];
        
        foreach ($currentAnalysis as $currentKeyword) {
            $keyword = $currentKeyword['keyword'];
            $currentPosition = $currentKeyword['position'];
            
            // Buscar en datos históricos
            $historicalPositions = [];
            foreach ($historicalData as $data) {
                if (isset($data['detalles_keywords'])) {
                    foreach ($data['detalles_keywords'] as $histKeyword) {
                        if ($histKeyword['keyword'] === $keyword) {
                            $historicalPositions[] = [
                                'position' => $histKeyword['position'],
                                'date' => $data['uploaded_at'] ?? 'unknown'
                            ];
                        }
                    }
                }
            }
            
            $comparison[] = [
                'keyword' => $keyword,
                'current_position' => $currentPosition,
                'historical_positions' => $historicalPositions,
                'trend' => $this->calculateTrend($currentPosition, $historicalPositions)
            ];
        }
        
        return $comparison;
    }

    /**
     * Calcula la tendencia de una keyword
     */
    private function calculateTrend($currentPosition, $historicalPositions)
    {
        if (empty($historicalPositions)) {
            return 'new';
        }
        
        // Tomar la posición más reciente histórica
        $lastHistorical = end($historicalPositions);
        $lastPosition = $lastHistorical['position'];
        
        if ($currentPosition === null && $lastPosition === null) {
            return 'stable_not_found';
        } elseif ($currentPosition === null) {
            return 'declined';
        } elseif ($lastPosition === null) {
            return 'improved';
        } elseif ($currentPosition < $lastPosition) {
            return 'improved';
        } elseif ($currentPosition > $lastPosition) {
            return 'declined';
        } else {
            return 'stable';
        }
    }

    /**
     * Categoriza keywords en short_tail y long_tail
     */
    private function categorizeKeywords($analysis, $type)
    {
        $keywords = [];
        
        foreach ($analysis as $keyword) {
            $wordCount = str_word_count($keyword['keyword']);
            
            if ($type === 'short' && $wordCount <= 3) {
                $keywords[] = $keyword['keyword'];
            } elseif ($type === 'long' && $wordCount > 3) {
                $keywords[] = $keyword['keyword'];
            }
        }
        
        return $keywords;
    }

    /**
     * Obtiene People Also Ask relevantes
     */
    private function getPeopleAlsoAsk($domainName)
    {
        try {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->get($this->baseUrl, [
                    'api_key' => $this->apiKey,
                    'q' => $domainName,
                    'engine' => 'google',
                    'gl' => 'es',
                    'hl' => 'es'
                ]);

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();
            $paaResults = $data['people_also_ask'] ?? [];

            $formattedPaa = [];
            foreach ($paaResults as $item) {
                if (isset($item['question'])) {
                    $formattedPaa[] = [
                        'question' => $item['question'],
                        'position' => null,
                        'total_results' => null
                    ];
                }
            }
            
            return array_slice($formattedPaa, 0, 8); // Máximo 8 preguntas
            
        } catch (\Exception $e) {
            Log::warning("Error obteniendo People Also Ask", [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Genera insights útiles
     */
    private function generateInsights($currentAnalysis, $historicalComparison)
    {
        $insights = [];
        
        // Análisis de posiciones
        $foundKeywords = array_filter($currentAnalysis, fn($k) => $k['position'] !== null);
        $top10Keywords = array_filter($currentAnalysis, fn($k) => $k['position'] !== null && $k['position'] <= 10);
        $top3Keywords = array_filter($currentAnalysis, fn($k) => $k['position'] !== null && $k['position'] <= 3);
        
        $insights['positions'] = [
            'total_analyzed' => count($currentAnalysis),
            'found' => count($foundKeywords),
            'top_10' => count($top10Keywords),
            'top_3' => count($top3Keywords),
            'visibility_percentage' => count($currentAnalysis) > 0 ? round((count($foundKeywords) / count($currentAnalysis)) * 100) : 0
        ];
        
        // Análisis de tendencias
        $improvedKeywords = array_filter($historicalComparison, fn($k) => $k['trend'] === 'improved');
        $declinedKeywords = array_filter($historicalComparison, fn($k) => $k['trend'] === 'declined');
        $stableKeywords = array_filter($historicalComparison, fn($k) => $k['trend'] === 'stable');
        
        $insights['trends'] = [
            'improved' => count($improvedKeywords),
            'declined' => count($declinedKeywords),
            'stable' => count($stableKeywords),
            'new' => count(array_filter($historicalComparison, fn($k) => $k['trend'] === 'new'))
        ];
        
        return $insights;
    }

    /**
     * Genera resumen ejecutivo
     */
    private function generateSummary($currentAnalysis, $historicalComparison)
    {
        $foundKeywords = array_filter($currentAnalysis, fn($k) => $k['position'] !== null);
        $top10Keywords = array_filter($currentAnalysis, fn($k) => $k['position'] !== null && $k['position'] <= 10);
        
        $improvedKeywords = array_filter($historicalComparison, fn($k) => $k['trend'] === 'improved');
        $declinedKeywords = array_filter($historicalComparison, fn($k) => $k['trend'] === 'declined');
        
        return [
            'total_keywords' => count($currentAnalysis),
            'found_keywords' => count($foundKeywords),
            'top_10_keywords' => count($top10Keywords),
            'improved_keywords' => count($improvedKeywords),
            'declined_keywords' => count($declinedKeywords),
            'visibility_score' => count($currentAnalysis) > 0 ? round((count($foundKeywords) / count($currentAnalysis)) * 100) : 0
        ];
    }

    /**
     * Extrae el nombre del dominio
     */
    private function extractDomainName($domain)
    {
        $domainName = parse_url($domain, PHP_URL_HOST);
        if (!$domainName) {
            $domainName = str_replace(['http://', 'https://', 'www.'], '', $domain);
        }
        $domainName = str_replace(['www.', '.com', '.es', '.net', '.org', '.ai'], '', $domainName);
        
        if (empty($domainName)) {
            $domainName = $domain;
        }
        
        return $domainName;
    }
}
