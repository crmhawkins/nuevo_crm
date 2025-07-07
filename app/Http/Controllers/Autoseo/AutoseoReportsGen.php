<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use File;

class AutoseoReportsGen extends Controller
{
    private $zipUrl = "https://crm.hawkins.es/api/autoseo/json/storage";
    private $uploadUrl = "https://crm.hawkins.es/api/autoseo/reports/upload";

    // URL base para las APIs
    private $baseUrl = "https://crm.hawkins.es/api/autoseo";

    public function generateReport(Request $request, $id = 15)
    {
        // Si es una petición GET, mostrar el formulario
        if ($request->isMethod('get')) {
            return view('autoseo.generate-report');
        }

        // Si es POST, procesar la generación
        $id = $request->input('report_id', $id);
        $email = $request->input('email_notification');

        return $this->processReportGeneration($id, $email);
    }

    public function generateReportFromCommand($id = 15)
    {
        return $this->processReportGeneration($id);
    }

    private function processReportGeneration($id, $email = null)
    {
        try {
            // Descargar y extraer ZIP
            $jsonDataList = $this->downloadAndExtractZip($id);

            if (empty($jsonDataList)) {
                return response()->json(['error' => 'No se encontraron archivos JSON válidos.'], 400);
            }

            // Si solo hay un JSON, mostrar solo los datos de ese JSON sin análisis comparativo
            if (count($jsonDataList) === 1) {
                $seoData = $jsonDataList[0];
                $shortTailLabels = $seoData['short_tail'] ?? [];
                $longTailLabels = $seoData['long_tail'] ?? [];
                $allKeywords = $this->getAllKeywords($jsonDataList);
                $paaData = $this->processPaaData($jsonDataList);
                $paaLabels = $paaData['labels'];
                $searchConsoleData = $this->processSearchConsoleData($jsonDataList);
                $scHasData = !empty($searchConsoleData['months']);

                $html = view('autoseo.report-template', [
                    'seo' => $seoData,
                    'short_tail_labels' => $shortTailLabels,
                    'long_tail_labels' => $longTailLabels,
                    'detalle_keywords_labels' => $allKeywords,
                    'paa_labels' => $paaLabels,
                    'version_dates' => [$seoData['uploaded_at'] ?? '-'],
                    'short_tail_chartjs_datasets' => [],
                    'long_tail_chartjs_datasets' => [],
                    'detalle_keywords_chartjs_datasets' => [],
                    'paa_chartjs_datasets' => [],
                    'short_tail_table' => [],
                    'long_tail_table' => [],
                    'detalle_keywords_table' => [],
                    'paa_table' => [],
                    'sc_months' => $searchConsoleData['months'],
                    'sc_clicks' => $searchConsoleData['clicks'],
                    'sc_impressions' => $searchConsoleData['impressions'],
                    'sc_avg_ctr' => $searchConsoleData['avg_ctr'],
                    'sc_avg_position' => $searchConsoleData['avg_position'],
                    'sc_has_data' => $scHasData,
                    'is_single' => true,
                ])->render();

                $filename = "informe_seo_{$id}.html";
                Storage::disk('public')->put("reports/{$filename}", $html);
                $this->uploadReport($filename, $id);

                return response()->json([
                    'success' => true,
                    'message' => 'Informe generado correctamente (solo un JSON)',
                    'filename' => $filename
                ]);
            }

            // Si hay más de un JSON, análisis comparativo como antes
            $versionDates = $this->extractVersionDates($jsonDataList);
            $seoData = $jsonDataList[0];

            // Procesar keywords
            $allKeywords = $this->getAllKeywords($jsonDataList);
            $shortTailLabels = $seoData['short_tail'] ?? [];
            $longTailLabels = $seoData['long_tail'] ?? [];

            // Generar datasets para Chart.js
            $shortTailDatasets = $this->buildChartjsDatasetsFromKeywords($shortTailLabels, $jsonDataList);
            $longTailDatasets = $this->buildChartjsDatasetsFromKeywords($longTailLabels, $jsonDataList);
            $detalleKeywordsDatasets = $this->buildChartjsDatasetsFromKeywords($allKeywords, $jsonDataList);

            // Procesar PAA (People Also Ask)
            $paaData = $this->processPaaData($jsonDataList);
            $paaLabels = $paaData['labels'];
            $paaDatasets = $paaData['datasets'];

            // Generar tablas comparativas
            $shortTailTable = $this->generateComparisonTable($shortTailLabels, $jsonDataList);
            $longTailTable = $this->generateComparisonTable($longTailLabels, $jsonDataList);
            $detalleKeywordsTable = $this->generateComparisonTable($allKeywords, $jsonDataList);
            $paaTable = $this->generatePaaComparisonTable($paaLabels, $jsonDataList);

            // Procesar datos de Search Console
            $searchConsoleData = $this->processSearchConsoleData($jsonDataList);
            $scHasData = !empty($searchConsoleData['months']);

            // Renderizar vista Blade
            $html = view('autoseo.report-template', [
                'seo' => $seoData,
                'short_tail_labels' => $shortTailLabels,
                'long_tail_labels' => $longTailLabels,
                'detalle_keywords_labels' => $allKeywords,
                'paa_labels' => $paaLabels,
                'version_dates' => $versionDates,
                'short_tail_chartjs_datasets' => $shortTailDatasets,
                'long_tail_chartjs_datasets' => $longTailDatasets,
                'detalle_keywords_chartjs_datasets' => $detalleKeywordsDatasets,
                'paa_chartjs_datasets' => $paaDatasets,
                'short_tail_table' => $shortTailTable,
                'long_tail_table' => $longTailTable,
                'detalle_keywords_table' => $detalleKeywordsTable,
                'paa_table' => $paaTable,
                'sc_months' => $searchConsoleData['months'],
                'sc_clicks' => $searchConsoleData['clicks'],
                'sc_impressions' => $searchConsoleData['impressions'],
                'sc_avg_ctr' => $searchConsoleData['avg_ctr'],
                'sc_avg_position' => $searchConsoleData['avg_position'],
                'sc_has_data' => $scHasData,
                'is_single' => false,
            ])->render();

            // Guardar archivo HTML
            $filename = "informe_seo_{$id}.html";
            Storage::disk('public')->put("reports/{$filename}", $html);

            // Enviar al servidor
            $this->uploadReport($filename, $id);

            return response()->json([
                'success' => true,
                'message' => 'Informe generado correctamente',
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el informe: ' . $e->getMessage()
            ], 500);
        }
    }

