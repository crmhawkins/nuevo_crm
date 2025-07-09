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

    private function sendReportNotification($autoseo, $filename, $email = null)
    {
        try {
            $domain = $autoseo->url;
            $reportUrl = Storage::disk('public')->url("reports/{$filename}");
            $clientEmail = $autoseo->client_email;
            $recipients = ['nico.garcia@hawkins.es'];
            if ($email !== null) {
                $recipients[] = $email;
            }
            Log::info("Configurando envío de correo", [
                'autoseo_id' => $autoseo->id,
                'domain' => $domain,
                'recipients' => $recipients
            ]);

            // Configurar el mailer
            $config = [
                'transport' => 'smtp',
                'host' => 'smtp.ionos.es',
                'port' => 465,
                'encryption' => 'ssl',
                'username' => 'seo@hawkins.es',
                'password' => env('AUTOSEO_PASSWORD'),
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN', 'hawkins.es'),
                'verify_peer' => false,
            ];

            Log::info("Configuración del mailer", [
                'host' => $config['host'],
                'port' => $config['port'],
                'username' => $config['username']
            ]);

            Config::set('mail.mailers.autoseo', $config);
            Config::set('mail.default', 'autoseo');
            Config::set('mail.from.address', 'seo@hawkins.es');
            Config::set('mail.from.name', 'SEO Hawkins');

            // Si no hay destinatarios, salir
            if (empty($recipients)) {
                Log::warning("No hay destinatarios configurados para el dominio $domain");
                return;
            }

            try {
                Log::info("Intentando enviar correo", [
                    'template' => 'mails.seo-report',
                    'data' => [
                        'domain' => $domain,
                        'has_pin' => !empty($autoseo->pin),
                        'has_report_url' => !empty($reportUrl)
                    ]
                ]);

                foreach ($recipients as $email) {
                    Log::info("Enviando correo a destinatario", ['email' => $email]);

                    Mail::send('mails.seo-report', [
                        'domain' => $domain,
                        'report_url' => $reportUrl,
                        'pin' => $autoseo->pin
                    ], function ($message) use ($email, $domain) {
                        $message->from('seo@hawkins.es', 'SEO Hawkins')
                               ->to($email)
                               ->subject("Informe SEO - $domain");
                    });
                }

                Log::info("Correo enviado exitosamente", [
                    'domain' => $domain,
                    'recipients' => $recipients
                ]);

            } catch (\Exception $e) {
                Log::error("Error al enviar el correo", [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'stack_trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("Error general en el proceso de notificación", [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'stack_trace' => $e->getTraceAsString(),
                'domain' => $autoseo->url ?? 'unknown',
                'autoseo_id' => $autoseo->id ?? 'unknown'
            ]);
        }
    }

    public function generateReport(Request $request, $id = null)
    {
        // Si es una petición GET, mostrar el formulario
        if ($request->isMethod('get')) {
            Log::info("Mostrando formulario de generación de informe", [
                'id' => $id,
                'method' => 'GET'
            ]);
            $clients = Autoseo::all();
            $selectedClientId = $id;
            return view('autoseo.generate-report', compact('clients', 'selectedClientId'));
        }

        // Si es POST, procesar la generación
        Log::info("Iniciando proceso de generación de informe", [
            'request_method' => $request->method(),
            'report_type' => $request->query('type'),
            'client_id' => $request->input('client_id'),
            'email' => $request->input('email_notification')
        ]);

        $id = $request->input('client_id');
        $email = $request->input('email_notification');

        try {
            return $this->processReportGeneration($id, $email);
        } catch (\Exception $e) {
            Log::error("Error en generateReport", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error al generar el informe: ' . $e->getMessage()], 500);
        }
    }

    public function generateReportFromCommand($id = 15)
    {
        return $this->processReportGeneration($id);
    }

    private function prepareKeywordData($data)
    {
        if (empty($data)) {
            return [];
        }

        foreach ($data as &$row) {
            if (!isset($row['keyword'])) {
                continue;
            }

            $lastResult = end($row['total_results']);
            $firstResult = reset($row['total_results']);
            $change = $lastResult - $firstResult;
            $changePercent = $firstResult ? round(($change / $firstResult) * 100, 1) : 0;

            $lastPos = end($row['position']);
            $firstPos = reset($row['position']);
            $posChange = $firstPos - $lastPos;

            $row['metrics'] = [
                'last_result' => $lastResult,
                'change_percent' => $changePercent,
                'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral'),
                'last_position' => $lastPos,
                'position_change' => $posChange,
                'chart_id' => 'chart_' . preg_replace('/[^a-zA-Z0-9]+/', '_', $row['keyword'])
            ];
        }

        return $data;
    }

    private function processReportGeneration($id, $email = null)
    {
        try {
            $reportType = request()->query('type', 'standard');
            Log::info("Iniciando generación de informe SEO", [
                'autoseo_id' => $id,
                'email_provided' => $email ?? 'no',
                'report_type' => $reportType,
                'timestamp' => now()->toDateTimeString()
            ]);

            // Obtener el registro de Autoseo
            $autoseo = Autoseo::findOrFail($id);

            Log::info("Datos del registro Autoseo", [
                'id' => $autoseo->id,
                'url' => $autoseo->url,
                'client_email' => $autoseo->client_email,
                'has_json_storage' => !empty($autoseo->json_storage),
                'report_type' => $reportType
            ]);

            $now = Carbon::now();

            // Verificar si hay archivos en json_storage
            if (empty($autoseo->json_storage)) {
                Log::warning("No hay archivos en json_storage", [
                    'autoseo_id' => $id,
                    'report_type' => $reportType
                ]);
                return response()->json(['error' => 'No hay archivos JSON disponibles para generar el informe.'], 400);
            }

            // Verificar que haya al menos 1 JSON en json_storage
            $jsonStorage = is_array($autoseo->json_storage) ? $autoseo->json_storage : json_decode($autoseo->json_storage, true);
            if (empty($jsonStorage) || count($jsonStorage) < 1) {
                Log::warning("JSON storage vacío o inválido", [
                    'autoseo_id' => $id,
                    'json_storage_count' => count($jsonStorage ?? []),
                    'report_type' => $reportType
                ]);
                return response()->json(['error' => 'Se requiere al menos 1 archivo JSON para generar el informe.'], 400);
            }

            // Descargar y extraer ZIP
            Log::info("Iniciando descarga y extracción de ZIP", [
                'autoseo_id' => $id,
                'report_type' => $reportType
            ]);

            $jsonDataList = $this->downloadAndExtractZip($id);

            Log::info("Datos JSON obtenidos", [
                'count' => count($jsonDataList),
                'first_date' => $jsonDataList[0]['uploaded_at'] ?? 'no date',
                'report_type' => $reportType
            ]);

            if (empty($jsonDataList)) {
                Log::warning("No se encontraron archivos JSON válidos", [
                    'autoseo_id' => $id,
                    'report_type' => $reportType
                ]);
                return response()->json(['error' => 'No se encontraron archivos JSON válidos.'], 400);
            }

            // Procesar datos de Search Console
            Log::info("Procesando datos de Search Console", [
                'autoseo_id' => $id,
                'report_type' => $reportType
            ]);

            $searchConsoleData = $this->processSearchConsoleData($jsonDataList);
            $scHasData = !empty($searchConsoleData['months']);

            // Si solo hay un JSON, usar la vista simple
            if (count($jsonDataList) === 1) {
                Log::info("Generando informe simple (un solo JSON)", [
                    'autoseo_id' => $id,
                    'report_type' => $reportType
                ]);

                $seoData = $jsonDataList[0];
                $shortTailLabels = $seoData['short_tail'] ?? [];
                $longTailLabels = $seoData['long_tail'] ?? [];

                // Generar tablas comparativas y preparar datos
                Log::info("Preparando datos para tablas", [
                    'short_tail_count' => count($shortTailLabels),
                    'long_tail_count' => count($longTailLabels),
                    'report_type' => $reportType
                ]);

                $shortTailTable = $this->prepareKeywordData($this->generateComparisonTable($shortTailLabels, $jsonDataList));
                $longTailTable = $this->prepareKeywordData($this->generateComparisonTable($longTailLabels, $jsonDataList));

                // Procesar PAA (People Also Ask)
                $paaData = $this->processPaaData($jsonDataList);
                $paaLabels = $paaData['labels'];
                $paaTable = $this->prepareKeywordData($this->generatePaaComparisonTable($paaLabels, $jsonDataList));

                // Determinar qué vista usar basado en el tipo de reporte
                $view = $reportType === 'parallel' ? 'autoseo.report-justification' : 'autoseo.report-single';

                Log::info("Renderizando vista", [
                    'view' => $view,
                    'report_type' => $reportType,
                    'autoseo_id' => $id
                ]);

                try {
                    $html = view($view, [
                        'seo' => $seoData,
                        'version_dates' => [$seoData['uploaded_at'] ?? '-'],
                        'short_tail_table' => $shortTailTable,
                        'long_tail_table' => $longTailTable,
                        'paa_table' => $paaTable,
                        'sc_months' => $searchConsoleData['months'],
                        'sc_clicks' => $searchConsoleData['clicks'],
                        'sc_impressions' => $searchConsoleData['impressions'],
                        'sc_avg_ctr' => $searchConsoleData['avg_ctr'],
                        'sc_avg_position' => $searchConsoleData['avg_position'],
                        'sc_has_data' => $scHasData,
                    ])->render();

                    Log::info("Vista renderizada correctamente", [
                        'html_length' => strlen($html),
                        'report_type' => $reportType
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error al renderizar la vista", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'view' => $view,
                        'report_type' => $reportType
                    ]);
                    throw $e;
                }

                $filename = "informe_seo_{$id}" . ($reportType === 'parallel' ? '_justificacion' : '') . ".html";
                Storage::disk('public')->put("reports/{$filename}", $html);

                Log::info("Archivo guardado correctamente", [
                    'filename' => $filename,
                    'size' => strlen($html),
                    'report_type' => $reportType
                ]);

                $this->uploadReport($filename, $id);

                // Actualizar fechas de reporte
                if (!$autoseo->first_report) {
                    $autoseo->first_report = $now;
                }
                $autoseo->last_report = $now;
                $autoseo->save();

                Log::info("Fechas de reporte actualizadas", [
                    'first_report' => $autoseo->first_report,
                    'last_report' => $autoseo->last_report,
                    'report_type' => $reportType
                ]);

                // Solo enviar correo si no es un informe de justificación
                if ($reportType !== 'parallel') {
                    Log::info("Enviando notificación por correo", [
                        'email' => $email,
                        'report_type' => $reportType
                    ]);
                    $this->sendReportNotification($autoseo, $filename, $email);
                } else {
                    Log::info("Omitiendo envío de correo para informe de justificación", [
                        'report_type' => $reportType
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Informe simple generado correctamente',
                    'filename' => $filename,
                    'report_type' => $reportType
                ]);
            }

            // Si hay más de un JSON, generar informe comparativo
            $versionDates = $this->extractVersionDates($jsonDataList);
            $seoData = $jsonDataList[0];

            Log::info("Datos base procesados", [
                'num_dates' => count($versionDates),
                'dominio' => $seoData['dominio'] ?? 'no domain'
            ]);

            // Procesar keywords
            $shortTailLabels = $seoData['short_tail'] ?? [];
            $longTailLabels = $seoData['long_tail'] ?? [];

            // Generar tablas comparativas
            $shortTailTable = $this->prepareKeywordData($this->generateComparisonTable($shortTailLabels, $jsonDataList));
            $longTailTable = $this->prepareKeywordData($this->generateComparisonTable($longTailLabels, $jsonDataList));

            // Procesar PAA (People Also Ask)
            $paaData = $this->processPaaData($jsonDataList);
            $paaLabels = $paaData['labels'];
            $paaTable = $this->prepareKeywordData($this->generatePaaComparisonTable($paaLabels, $jsonDataList));

            try {
                // Determinar qué vista usar basado en el tipo de reporte
                $view = request()->query('type') === 'parallel' ? 'autoseo.report-justification' : 'autoseo.report-template';

                // Renderizar vista Blade
                $html = view($view, [
                    'seo' => $seoData,
                    'version_dates' => $versionDates,
                    'short_tail_table' => $shortTailTable,
                    'long_tail_table' => $longTailTable,
                    'paa_table' => $paaTable,
                    'sc_months' => $searchConsoleData['months'],
                    'sc_clicks' => $searchConsoleData['clicks'],
                    'sc_impressions' => $searchConsoleData['impressions'],
                    'sc_avg_ctr' => $searchConsoleData['avg_ctr'],
                    'sc_avg_position' => $searchConsoleData['avg_position'],
                    'sc_has_data' => $scHasData,
                ])->render();

                Log::info("Vista renderizada correctamente");
            } catch (\Exception $e) {
                Log::error("Error al renderizar la vista", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            // Guardar archivo HTML
            $filename = "informe_seo_{$id}" . (request()->query('type') === 'parallel' ? '_justificacion' : '') . ".html";
            Storage::disk('public')->put("reports/{$filename}", $html);

            Log::info("Archivo HTML guardado", [
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

            // Solo enviar correo si no es un informe de justificación
            if (request()->query('type') !== 'parallel') {
                $this->sendReportNotification($autoseo, $filename, $email);
            }

            return response()->json([
                'success' => true,
                'message' => 'Informe generado correctamente',
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            Log::error("Error al generar informe SEO", [
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
            Log::info("Iniciando descarga de ZIP", [
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

            Log::info("Respuesta del servidor", [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body_length' => strlen($response->body())
            ]);

            if (!$response->successful()) {
                Log::warning("La descarga del ZIP no fue exitosa, buscando JSONs locales", [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500) // Primeros 500 caracteres del body
                ]);

                // Si falla la descarga del ZIP, intentar buscar JSONs sueltos
                $jsonDir = storage_path("app/public/autoseo_reports/{$id}");
                if (File::exists($jsonDir)) {
                    $files = File::glob($jsonDir . '/*.json');
                    sort($files);
                    $jsonDataList = [];
                    foreach ($files as $file) {
                        try {
                            $data = json_decode(File::get($file), true);
                            if ($data) {
                                $jsonDataList[] = $data;
                            }
                        } catch (\Exception $e) {
                            Log::error("Error al procesar archivo JSON suelto: " . $file . " - " . $e->getMessage());
                        }
                    }
                    if (!empty($jsonDataList)) {
                        Log::info("Se encontraron JSONs locales", ['count' => count($jsonDataList)]);
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
            if (File::exists($jsonDir)) {
                $files = File::glob($jsonDir . '/*.json');
                sort($files);
                $jsonDataList = [];
                foreach ($files as $file) {
                    try {
                        $data = json_decode(File::get($file), true);
                        if ($data) {
                            $jsonDataList[] = $data;
                        }
                    } catch (\Exception $e) {
                        Log::error("Error al procesar archivo JSON suelto: " . $file . " - " . $e->getMessage());
                    }
                }
                if (!empty($jsonDataList)) {
                    return $jsonDataList;
                }
            }
            Log::error("Error en downloadAndExtractZip", [
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
                Log::error("Error al procesar archivo JSON: " . $file . " - " . $e->getMessage());
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
        $evolution = [
            'total_results' => [],
            'position' => []
        ];

        foreach ($jsonDataList as $data) {
            $found = false;
            foreach ($data['detalles_keywords'] ?? [] as $item) {
                if (($item['keyword'] ?? '') === $keyword) {
                    $evolution['total_results'][] = $item['total_results'] ?? null;
                    $evolution['position'][] = $item['position'] ?? null;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $evolution['total_results'][] = null;
                $evolution['position'][] = null;
            }
        }
        return $evolution;
    }

    private function buildChartjsDatasetsFromKeywords($keywords, $jsonDataList)
    {
        $datasets = [];
        foreach ($keywords as $keyword) {
            $evolution = $this->getKeywordEvolution($keyword, $jsonDataList);
            // Dataset para total_results
            $datasets[] = [
                'label' => $keyword . ' (Resultados)',
                'data' => $evolution['total_results'],
                'yAxisID' => 'y'
            ];
            // Dataset para position
            $datasets[] = [
                'label' => $keyword . ' (Posición)',
                'data' => $evolution['position'],
                'yAxisID' => 'y1',
                'type' => 'line',
                'borderDash' => [5, 5]
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
            $evolution = $this->getKeywordEvolution($keyword, $jsonDataList);
            $table[] = [
                'keyword' => $keyword,
                'total_results' => $evolution['total_results'],
                'position' => $evolution['position']
            ];
        }
        return $table;
    }

    private function generatePaaComparisonTable($paaLabels, $jsonDataList)
    {
        $table = [];
        foreach ($paaLabels as $question) {
            $evolution = [
                'total_results' => [],
                'position' => []
            ];

            foreach ($jsonDataList as $data) {
                $found = false;
                foreach ($data['people_also_ask'] ?? [] as $item) {
                    if (strtolower(trim($item['question'] ?? '')) === strtolower(trim($question))) {
                        $evolution['total_results'][] = $item['total_results'] ?? null;
                        $evolution['position'][] = $item['position'] ?? null;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $evolution['total_results'][] = null;
                    $evolution['position'][] = null;
                }
            }

            $table[] = [
                'keyword' => $question,
                'total_results' => $evolution['total_results'],
                'position' => $evolution['position']
            ];
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
        // Obtener el registro de Autoseo para conseguir los datos del cliente
        $autoseo = Autoseo::findOrFail($id);

        $filePath = Storage::disk('public')->path("reports/{$filename}");

        if (!File::exists($filePath)) {
            throw new \Exception('Archivo de informe no encontrado');
        }

        try {
            Log::info("Iniciando subida de informe", [
                'id' => $id,
                'filename' => $filename,
                'filesize' => File::size($filePath)
            ]);

            // Log the payload being sent
            $payload = [
                'id' => $id,
                'client_name' => $autoseo->client_name,
                'client_email' => $autoseo->client_email,
                'url' => $autoseo->url
            ];

            Log::info("Payload being sent to upload endpoint", [
                'url' => $this->baseUrl . "/reports/upload",
                'payload' => $payload,
                'filename' => $filename,
                'file_size' => File::size($filePath)
            ]);

            // Primero creamos la instancia de Http con la configuración
            $http = Http::timeout(120)
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
                        CURLOPT_BUFFERSIZE => 128000
                    ]
                ])
                ->retry(3, 5000);

            // Luego adjuntamos el archivo y los datos en una sola llamada multipart
            $response = $http->attach(
                'file',
                File::get($filePath),
                $filename
            )->post($this->baseUrl . "/reports/upload", $payload);

            Log::info("Respuesta de subida", [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 1000),
                'headers' => $response->headers()
            ]);

            if (!$response->successful()) {
                Log::warning("Error en la subida del informe", [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500)
                ]);
                throw new \Exception('Error al subir el informe al servidor. Status: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error("Error en uploadReport", [
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
