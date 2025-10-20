<?php

namespace App\Console\Commands;

use App\Models\Autoseo\Autoseo;
use App\Http\Controllers\Autoseo\AutoseoReports;
use App\Http\Controllers\Autoseo\AutoseoAdvancedReports;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyAutoseoReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoseo:generate-monthly-reports {--client_id= : ID específico del cliente (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera informes SEO mensuales para todos los clientes de AutoSEO basándose en sus JSONs históricos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando generación de informes SEO mensuales...');
        $this->newLine();

        // Aumentar tiempo de ejecución
        set_time_limit(0);

        // Obtener clientes
        $clientId = $this->option('client_id');
        
        if ($clientId) {
            $clients = Autoseo::where('id', $clientId)->get();
            if ($clients->isEmpty()) {
                $this->error("❌ No se encontró el cliente con ID: {$clientId}");
                return 1;
            }
        } else {
            $clients = Autoseo::all();
        }

        $this->info("📊 Total de clientes a procesar: " . $clients->count());
        $this->newLine();

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        $progressBar = $this->output->createProgressBar($clients->count());
        $progressBar->start();

        foreach ($clients as $client) {
            $progressBar->advance();

            // Verificar si el cliente tiene JSONs almacenados
            $jsonStorage = $client->json_storage ? json_decode($client->json_storage, true) : [];
            
            if (empty($jsonStorage)) {
                $this->newLine();
                $this->warn("⏭️  Cliente #{$client->id} ({$client->client_name}): Sin JSONs históricos - OMITIDO");
                $skippedCount++;
                continue;
            }

            $this->newLine();
            $this->info("🔍 Procesando: #{$client->id} - {$client->client_name}");
            $this->info("   📁 JSONs almacenados: " . count($jsonStorage));

            try {
                // Generar el informe AVANZADO usando el nuevo controlador
                $reportsController = new AutoseoAdvancedReports();
                $request = new Request(['id' => $client->id]);
                $response = $reportsController->generateAdvancedReport($request);
                $data = $response->getData(true);

                if ($data['success']) {
                    $this->info("   ✅ Informe generado exitosamente");
                    $this->info("   📄 Report ID: " . $data['data']['report_id']);
                    $this->info("   🌐 URL: " . $data['data']['url']);
                    $this->info("   📊 Keywords: " . $data['data']['summary']['total_keywords']);
                    $this->info("   📈 Top 10: " . $data['data']['summary']['keywords_in_top10']);
                    $successCount++;
                } else {
                    $this->error("   ❌ Error: " . $data['message']);
                    $errorCount++;
                }

            } catch (\Exception $e) {
                $this->error("   ❌ Excepción: " . $e->getMessage());
                Log::error("Error generando informe para cliente {$client->id}: " . $e->getMessage());
                $errorCount++;
            }

            $this->newLine();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Resumen final
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('📊 RESUMEN DE GENERACIÓN DE INFORMES');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info("✅ Exitosos:  {$successCount}");
        $this->info("❌ Errores:   {$errorCount}");
        $this->info("⏭️  Omitidos:  {$skippedCount}");
        $this->info("📋 Total:     {$clients->count()}");
        $this->info('═══════════════════════════════════════════════════════');

        if ($errorCount > 0) {
            $this->newLine();
            $this->warn("⚠️  Hubo {$errorCount} errores. Revisa los logs en storage/logs/laravel.log");
        }

        return $successCount > 0 ? 0 : 1;
    }
}
