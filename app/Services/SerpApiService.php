<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Autoseo\Autoseo;

class SerpApiService
{
    private $apiKey;
    private $baseUrl = 'https://serpapi.com/search';

    public function __construct()
    {
        $this->apiKey = env('SERPAPI_KEY');
        if (!$this->apiKey) {
            throw new \Exception('SERPAPI_KEY no configurada en .env');
        }
    }

    /**
     * Obtiene datos actuales de SerpAPI para un dominio
     */
    public function getCurrentData($autoseo)
    {
        try {
            Log::info("Obteniendo datos actuales de SerpAPI", [
                'autoseo_id' => $autoseo->id ?? 'standalone',
                'domain' => $autoseo->url
            ]);

            // Obtener keywords principales del dominio
            $keywords = $this->extractKeywordsFromDomain($autoseo->url);
            
            $allData = [
                'dominio' => $autoseo->url,
                'uploaded_at' => now()->toDateTimeString(),
                'detalles_keywords' => [],
                'short_tail' => [],
                'long_tail' => [],
                'people_also_ask' => [],
                'monthly_performance' => []
            ];

            // Procesar cada keyword
            foreach ($keywords as $keyword) {
                $keywordData = $this->searchKeyword($keyword, $autoseo->url);
                if ($keywordData) {
                    $allData['detalles_keywords'][] = $keywordData;
                    
                    // Clasificar como short tail o long tail
                    if (str_word_count($keyword) <= 2) {
                        $allData['short_tail'][] = $keyword;
                    } else {
                        $allData['long_tail'][] = $keyword;
                    }
                }
            }

            // Obtener People Also Ask
            $paaData = $this->getPeopleAlsoAsk($autoseo->url);
            $allData['people_also_ask'] = $paaData;

            // Obtener datos de Search Console (si están disponibles)
            $scData = $this->getSearchConsoleData($autoseo);
            $allData['monthly_performance'] = $scData;

            Log::info("Datos de SerpAPI obtenidos correctamente", [
                'autoseo_id' => $autoseo->id,
                'keywords_count' => count($allData['detalles_keywords']),
                'short_tail_count' => count($allData['short_tail']),
                'long_tail_count' => count($allData['long_tail']),
                'paa_count' => count($allData['people_also_ask'])
            ]);

            return $allData;

        } catch (\Exception $e) {
            Log::error("Error obteniendo datos de SerpAPI", [
                'autoseo_id' => $autoseo->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Busca una keyword específica en SerpAPI
     */
    private function searchKeyword($keyword, $domain)
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
                    'num' => 100
                ]);

            if (!$response->successful()) {
                Log::warning("Error en búsqueda de SerpAPI", [
                    'keyword' => $keyword,
                    'status' => $response->status()
                ]);
                return null;
            }

            $data = $response->json();
            
            // Buscar el dominio en los resultados orgánicos
            $position = null;
            $totalResults = $data['search_information']['total_results'] ?? null;

            foreach ($data['organic_results'] ?? [] as $index => $result) {
                if (isset($result['link']) && strpos($result['link'], $domain) !== false) {
                    $position = $index + 1;
                    break;
                }
            }

            return [
                'keyword' => $keyword,
                'position' => $position,
                'total_results' => $totalResults,
                'url' => $domain
            ];

        } catch (\Exception $e) {
            Log::error("Error en búsqueda de keyword", [
                'keyword' => $keyword,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obtiene People Also Ask para un dominio
     */
    private function getPeopleAlsoAsk($domain)
    {
        try {
            // Usar una búsqueda genérica para obtener PAA
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->get($this->baseUrl, [
                    'api_key' => $this->apiKey,
                    'q' => $domain,
                    'engine' => 'google',
                    'gl' => 'es',
                    'hl' => 'es'
                ]);

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();
            $paaData = [];

            foreach ($data['related_questions'] ?? [] as $question) {
                $paaData[] = [
                    'question' => $question['question'],
                    'position' => $question['position'] ?? null,
                    'total_results' => $question['total_results'] ?? null
                ];
            }

            return $paaData;

        } catch (\Exception $e) {
            Log::error("Error obteniendo PAA", [
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Extrae keywords principales de un dominio
     */
    private function extractKeywordsFromDomain($domain)
    {
        // Keywords básicas basadas en el dominio
        $domainName = parse_url($domain, PHP_URL_HOST);
        if (!$domainName) {
            $domainName = str_replace(['http://', 'https://', 'www.'], '', $domain);
        }
        $domainName = str_replace(['www.', '.com', '.es', '.net', '.org'], '', $domainName);
        
        // Si el domainName está vacío, usar el dominio completo
        if (empty($domainName)) {
            $domainName = $domain;
        }
        
        $baseKeywords = [
            $domainName,
            $domainName . ' servicios',
            $domainName . ' empresa',
            $domainName . ' contacto',
            $domainName . ' información',
            $domainName . ' marketing',
            $domainName . ' publicidad',
            $domainName . ' diseño web',
            $domainName . ' desarrollo web',
            $domainName . ' SEO'
        ];

        // También hacer una búsqueda genérica para obtener más keywords
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

            if ($response->successful()) {
                $data = $response->json();
                
                // Añadir keywords de búsquedas relacionadas
                foreach ($data['related_searches'] ?? [] as $related) {
                    if (isset($related['query'])) {
                        $baseKeywords[] = $related['query'];
                    }
                }
                
                // Añadir keywords de sugerencias
                foreach ($data['search_information']['suggested_searches'] ?? [] as $suggestion) {
                    if (isset($suggestion['query'])) {
                        $baseKeywords[] = $suggestion['query'];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Error obteniendo keywords adicionales", [
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
        }
        
        return array_unique($baseKeywords);
    }

    /**
     * Obtiene datos de Search Console (placeholder)
     */
    private function getSearchConsoleData($autoseo)
    {
        // Placeholder - aquí integrarías con Google Search Console API
        // Por ahora retornamos datos vacíos
        return [];
    }
}
