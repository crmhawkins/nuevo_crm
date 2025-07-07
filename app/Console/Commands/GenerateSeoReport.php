<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Autoseo\AutoseoReportsGen;

class GenerateSeoReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:generate-report {id? : ID del reporte (opcional, por defecto 15)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un informe SEO comparativo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id') ?? 15;

        $this->info("🔍 Generando informe SEO para ID: {$id}");

        try {
            $controller = new AutoseoReportsGen();
            $result = $controller->generateReportFromCommand($id);

            if ($result->getStatusCode() === 200) {
                $data = json_decode($result->getContent(), true);
                $this->info("✅ Informe generado correctamente");
                $this->info("📁 Archivo: {$data['filename']}");
                $this->info("📧 Informe enviado al servidor");
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
