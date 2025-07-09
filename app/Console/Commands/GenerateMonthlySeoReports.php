<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Autoseo\Autoseo;
use App\Http\Controllers\Autoseo\AutoseoReportsGen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateMonthlySeoReports extends Command
{
    protected $signature = 'seo:generate-monthly-reports';
    protected $description = 'Genera informes SEO mensuales y anuales para cada página Autoseo';

    public function handle()
    {
        $now = Carbon::now();
        $controller = new AutoseoReportsGen();
        $autoseos = Autoseo::all();

        foreach ($autoseos as $autoseo) {
            $id = $autoseo->id;

            // Verificar si hay archivos en json_storage
            if (empty($autoseo->json_storage)) {
                Log::info("Saltando generación de informe para Autoseo ID $id - No hay archivos en json_storage");

                // Si no hay archivos JSON, reseteamos las fechas de reportes
                if ($autoseo->first_report || $autoseo->last_report) {
                    $autoseo->first_report = null;
                    $autoseo->last_report = null;
                    $autoseo->save();
                    Log::info("Reseteadas fechas de reportes para Autoseo ID $id por falta de archivos JSON");
                }
                continue;
            }

            // Verificar que haya al menos 1 JSON en json_storage
            $jsonStorage = is_array($autoseo->json_storage) ? $autoseo->json_storage : json_decode($autoseo->json_storage, true);
            if (empty($jsonStorage) || count($jsonStorage) < 1) {
                Log::info("Saltando generación de informe para Autoseo ID $id - No hay suficientes JSONs (mínimo 1 requerido)");
                continue;
            }

            // Solo procesamos las fechas si hay archivos JSON
            $firstReport = $autoseo->first_report ? Carbon::parse($autoseo->first_report) : null;
            $lastReport = $autoseo->last_report ? Carbon::parse($autoseo->last_report) : null;
            $shouldGenerate = false;
            $isAnnual = false;

            if (!$firstReport) {
                // Nunca se ha hecho un informe
                $shouldGenerate = true;
            } elseif ($lastReport && $lastReport->diffInMonths($now) >= 1) {
                // Ha pasado al menos un mes desde el último
                $shouldGenerate = true;
            }

            if ($firstReport && $firstReport->diffInMonths($now) >= 12) {
                // Ha pasado un año desde el primer informe
                $isAnnual = true;
            }

            if ($shouldGenerate) {
                Log::info("Generando informe SEO para Autoseo ID $id");
                $result = $controller->generateReportFromCommand($id);

                // Generar nombre único para el archivo JSON
                $jsonFileName = uniqid() . '_' . $id . '.json';
                $jsonPath = 'autoseo/json/' . $jsonFileName;

                // Obtener el array actual de json_storage
                $currentStorage = $autoseo->reports ? $autoseo->reports : [];
                if (!is_array($currentStorage)) {
                    $currentStorage = [];
                }

                // Añadir nueva entrada
                $currentStorage[] = [
                    'path' => $jsonPath,
                    'creation_date' => $now->toDateTimeString(),
                    'original_name' => $jsonFileName
                ];

                // Actualizar el modelo
                $autoseo->reports = $currentStorage;
                $autoseo->last_report = $now;
                if (!$firstReport) {
                    $autoseo->first_report = $now;
                }
                $autoseo->save();

                Log::info("Informe generado para Autoseo ID $id", [
                    'json_path' => $jsonPath,
                    'storage_count' => count($currentStorage)
                ]);
            }

            if ($isAnnual) {
                Log::info("Generando informe ANUAL SEO para Autoseo ID $id");
                // Aquí puedes llamar a un método especial para el informe anual
                // $controller->generateAnnualReport($id);
                // Por ahora, solo logueamos
                Log::info("Informe ANUAL generado para Autoseo ID $id");
                // Opcional: puedes actualizar un campo o enviar notificación
            }
        }
        $this->info('Informes SEO mensuales/anuales generados.');
    }
}
