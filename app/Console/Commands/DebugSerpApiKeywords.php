<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SerpApiService;
use Illuminate\Support\Facades\Http;

class DebugSerpApiKeywords extends Command
{
    protected $signature = 'seo:debug-keywords {domain : Dominio a analizar}';
    protected $description = 'Debug del procesamiento de keywords de SerpAPI';

    public function handle()
    {
        $domain = $this->argument('domain');

        $this->info("🔍 Debug de keywords para: {$domain}");

        try {
            // Verificar configuración de SerpAPI
            if (!env('SERPAPI_KEY')) {
                $this->error("❌ SERPAPI_KEY no configurada en .env");
                return 1;
            }

            $this->info("✅ API Key configurada");

            // Crear objeto Autoseo simulado
            $mockAutoseo = new \stdClass();
            $mockAutoseo->id = 1;
            $mockAutoseo->url = $domain;
            $mockAutoseo->client_name = ucfirst($domain);

            $this->info("📊 Obteniendo datos de SerpAPI...");

            // Obtener datos de SerpAPI
            $serpApiService = new SerpApiService();
            $currentData = $serpApiService->getCurrentData($mockAutoseo);

            $this->info("📈 Datos obtenidos:");
            $this->info("   - Keywords: " . count($currentData['detalles_keywords'] ?? []));
            $this->info("   - Short tail: " . count($currentData['short_tail'] ?? []));
            $this->info("   - Long tail: " . count($currentData['long_tail'] ?? []));
            $this->info("   - PAA: " . count($currentData['people_also_ask'] ?? []));

            // Mostrar detalles de keywords
            if (!empty($currentData['detalles_keywords'])) {
                $this->info("🔍 Detalles de keywords:");
                foreach ($currentData['detalles_keywords'] as $keyword) {
                    $position = $keyword['position'] ? "Posición {$keyword['position']}" : "No encontrado";
                    $this->info("   - {$keyword['keyword']}: {$position}");
                }
            } else {
                $this->warn("⚠️ No se encontraron keywords procesadas");
            }

            // Mostrar short tail
            if (!empty($currentData['short_tail'])) {
                $this->info("🔤 Short tail keywords:");
                foreach ($currentData['short_tail'] as $keyword) {
                    $this->info("   - {$keyword}");
                }
            }

            // Mostrar long tail
            if (!empty($currentData['long_tail'])) {
                $this->info("📝 Long tail keywords:");
                foreach ($currentData['long_tail'] as $keyword) {
                    $this->info("   - {$keyword}");
                }
            }

            // Mostrar PAA
            if (!empty($currentData['people_also_ask'])) {
                $this->info("❓ People Also Ask:");
                foreach ($currentData['people_also_ask'] as $question) {
                    $this->info("   - {$question['question']}");
                }
            }

            // Debug: Probar búsqueda individual
            $this->info("🔍 Probando búsqueda individual...");
            $testKeyword = "hawkins servicios";
            $this->info("Probando keyword: {$testKeyword}");

            $response = Http::timeout(30)
                ->withoutVerifying()
                ->get('https://serpapi.com/search', [
                    'api_key' => env('SERPAPI_KEY'),
                    'q' => $testKeyword,
                    'engine' => 'google',
                    'gl' => 'es',
                    'hl' => 'es',
                    'num' => 100
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info("✅ Búsqueda exitosa");
                $this->info("   - Total resultados: " . ($data['search_information']['total_results'] ?? 'N/A'));
                $this->info("   - Resultados orgánicos: " . count($data['organic_results'] ?? []));

                // Buscar el dominio en los resultados
                $found = false;
                foreach ($data['organic_results'] ?? [] as $index => $result) {
                    $position = $index + 1;
                    $link = $result['link'] ?? '';
                    $title = $result['title'] ?? '';
                    
                    if (strpos($link, $domain) !== false) {
                        $this->info("🎯 ¡Dominio encontrado en posición {$position}!");
                        $this->info("   - Título: {$title}");
                        $this->info("   - Enlace: {$link}");
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $this->warn("⚠️ Dominio no encontrado en los primeros " . count($data['organic_results'] ?? []) . " resultados");
                    $this->info("🔍 Primeros 5 resultados:");
                    foreach (array_slice($data['organic_results'] ?? [], 0, 5) as $index => $result) {
                        $position = $index + 1;
                        $title = $result['title'] ?? 'Sin título';
                        $link = $result['link'] ?? 'Sin enlace';
                        $this->info("   {$position}. {$title}");
                        $this->info("      {$link}");
                    }
                }
            } else {
                $this->error("❌ Error en búsqueda individual: " . $response->status());
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