            private function downloadAndExtractZip($id)
    {
        try {
            // Configurar timeout y SSL más permisivo
            $response = Http::timeout(60)
                ->withoutVerifying()
                ->withOptions([
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_MAXREDIRS => 5
                    ]
                ])
                ->get($this->baseUrl . "/json/storage", ['id' => $id]);

            \Log::info("Respuesta del servidor", [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body_length' => strlen($response->body())
            ]);

            if (!$response->successful()) {
                // Si falla la descarga del ZIP, intentar buscar JSONs sueltos
                $jsonDir = storage_path("app/public/autoseo_reports/{$id}");
                if (\File::exists($jsonDir)) {
                    $files = \File::glob($jsonDir . '/*.json');
                    sort($files);
                    $jsonDataList = [];
                    foreach ($files as $file) {
                        try {
                            $data = json_decode(\File::get($file), true);
                            if ($data) {
                                $jsonDataList[] = $data;
                            }
                        } catch (\Exception $e) {
                            \Log::error("Error al procesar archivo JSON suelto: " . $file . " - " . $e->getMessage());
                        }
                    }
                    if (!empty($jsonDataList)) {
                        return $jsonDataList;
                    }
                }
                $status = $response->status();
                $body = $response->body();

                if ($status === 500 && strpos($body, 'does not exist') !== false) {
                    throw new \Exception("El archivo ZIP para el ID {$id} no existe en el servidor y no se encontraron JSONs sueltos. Verifica que el ID sea correcto.");
                } elseif ($status === 404) {
                    throw new \Exception("No se encontró el archivo ZIP para el ID {$id} ni JSONs sueltos. Verifica que el ID sea correcto.");
                } else {
                    throw new \Exception("Error al descargar el ZIP y no se encontraron JSONs sueltos. Status: {$status} - Body: {$body}");
                }
            }

        } catch (\Exception $e) {
            // Si hay excepción, intentar buscar JSONs sueltos
            $jsonDir = storage_path("app/public/autoseo_reports/{$id}");
            if (\File::exists($jsonDir)) {
                $files = \File::glob($jsonDir . '/*.json');
                sort($files);
                $jsonDataList = [];
                foreach ($files as $file) {
                    try {
                        $data = json_decode(\File::get($file), true);
                        if ($data) {
                            $jsonDataList[] = $data;
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error al procesar archivo JSON suelto: " . $file . " - " . $e->getMessage());
                    }
                }
                if (!empty($jsonDataList)) {
                    return $jsonDataList;
                }
            }
            \Log::error("Error en downloadAndExtractZip", [
                'id' => $id,
                'url' => $this->zipUrl,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // Crear directorio temporal
        $tempDir = storage_path("app/temp/reports_{$id}");
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // Guardar ZIP
        $zipPath = $tempDir . '/data.zip';
        File::put($zipPath, $response->body());

        // Extraer ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            throw new \Exception('Error al extraer el ZIP');
        }

        // Leer archivos JSON
        $jsonDataList = [];
        $files = File::glob($tempDir . '/*.json');
        sort($files);

        foreach ($files as $file) {
            try {
                $data = json_decode(File::get($file), true);
                if ($data) {
                    $jsonDataList[] = $data;
                }
            } catch (\Exception $e) {
                \Log::error("Error al procesar archivo JSON: " . $file . " - " . $e->getMessage());
            }
        }

        // Limpiar archivos temporales
        File::deleteDirectory($tempDir);

        return $jsonDataList;
    }

    private function extractVersionDates($jsonDataList)
    {
        return array_map(function($data) {
            return $data['uploaded_at'] ?? '-';
        }, $jsonDataList);
    }

    private function getAllKeywords($jsonDataList)
    {
        $allKeywords = [];
        foreach ($jsonDataList as $data) {
            foreach ($data['detalles_keywords'] ?? [] as $item) {
                $keyword = $item['keyword'] ?? '';
                if ($keyword && !in_array($keyword, $allKeywords)) {
                    $allKeywords[] = $keyword;
                }
            }
        }
        sort($allKeywords);
        return $allKeywords;
    }

    private function getKeywordEvolution($keyword, $jsonDataList)
    {
        $values = [];
        foreach ($jsonDataList as $data) {
            $found = false;
            foreach ($data['detalles_keywords'] ?? [] as $item) {
                if (($item['keyword'] ?? '') === $keyword) {
                    $values[] = $item['total_results'] ?? null;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $values[] = null;
            }
        }
        return $values;
    }

    private function buildChartjsDatasetsFromKeywords($keywords, $jsonDataList)
    {
        $datasets = [];
        foreach ($keywords as $keyword) {
            $datasets[] = [
                'label' => $keyword,
                'data' => $this->getKeywordEvolution($keyword, $jsonDataList)
            ];
        }
        return $datasets;
    }

    private function processPaaData($jsonDataList)
    {
        $allPaaQuestions = [];
        $paaLabelMap = [];

        // Recopilar todas las preguntas únicas
        foreach ($jsonDataList as $data) {
            foreach ($data['people_also_ask'] ?? [] as $item) {
                $question = $item['question'] ?? '';
                if ($question) {
                    $normalized = strtolower(trim($question));
                    if (!in_array($normalized, $allPaaQuestions)) {
                        $allPaaQuestions[] = $normalized;
                        $paaLabelMap[$normalized] = $question;
                    }
                }
            }
        }
        sort($allPaaQuestions);

        // Generar datasets
        $datasets = [];
        foreach ($allPaaQuestions as $questionNorm) {
            $datasets[] = [
                'label' => $paaLabelMap[$questionNorm] ?? $questionNorm,
                'data' => $this->getPaaEvolution($questionNorm, $jsonDataList)
            ];
        }

        $labels = array_values($paaLabelMap);

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    private function getPaaEvolution($questionNorm, $jsonDataList)
    {
        $values = [];
        foreach ($jsonDataList as $data) {
            $found = false;
            foreach ($data['people_also_ask'] ?? [] as $item) {
                $question = $item['question'] ?? '';
                if (strtolower(trim($question)) === $questionNorm) {
                    $values[] = $item['total_results'] ?? null;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $values[] = null;
            }
        }
        return $values;
    }

    private function generateComparisonTable($keywords, $jsonDataList)
    {
        $table = [];
        foreach ($keywords as $keyword) {
            $row = [$keyword];
            $row = array_merge($row, $this->getKeywordEvolution($keyword, $jsonDataList));
            $table[] = $row;
        }
        return $table;
    }

    private function generatePaaComparisonTable($paaLabels, $jsonDataList)
    {
        $table = [];
        foreach ($paaLabels as $question) {
            $row = [$question];
            $row = array_merge($row, $this->getPaaEvolution(strtolower(trim($question)), $jsonDataList));
            $table[] = $row;
        }
        return $table;
    }

    private function processSearchConsoleData($jsonDataList)
    {
        // Buscar archivo de datos mensuales
        foreach ($jsonDataList as $data) {
            if (isset($data['monthly_performance'])) {
                $monthlyData = $data['monthly_performance'];
                $months = array_keys($monthlyData);

                if (!empty($months)) {
                    $clicks = [];
                    $impressions = [];
                    $avgCtr = [];
                    $avgPosition = [];

                    foreach ($months as $month) {
                        $clicks[] = $monthlyData[$month]['clicks'] ?? 0;
                        $impressions[] = $monthlyData[$month]['impressions'] ?? 0;
                        $avgCtr[] = $monthlyData[$month]['avg_ctr'] ?? 0;
                        $avgPosition[] = $monthlyData[$month]['avg_position'] ?? 0;
                    }

                    return [
                        'months' => $months,
                        'clicks' => $clicks,
                        'impressions' => $impressions,
                        'avg_ctr' => $avgCtr,
                        'avg_position' => $avgPosition
                    ];
                }
            }
        }

        return [
            'months' => [],
            'clicks' => [],
            'impressions' => [],
            'avg_ctr' => [],
            'avg_position' => []
        ];
    }

    private function uploadReport($filename, $id)
    {
        $filePath = Storage::disk('public')->path("reports/{$filename}");

        if (!File::exists($filePath)) {
            throw new \Exception('Archivo de informe no encontrado');
        }

        try {
            $response = Http::timeout(60)
                ->withoutVerifying()
                ->withOptions([
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_MAXREDIRS => 5
                    ]
                ])
                ->attach(
                    'file',
                    File::get($filePath),
                    $filename
                )
                ->post($this->baseUrl . "/reports/upload", [
                    'id' => $id
                ]);

            \Log::info("Respuesta de subida", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                throw new \Exception('Error al subir el informe al servidor. Status: ' . $response->status() . ' - Body: ' . $response->body());
            }

        } catch (\Exception $e) {
            \Log::error("Error en uploadReport", [
                'id' => $id,
                'url' => $this->uploadUrl,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        return $response->json();
    }
}
