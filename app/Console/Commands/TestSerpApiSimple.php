<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SerpApiService;

class TestSerpApiSimple extends Command
{
    protected $signature = 'seo:test-serpapi-simple';
    protected $description = 'Prueba la conexión con SerpAPI sin base de datos';

    public function handle()
    {
        $this->info("🔍 Probando conexión con SerpAPI (sin base de datos)...");

        // Verificar que la API key esté configurada
        if (!env('SERPAPI_KEY')) {
            $this->error("❌ SERPAPI_KEY no configurada en .env");
            $this->info("💡 Añade SERPAPI_KEY=tu_clave_aqui en tu archivo .env");
            return 1;
        }

        $this->info("✅ API Key configurada: " . substr(env('SERPAPI_KEY'), 0, 10) . "...");

        try {
            $serpApiService = new SerpApiService();
            
            // Crear un objeto Autoseo simulado
            $mockAutoseo = new \stdClass();
            $mockAutoseo->id = 11;
            $mockAutoseo->url = 'hawkins.es';
            $mockAutoseo->client_name = 'Hawkins Test';
            
            $this->info("📊 Probando con dominio: {$mockAutoseo->url}");
            
            // Probar la conexión directamente con SerpAPI
            $this->info("🌐 Haciendo petición a SerpAPI...");
            
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withoutVerifying()
                ->get('https://serpapi.com/search', [
                    'api_key' => env('SERPAPI_KEY'),
                    'q' => 'hawkins.es',
                    'engine' => 'google',
                    'gl' => 'es',
                    'hl' => 'es',
                    'num' => 10
                ]);

            if (!$response->successful()) {
                $this->error("❌ Error en la petición a SerpAPI");
                $this->error("Status: " . $response->status());
                $this->error("Response: " . substr($response->body(), 0, 200));
                return 1;
            }

            $data = $response->json();
            
            $this->info("✅ Conexión exitosa con SerpAPI");
            $this->info("📈 Datos obtenidos:");
            $this->info("   - Query: " . ($data['search_parameters']['q'] ?? 'N/A'));
            $this->info("   - Engine: " . ($data['search_parameters']['engine'] ?? 'N/A'));
            $this->info("   - Resultados orgánicos: " . count($data['organic_results'] ?? []));
            
            if (!empty($data['organic_results'])) {
                $this->info("🔍 Primeros resultados:");
                foreach (array_slice($data['organic_results'], 0, 3) as $index => $result) {
                    $position = $index + 1;
                    $title = $result['title'] ?? 'Sin título';
                    $link = $result['link'] ?? 'Sin enlace';
                    $this->info("   {$position}. {$title}");
                    $this->info("      {$link}");
                }
            }

            if (!empty($data['related_searches'])) {
                $this->info("🔍 Búsquedas relacionadas: " . count($data['related_searches']));
            }

            if (!empty($data['related_questions'])) {
                $this->info("❓ Preguntas relacionadas: " . count($data['related_questions']));
            }

            $this->info("🎉 ¡Prueba exitosa! SerpAPI está funcionando correctamente.");
            $this->info("💡 Ahora puedes usar: php artisan seo:generate-with-current-data 11");

        } catch (\Exception $e) {
            $this->error("❌ Error en la prueba: " . $e->getMessage());
            $this->info("🔧 Verifica que la API key sea correcta y que tengas créditos en SerpAPI");
            return 1;
        }

        return 0;
    }
}
