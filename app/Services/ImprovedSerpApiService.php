<?php

namespace App\Services;

use App\Models\Autoseo\Autoseo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImprovedSerpApiService
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
     * Obtiene datos SEO realistas y útiles para un dominio
     */
    public function getRealisticSeoData($autoseo)
    {
        Log::info("Obteniendo datos SEO realistas", [
            'autoseo_id' => $autoseo->id ?? 'standalone',
            'domain' => $autoseo->url
        ]);

        $domain = $autoseo->url;
        $domainName = $this->extractDomainName($domain);

        // 1. Obtener keywords relevantes basadas en el dominio
        $relevantKeywords = $this->getRelevantKeywords($domainName, $domain);
        
        // 2. Analizar posiciones reales para cada keyword
        $keywordAnalysis = $this->analyzeKeywordPositions($relevantKeywords, $domain);
        
        // 3. Obtener datos de competencia
        $competitorAnalysis = $this->analyzeCompetitors($domainName);
        
        // 4. Obtener sugerencias de mejora
        $improvementSuggestions = $this->getImprovementSuggestions($keywordAnalysis);

        return [
            'domain' => $domain,
            'domain_name' => $domainName,
            'analysis_date' => now()->toDateTimeString(),
            'keywords_analyzed' => count($keywordAnalysis),
            'keyword_analysis' => $keywordAnalysis,
            'competitor_analysis' => $competitorAnalysis,
            'improvement_suggestions' => $improvementSuggestions,
            'summary' => $this->generateSummary($keywordAnalysis, $competitorAnalysis)
        ];
    }

    /**
     * Extrae el nombre del dominio sin extensiones
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

    /**
     * Genera keywords relevantes basadas en el dominio
     */
    private function getRelevantKeywords($domainName, $domain)
    {
        // Keywords básicas del dominio
        $basicKeywords = [
            $domainName,
            $domainName . ' servicios',
            $domainName . ' empresa',
            $domainName . ' contacto',
            $domainName . ' precios',
            $domainName . ' opiniones'
        ];

        // Keywords específicas según el tipo de dominio
        $specificKeywords = $this->getSpecificKeywords($domainName, $domain);
        
        // Keywords de long tail
        $longTailKeywords = $this->getLongTailKeywords($domainName);

        return array_merge($basicKeywords, $specificKeywords, $longTailKeywords);
    }

    /**
     * Genera keywords específicas según el tipo de negocio
     */
    private function getSpecificKeywords($domainName, $domain)
    {
        $keywords = [];
        
        // Detectar tipo de negocio por el dominio
        if (Str::contains($domain, 'autoseo') || Str::contains($domain, 'seo')) {
            $keywords = [
                'SEO ' . $domainName,
                'posicionamiento web ' . $domainName,
                'marketing digital ' . $domainName,
                'optimización SEO',
                'auditoría SEO',
                'consultoría SEO'
            ];
        } elseif (Str::contains($domain, 'arquitectura')) {
            $keywords = [
                'arquitecto ' . $domainName,
                'proyectos arquitectura',
                'diseño arquitectónico',
                'construcción ' . $domainName
            ];
        } elseif (Str::contains($domain, 'gas') || Str::contains($domain, 'energía')) {
            $keywords = [
                'gas natural ' . $domainName,
                'suministro gas',
                'instalaciones gas',
                'energía ' . $domainName
            ];
        } else {
            // Keywords genéricas para otros tipos de negocio
            $keywords = [
                'servicios ' . $domainName,
                'empresa ' . $domainName,
                'profesionales ' . $domainName,
                'expertos ' . $domainName
            ];
        }

        return $keywords;
    }

    /**
     * Genera keywords de long tail
     */
    private function getLongTailKeywords($domainName)
    {
        return [
            'mejor ' . $domainName . ' servicios',
            'como elegir ' . $domainName,
            'precios ' . $domainName . ' 2024',
            'opiniones ' . $domainName . ' clientes',
            'contactar ' . $domainName . ' urgente',
            'donde encontrar ' . $domainName,
            'alternativas a ' . $domainName
        ];
    }

    /**
     * Analiza las posiciones reales de las keywords
     */
    private function analyzeKeywordPositions($keywords, $domain)
    {
        $analysis = [];
        
        foreach ($keywords as $keyword) {
            try {
                $position = $this->searchKeywordPosition($keyword, $domain);
                
                $analysis[] = [
                    'keyword' => $keyword,
                    'position' => $position,
                    'status' => $this->getPositionStatus($position),
                    'traffic_potential' => $this->estimateTrafficPotential($position),
                    'difficulty' => $this->estimateKeywordDifficulty($keyword)
                ];
                
                // Pausa para no sobrecargar la API
                usleep(500000); // 0.5 segundos
                
            } catch (\Exception $e) {
                Log::warning("Error analizando keyword: {$keyword}", [
                    'error' => $e->getMessage()
                ]);
                
                $analysis[] = [
                    'keyword' => $keyword,
                    'position' => null,
                    'status' => 'error',
                    'traffic_potential' => 'unknown',
                    'difficulty' => 'unknown'
                ];
            }
        }
        
        return $analysis;
    }

    /**
     * Busca la posición de una keyword específica
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

        return null; // No encontrado en los primeros 50 resultados
    }

    /**
     * Determina el estado de una posición
     */
    private function getPositionStatus($position)
    {
        if ($position === null) return 'not_found';
        if ($position <= 3) return 'excellent';
        if ($position <= 10) return 'good';
        if ($position <= 20) return 'fair';
        return 'poor';
    }

    /**
     * Estima el potencial de tráfico basado en la posición
     */
    private function estimateTrafficPotential($position)
    {
        if ($position === null) return 'none';
        if ($position <= 3) return 'high';
        if ($position <= 10) return 'medium';
        if ($position <= 20) return 'low';
        return 'minimal';
    }

    /**
     * Estima la dificultad de una keyword
     */
    private function estimateKeywordDifficulty($keyword)
    {
        $wordCount = str_word_count($keyword);
        $hasNumbers = preg_match('/\d/', $keyword);
        
        if ($wordCount <= 2 && !$hasNumbers) return 'high';
        if ($wordCount <= 3) return 'medium';
        return 'low';
    }

    /**
     * Analiza competidores
     */
    private function analyzeCompetitors($domainName)
    {
        try {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->get($this->baseUrl, [
                    'api_key' => $this->apiKey,
                    'q' => $domainName,
                    'engine' => 'google',
                    'gl' => 'es',
                    'hl' => 'es',
                    'num' => 20
                ]);

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();
            $organicResults = $data['organic_results'] ?? [];
            
            $competitors = [];
            foreach (array_slice($organicResults, 0, 5) as $index => $result) {
                $competitors[] = [
                    'position' => $index + 1,
                    'title' => $result['title'] ?? 'Sin título',
                    'url' => $result['link'] ?? '',
                    'snippet' => $result['snippet'] ?? ''
                ];
            }
            
            return $competitors;
            
        } catch (\Exception $e) {
            Log::warning("Error analizando competidores", [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Genera sugerencias de mejora
     */
    private function getImprovementSuggestions($keywordAnalysis)
    {
        $suggestions = [];
        
        $notFoundKeywords = array_filter($keywordAnalysis, fn($k) => $k['position'] === null);
        $poorPositionKeywords = array_filter($keywordAnalysis, fn($k) => $k['position'] > 20);
        
        if (count($notFoundKeywords) > 0) {
            $suggestions[] = [
                'type' => 'critical',
                'title' => 'Keywords no encontradas',
                'description' => 'Tienes ' . count($notFoundKeywords) . ' keywords importantes que no aparecen en los resultados de búsqueda.',
                'action' => 'Optimiza el contenido para estas keywords y crea páginas específicas.'
            ];
        }
        
        if (count($poorPositionKeywords) > 0) {
            $suggestions[] = [
                'type' => 'important',
                'title' => 'Posiciones mejorables',
                'description' => 'Tienes ' . count($poorPositionKeywords) . ' keywords con posiciones bajas que pueden mejorarse.',
                'action' => 'Mejora el contenido existente y aumenta la autoridad del dominio.'
            ];
        }
        
        $excellentKeywords = array_filter($keywordAnalysis, fn($k) => $k['position'] <= 3);
        if (count($excellentKeywords) > 0) {
            $suggestions[] = [
                'type' => 'positive',
                'title' => 'Keywords destacadas',
                'description' => 'Tienes ' . count($excellentKeywords) . ' keywords en posiciones excelentes.',
                'action' => 'Mantén y fortalece estas posiciones con contenido de calidad.'
            ];
        }
        
        return $suggestions;
    }

    /**
     * Genera un resumen ejecutivo
     */
    private function generateSummary($keywordAnalysis, $competitorAnalysis)
    {
        $totalKeywords = count($keywordAnalysis);
        $foundKeywords = count(array_filter($keywordAnalysis, fn($k) => $k['position'] !== null));
        $top10Keywords = count(array_filter($keywordAnalysis, fn($k) => $k['position'] <= 10));
        $top3Keywords = count(array_filter($keywordAnalysis, fn($k) => $k['position'] <= 3));
        
        return [
            'total_keywords_analyzed' => $totalKeywords,
            'keywords_found' => $foundKeywords,
            'keywords_top_10' => $top10Keywords,
            'keywords_top_3' => $top3Keywords,
            'visibility_score' => $this->calculateVisibilityScore($keywordAnalysis),
            'main_competitors' => count($competitorAnalysis)
        ];
    }

    /**
     * Calcula un score de visibilidad
     */
    private function calculateVisibilityScore($keywordAnalysis)
    {
        $score = 0;
        $total = count($keywordAnalysis);
        
        foreach ($keywordAnalysis as $keyword) {
            if ($keyword['position'] === null) continue;
            
            if ($keyword['position'] <= 3) $score += 100;
            elseif ($keyword['position'] <= 10) $score += 70;
            elseif ($keyword['position'] <= 20) $score += 40;
            else $score += 10;
        }
        
        return $total > 0 ? round($score / $total) : 0;
    }
}
