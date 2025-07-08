<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Log;
use ZipArchive;
use File;
use App\Models\Autoseo\Autoseo;
use Carbon\Carbon;
use App\Mail\AutoseoReportGenerated;

class AutoseoReportsGen extends Controller
{
    private $zipUrl = "https://crm.hawkins.es/api/autoseo/json/storage";
    private $uploadUrl = "https://crm.hawkins.es/api/autoseo/reports/upload";

    // URL base para las APIs
    private $baseUrl = "https://crm.hawkins.es/api/autoseo";

    private function configureAutoseoMailer()
    {
        try {
            // Verificar credenciales
            if (!env('AUTOSEO_MAIL') || !env('AUTOSEO_PASSWORD')) {
                throw new \Exception('Credenciales de correo no configuradas');
            }

            // Configurar el mailer dinámicamente con los datos de IONOS
            $config = [
                'transport' => 'smtp',
                'host' => 'smtp.ionos.es',
                'port' => 465,
                'encryption' => 'ssl',
                'username' => env('AUTOSEO_MAIL'),
                'password' => env('AUTOSEO_PASSWORD'),
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN', 'hawkins.es'),
                'verify_peer' => false,
            ];

            Log::info("Configurando mailer Autoseo con IONOS", [
                'host' => $config['host'],
                'port' => $config['port'],
                'encryption' => $config['encryption'],
                'username' => $config['username']
            ]);

            Config::set('mail.mailers.autoseo', $config);
            Config::set('mail.default', 'autoseo');
            Config::set('mail.from.address', env('AUTOSEO_MAIL'));
            Config::set('mail.from.name', 'Autoseo Hawkins');

        } catch (\Exception $e) {
            Log::error("Error al configurar mailer Autoseo", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function sendReportNotification($autoseo, $filename)
    {
        try {
            // Verificar credenciales
            if (!env('AUTOSEO_MAIL') || !env('AUTOSEO_PASSWORD')) {
                Log::error("Credenciales de correo Autoseo no configuradas", [
                    'autoseo_id' => $autoseo->id,
                    'has_mail' => (bool)env('AUTOSEO_MAIL'),
                    'has_password' => (bool)env('AUTOSEO_PASSWORD')
                ]);
                throw new \Exception('Credenciales de correo no configuradas');
            }

            $recipients = ['nico.garcia@hawkins.es'];
            $domain = $autoseo->url ?? 'No especificado';

            // Configurar el mailer de autoseo
            $this->configureAutoseoMailer();

            Log::info("Intentando enviar correo de notificación", [
                'autoseo_id' => $autoseo->id,
                'domain' => $domain,
                'recipients' => $recipients,
                'filename' => $filename
            ]);

            foreach ($recipients as $email) {
                try {
                    Mail::to($email)->send(new AutoseoReportGenerated($autoseo->id, $filename, $domain, $autoseo->pin));
                    Log::info("Correo enviado exitosamente", [
                        'email' => $email,
                        'autoseo_id' => $autoseo->id
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error al enviar correo individual", [
                        'email' => $email,
                        'error' => $e->getMessage(),
                        'autoseo_id' => $autoseo->id
                    ]);
                    throw $e;
                }
            }

        } catch (\Exception $e) {
            Log::error("Error al enviar correo de notificación", [
                'error' => $e->getMessage(),
                'autoseo_id' => $autoseo->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

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
            \Log::info("Iniciando generación de informe SEO para Autoseo ID $id");

            // Obtener el registro de Autoseo
            $autoseo = Autoseo::findOrFail($id);
            $now = Carbon::now();

            // Verificar si hay archivos en json_storage
            if (empty($autoseo->json_storage)) {
                Log::info("Saltando generación de informe para Autoseo ID $id - No hay archivos en json_storage");
                return response()->json(['error' => 'No hay archivos JSON disponibles para generar el informe.'], 400);
            }

            // Descargar y extraer ZIP
            $jsonDataList = $this->downloadAndExtractZip($id);

            \Log::info("Datos JSON obtenidos", [
                'count' => count($jsonDataList),
                'first_date' => $jsonDataList[0]['uploaded_at'] ?? 'no date'
            ]);

            if (empty($jsonDataList)) {
                \Log::warning("No se encontraron archivos JSON válidos para ID $id");
                return response()->json(['error' => 'No se encontraron archivos JSON válidos.'], 400);
            }

            // Si solo hay un JSON, mostrar solo los datos de ese JSON sin análisis comparativo
            if (count($jsonDataList) === 1) {
                \Log::info("Generando informe simple (un solo JSON)");
                $seoData = $jsonDataList[0];
                $shortTailLabels = $seoData['short_tail'] ?? [];
                $longTailLabels = $seoData['long_tail'] ?? [];
                $allKeywords = $this->getAllKeywords($jsonDataList);
                $paaData = $this->processPaaData($jsonDataList);
                $paaLabels = $paaData['labels'];
                $searchConsoleData = $this->processSearchConsoleData($jsonDataList);
                $scHasData = !empty($searchConsoleData['months']);

                $html = view('autoseo.report-single', [
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
                ])->render();

                $filename = "informe_seo_{$id}.html";
                Storage::disk('public')->put("reports/{$filename}", $html);
                $this->uploadReport($filename, $id);

                // Actualizar fechas de reporte
                if (!$autoseo->first_report) {
                    $autoseo->first_report = $now;
                }
                $autoseo->last_report = $now;
                $autoseo->save();

                // Enviar notificación por correo
                $this->sendReportNotification($autoseo, $filename);

                return response()->json([
                    'success' => true,
                    'message' => 'Informe generado correctamente (solo un JSON)',
                    'filename' => $filename
                ]);
            }

            // Si hay más de un JSON, análisis comparativo como antes
            $versionDates = $this->extractVersionDates($jsonDataList);
            $seoData = $jsonDataList[0];

            \Log::info("Datos base procesados", [
                'num_dates' => count($versionDates),
                'dominio' => $seoData['dominio'] ?? 'no domain'
            ]);

            // Procesar keywords
            $allKeywords = $this->getAllKeywords($jsonDataList);
            $shortTailLabels = $seoData['short_tail'] ?? [];
            $longTailLabels = $seoData['long_tail'] ?? [];

            Log::info("Keywords procesadas", [
                'short_tail_count' => count($shortTailLabels),
                'long_tail_count' => count($longTailLabels),
                'all_keywords_count' => count($allKeywords)
            ]);

            // Generar datasets para Chart.js
            $shortTailDatasets = $this->buildChartjsDatasetsFromKeywords($shortTailLabels, $jsonDataList);
            $longTailDatasets = $this->buildChartjsDatasetsFromKeywords($longTailLabels, $jsonDataList);
            $detalleKeywordsDatasets = $this->buildChartjsDatasetsFromKeywords($allKeywords, $jsonDataList);

            // Procesar PAA (People Also Ask)
            $paaData = $this->processPaaData($jsonDataList);
            $paaLabels = $paaData['labels'];
            $paaDatasets = $paaData['datasets'];

            Log::info("PAA procesado", [
                'paa_count' => count($paaLabels)
            ]);

            // Generar tablas comparativas
            $shortTailTable = $this->generateComparisonTable($shortTailLabels, $jsonDataList);
            $longTailTable = $this->generateComparisonTable($longTailLabels, $jsonDataList);
            $detalleKeywordsTable = $this->generateComparisonTable($allKeywords, $jsonDataList);
            $paaTable = $this->generatePaaComparisonTable($paaLabels, $jsonDataList);

            // Procesar datos de Search Console
            $searchConsoleData = $this->processSearchConsoleData($jsonDataList);
            $scHasData = !empty($searchConsoleData['months']);

            \Log::info("Datos de Search Console procesados", [
                'has_data' => $scHasData,
                'num_months' => count($searchConsoleData['months'])
            ]);

            try {
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

                \Log::info("Vista renderizada correctamente");
            } catch (\Exception $e) {
                \Log::error("Error al renderizar la vista", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            // Guardar archivo HTML
            $filename = "informe_seo_{$id}.html";
            Storage::disk('public')->put("reports/{$filename}", $html);

            \Log::info("Archivo HTML guardado", [
                'filename' => $filename,
                'size' => strlen($html)
            ]);

            // Enviar al servidor
            $this->uploadReport($filename, $id);

            // Actualizar fechas de reporte
            if (!$autoseo->first_report) {
                $autoseo->first_report = $now;
            }
            $autoseo->last_report = $now;
            $autoseo->save();

            // Enviar notificación por correo
            $this->sendReportNotification($autoseo, $filename);

            return response()->json([
                'success' => true,
                'message' => 'Informe generado correctamente',
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            \Log::error("Error en processReportGeneration", [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error al generar el informe: ' . $e->getMessage()
            ], 500);
        }
    }

    private function downloadAndExtractZip($id)
    {
        try {
            \Log::info("Iniciando descarga de ZIP", [
                'id' => $id,
                'url' => $this->baseUrl . "/json/storage"
            ]);

            // Configurar timeout y SSL más permisivo
            $response = Http::timeout(120) // Aumentado a 120 segundos
                ->withoutVerifying()
                ->withOptions([
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_MAXREDIRS => 5,
                        CURLOPT_CONNECTTIMEOUT => 30, // Timeout de conexión
                        CURLOPT_DNS_CACHE_TIMEOUT => 30, // Cache DNS
                        CURLOPT_TCP_KEEPALIVE => 1,
                    ]
                ])
                ->retry(3, 5000) // Reintentar 3 veces con 5 segundos entre intentos
                ->get($this->baseUrl . "/json/storage", ['id' => $id]);

            \Log::info("Respuesta del servidor", [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body_length' => strlen($response->body())
            ]);

            if (!$response->successful()) {
                \Log::warning("La descarga del ZIP no fue exitosa, buscando JSONs locales", [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500) // Primeros 500 caracteres del body
                ]);

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
                        \Log::info("Se encontraron JSONs locales", ['count' => count($jsonDataList)]);
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
                    throw new \Exception("Error al descargar el ZIP y no se encontraron JSONs sueltos. Status: {$status}");
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
        // Obtener el registro de Autoseo para conseguir el client_id
        $autoseo = Autoseo::findOrFail($id);

        $filePath = Storage::disk('public')->path("reports/{$filename}");

        if (!File::exists($filePath)) {
            throw new \Exception('Archivo de informe no encontrado');
        }

        try {
            \Log::info("Iniciando subida de informe", [
                'id' => $autoseo->id,
                'client_id' => $autoseo->id,
                'filename' => $filename,
                'filesize' => File::size($filePath)
            ]);

            // Log the payload being sent
            $payload = [
                'id' => $id,
                'client_id' => $autoseo->client_id
            ];

            \Log::info("Payload being sent to upload endpoint", [
                'url' => $this->baseUrl . "/reports/upload",
                'payload' => $payload,
                'filename' => $filename,
                'file_size' => File::size($filePath)
            ]);

            $response = Http::timeout(120)
                ->withoutVerifying()
                ->withOptions([
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_MAXREDIRS => 5,
                        CURLOPT_CONNECTTIMEOUT => 30,
                        CURLOPT_DNS_CACHE_TIMEOUT => 30,
                        CURLOPT_TCP_KEEPALIVE => 1,
                        CURLOPT_TIMEOUT => 120,
                        CURLOPT_BUFFERSIZE => 128000,
                        CURLOPT_UPLOAD => true
                    ]
                ])
                ->retry(3, 5000)
                ->attach(
                    'file',
                    File::get($filePath),
                    $filename
                )
                ->post($this->baseUrl . "/reports/upload", $payload);

            \Log::info("Respuesta de subida", [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 1000), // Limitar el tamaño del log
                'headers' => $response->headers()
            ]);

            if (!$response->successful()) {
                \Log::warning("Error en la subida del informe", [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500)
                ]);
                throw new \Exception('Error al subir el informe al servidor. Status: ' . $response->status());
            }

        } catch (\Exception $e) {
            \Log::error("Error en uploadReport", [
                'id' => $id,
                'url' => $this->baseUrl . "/reports/upload",
                'error' => $e->getMessage(),
                'file_exists' => File::exists($filePath),
                'file_size' => File::exists($filePath) ? File::size($filePath) : 0
            ]);
            throw $e;
        }

        return $response->json();
    }
}
