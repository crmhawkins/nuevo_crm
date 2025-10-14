<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Autoseo\AutoseoReportsGen;
use App\Models\Autoseo\Autoseo;
use App\Services\SerpApiService;

class GenerateSeoReportWithCurrentData extends Command
{
    protected $signature = 'seo:generate-with-current-data {id? : ID del cliente (opcional, por defecto 15)}';
    protected $description = 'Genera un informe SEO combinando datos históricos con datos actuales de SerpAPI';

    public function handle()
    {
        $id = $this->argument('id') ?? 15;

        $this->info("🔍 Generando informe SEO con datos actuales para ID: {$id}");

        try {
            // Verificar que el cliente existe
            $autoseo = Autoseo::find($id);
            if (!$autoseo) {
                $this->error("❌ Cliente con ID {$id} no encontrado");
                return 1;
            }

            $this->info("📊 Cliente encontrado: {$autoseo->client_name} ({$autoseo->url})");

            // Verificar configuración de SerpAPI
            if (!env('SERPAPI_KEY')) {
                $this->error("❌ SERPAPI_KEY no configurada en .env");
                $this->info("💡 Añade SERPAPI_KEY=tu_clave_aqui en tu archivo .env");
                return 1;
            }

            $this->info("✅ Configuración de SerpAPI verificada");

            // Generar informe
            $controller = new AutoseoReportsGen();
            $result = $controller->generateReportFromCommand($id);

            if ($result->getStatusCode() === 200) {
                $data = json_decode($result->getContent(), true);
                $this->info("✅ Informe generado correctamente");
                $this->info("📁 Archivo: {$data['filename']}");
                $this->info("📧 Informe enviado al servidor");
                $this->info("💾 Datos actuales almacenados para próximo mes");
            } else {
                $this->error("❌ Error al generar el informe");
                $this->error($result->getContent());
            }

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
