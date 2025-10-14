<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SerpApiService;
use App\Models\Autoseo\Autoseo;

class TestSerpApiConnection extends Command
{
    protected $signature = 'seo:test-serpapi {id? : ID del cliente para probar (opcional)}';
    protected $description = 'Prueba la conexión con SerpAPI y obtiene datos de muestra';

    public function handle()
    {
        $this->info("🔍 Probando conexión con SerpAPI...");

        // Verificar que la API key esté configurada
        if (!env('SERPAPI_KEY')) {
            $this->error("❌ SERPAPI_KEY no configurada en .env");
            $this->info("💡 Añade SERPAPI_KEY=tu_clave_aqui en tu archivo .env");
            return 1;
        }

        $this->info("✅ API Key configurada: " . substr(env('SERPAPI_KEY'), 0, 10) . "...");

        try {
            $serpApiService = new SerpApiService();
            
            // Si se proporciona un ID, usar ese cliente
            $id = $this->argument('id');
            if ($id) {
                $autoseo = Autoseo::find($id);
                if (!$autoseo) {
                    $this->error("❌ Cliente con ID {$id} no encontrado");
                    return 1;
                }
                
                $this->info("📊 Probando con cliente: {$autoseo->client_name} ({$autoseo->url})");
                $data = $serpApiService->getCurrentData($autoseo);
            } else {
                // Usar un cliente de prueba
                $autoseo = Autoseo::first();
                if (!$autoseo) {
                    $this->error("❌ No hay clientes Autoseo en la base de datos");
                    return 1;
                }
                
                $this->info("📊 Probando con primer cliente: {$autoseo->client_name} ({$autoseo->url})");
                $data = $serpApiService->getCurrentData($autoseo);
            }

            // Mostrar resultados
            $this->info("✅ Conexión exitosa con SerpAPI");
            $this->info("📈 Datos obtenidos:");
            $this->info("   - Dominio: " . ($data['dominio'] ?? 'N/A'));
            $this->info("   - Keywords encontradas: " . count($data['detalles_keywords'] ?? []));
            $this->info("   - Short tail: " . count($data['short_tail'] ?? []));
            $this->info("   - Long tail: " . count($data['long_tail'] ?? []));
            $this->info("   - People Also Ask: " . count($data['people_also_ask'] ?? []));
            $this->info("   - Fecha: " . ($data['uploaded_at'] ?? 'N/A'));

            // Mostrar algunas keywords de ejemplo
            if (!empty($data['detalles_keywords'])) {
                $this->info("🔍 Keywords de ejemplo:");
                foreach (array_slice($data['detalles_keywords'], 0, 3) as $keyword) {
                    $position = $keyword['position'] ? "Posición {$keyword['position']}" : "No encontrado";
                    $this->info("   - {$keyword['keyword']}: {$position}");
                }
            }

            $this->info("🎉 ¡Prueba exitosa! El sistema está listo para generar informes.");

        } catch (\Exception $e) {
            $this->error("❌ Error en la prueba: " . $e->getMessage());
            $this->info("🔧 Verifica que la API key sea correcta y que tengas créditos en SerpAPI");
            return 1;
        }

        return 0;
    }
}
