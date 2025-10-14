<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Autoseo\Autoseo;
use Illuminate\Support\Str;

class AutoseoJsonController extends Controller
{
    public function download($field, $id)
    {
        $autoseo = Autoseo::find($id);
        if (!$autoseo) {
            abort(501, 'Cliente no encontrado');
        }

        if ($field === 'reporte') {
            if (!$autoseo->reports || empty($autoseo->reports)) {
                abort(502, 'No hay reportes disponibles');
            }

            // Ordenar reportes por fecha de creación (más reciente primero)
            $reports = collect($autoseo->reports)
                ->sortByDesc(function ($report) {
                    return strtotime($report['creation_date']);
                })
                ->values()
                ->all();

            // Obtener el reporte más reciente
            $latestReport = $reports[0];
            $filename = $latestReport['path'];
            $path = public_path("storage/{$filename}");

            if (!file_exists($path)) {
                abort(503, 'Archivo no encontrado');
            }

            return Response::download($path, $latestReport['original_name']);
        }

        // Verifica si el campo existe en el modelo
        if (!in_array($field, ['home', 'nosotros', 'mesanterior', 'mesactual'])) {
            abort(400, 'Campo no permitido');
        }
        $field = 'json_' . $field;
        $filename = $autoseo->{$field};
        if (!$filename) {
            abort(502, 'Archivo no especificado para este cliente');
        }

        $path = public_path("storage/{$filename}");

        if (!file_exists($path)) {
            abort(503, 'Archivo no encontrado');
        }

        // Sanitizar el nombre para la cabecera (remplaza / y \ por _)
        $safeFilename = preg_replace('/[\/\\\\]/', '_', $filename);

        // Leer el contenido del archivo JSON
        $jsonContent = file_get_contents($path);
        $jsonData = json_decode($jsonContent, true);

        // Añadir las credenciales al JSON
        $jsonData['credentials'] = [
            'username' => $autoseo->username,
            'password' => $autoseo->password,
        ];

        $jsonData['wp_login'] = [
            'username' => $autoseo->user_app,
            'password' => $autoseo->password_app,
        ];

        if ($autoseo->CompanyName) {
            $jsonData['CompanyName'] = $autoseo->CompanyName;
            $jsonData['AddressLine1'] = $autoseo->AddressLine1;
            $jsonData['Locality'] = $autoseo->Locality;
            $jsonData['AdminDistrict'] = $autoseo->AdminDistrict;
            $jsonData['PostalCode'] = $autoseo->PostalCode;
            $jsonData['CountryRegion'] = $autoseo->CountryRegion;
        }



        // Convertir de nuevo a JSON
        $modifiedJsonContent = json_encode($jsonData, JSON_PRETTY_PRINT);

        return Response::make($modifiedJsonContent, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $safeFilename . '"',
        ]);
    }

    public function upload($field, $id, Request $request)
    {
        $autoseo = Autoseo::find($id);
        if (!$autoseo) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        // Validar que se subió un archivo válido
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return response()->json(['error' => 'Archivo no válido o no enviado'], 400);
        }

        $file = $request->file('file');

        if ($field == 'home') {
            if ($autoseo->json_home) {
                Storage::disk('public')->delete($autoseo->json_home);
            }
            $autoseo->json_home = $file->store('autoseo', 'public');
            $autoseo->json_home_update = now();
        } elseif ($field == 'nosotros') {
            if ($autoseo->json_nosotros) {
                Storage::disk('public')->delete($autoseo->json_nosotros);
            }
            $autoseo->json_nosotros = $file->store('autoseo', 'public');
            $autoseo->json_nosotros_update = now();
        } elseif ($field == 'mesanterior') {
            if ($autoseo->json_mes_anterior) {
                Storage::disk('public')->delete($autoseo->json_mes_anterior);
            }
            $autoseo->json_mes_anterior = $file->store('autoseo', 'public');
            $autoseo->json_mes_anterior_update = now();
        } elseif ($field == 'reporte') {
            try {
                // Asegurar el directorio de reportes
                Storage::disk('public')->makeDirectory('autoseo/reports');

                $newReportPath = $file->store('autoseo/reports', 'public');
                if (!$newReportPath) {
                    throw new \Exception('Error al guardar el archivo');
                }

                $reports = $autoseo->reports ?? [];
                $reports[] = [
                    'path' => $newReportPath,
                    'creation_date' => now()->toDateTimeString(),
                    'original_name' => $file->getClientOriginalName(),
                ];
                $autoseo->reports = $reports;
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al subir el reporte: ' . $e->getMessage()], 500);
            }
        } else {
            return response()->json(['error' => 'Campo no permitido'], 400);
        }

        // Guardar cambios
        if (!$autoseo->save()) {
            return response()->json(['error' => 'Error al guardar en la base de datos'], 500);
        }

        return response()->json(['message' => 'Archivo subido correctamente']);
    }

    public function uploadJson(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'json' => 'required|file|mimetypes:application/json,text/plain',
        ]);

        $id = $request->input('id');
        $jsonFile = $request->file('json');

        // Nombre del archivo y ruta relativa
        $filename = uniqid() . '_' . $id . '.json';
        $relativePath = "autoseo/json/$filename";

        // Leer el contenido del archivo JSON
        $fileContent = $jsonFile->get();

        if (empty($fileContent)) {
            return response()->json(['error' => 'El archivo JSON está vacío'], 400);
        }

        $jsonContent = json_decode($fileContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'El archivo no es un JSON válido: ' . json_last_error_msg()], 400);
        }

        // Añadir campo de fecha al contenido JSON
        $jsonContent['uploaded_at'] = now()->toDateTimeString();

        // Guardar archivo en storage/app/autoseo/json con el contenido modificado
        Storage::disk('public')->makeDirectory('autoseo/json');
        $saved = Storage::disk('public')->put('autoseo/json/' . $filename, json_encode($jsonContent, JSON_PRETTY_PRINT));

        if (!$saved) {
            return response()->json(['error' => 'Error al guardar el archivo'], 500);
        }

        // Buscar el modelo
        $autoseo = Autoseo::find($id);

        // Actualizar json_storage (array con id y path)
        $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];
        $jsonStorage[] = [
            'id' => $id,
            'path' => $relativePath,
        ];

        // Guardar cambios
        $autoseo->json_mesanterior = $relativePath;
        $autoseo->json_storage = json_encode($jsonStorage);
        $autoseo->save();

        return response()->json([
            'message' => 'Archivo JSON subido y campos actualizados correctamente.',
            'path' => $relativePath,
        ]);
    }

    public function uploadJsonCompetencia(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'json' => 'required|file|mimetypes:application/json,text/plain',
        ]);

        $id = $request->input('id');
        $jsonFile = $request->file('json');

        // Verificar que el Autoseo exista
        $autoseo = Autoseo::find($id);
        if (!$autoseo) {
            return response()->json(['error' => 'Autoseo no encontrado'], 404);
        }

        // Crear nombre único y ruta relativa
        $filename = uniqid() . '_' . $id . '.json';
        $relativePath = "autoseo/json/$filename";

        // Guardar el archivo en storage/public/autoseo/json
        Storage::disk('public')->makeDirectory('autoseo/json');
        Storage::disk('public')->putFileAs('autoseo/json', $jsonFile, $filename);

        // Obtener y decodificar json_storage actual
        $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];

        // Añadir nuevo archivo al array
        $jsonStorage[] = [
            'path' => $relativePath,
            'uploaded_at' => now()->toDateTimeString(),
        ];

        // Guardar json_storage actualizado
        $autoseo->json_storage = json_encode($jsonStorage, JSON_UNESCAPED_UNICODE);
        $autoseo->save();

        return response()->json([
            'message' => 'Archivo JSON subido y registrado correctamente.',
            'path' => $relativePath,
            'json_storage' => $jsonStorage,
        ]);
    }



    public function getLastJson(Request $request)
    {
        $autoseo = \App\Models\Autoseo\Autoseo::find($request->id);
        if (!$autoseo) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $json = $autoseo->json_mesanterior;

        if (!$json) {
            return response()->json(['error' => 'No hay JSON disponible'], 404);
        }

        return response()->download(storage_path('app/public/' . $json), 'autoseo_' . $autoseo->id . '_' . date('Y-m-d') . '.json', ['Content-Type' => 'application/json']);
    }

    public function getJsonStorage(Request $request)
    {
        // Aumentar tiempo de ejecución para generar ZIP
        set_time_limit(300);
        
        $id = $request->input('id');
        $autoseo = Autoseo::find($id);
        if (!$autoseo) {
            \Log::debug('Cliente no encontrado', ['id' => $id]);
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];
        \Log::debug('json_storage count', ['count' => count($jsonStorage), 'jsonStorage' => $jsonStorage]);
        $zip = new \ZipArchive();
        $zipName = 'autoseo_' . $autoseo->id . '_' . date('Y-m-d') . '.zip';
        $zipPath = storage_path('app/public/' . $zipName);

        $addedFiles = 0;
        if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
            foreach ($jsonStorage as $json) {
                if (isset($json['path']) && \Storage::disk('public')->exists($json['path'])) {
                    $zip->addFile(storage_path('app/public/' . $json['path']), basename($json['path']));
                    $addedFiles++;
                    \Log::debug('Archivo agregado al ZIP', ['path' => $json['path']]);
                } else {
                    \Log::debug('Archivo NO encontrado para agregar al ZIP', ['path' => $json['path'] ?? null]);
                }
            }
            $zip->close();

            \Log::debug('Total archivos agregados al ZIP', ['addedFiles' => $addedFiles]);
            if ($addedFiles === 0) {
                // El ZIP está vacío
                if (file_exists($zipPath)) {
                    unlink($zipPath);
                }
                \Log::debug('ZIP vacío, no se agregó ningún archivo');
                return response()->json(['error' => 'No se encontró ningún archivo JSON físico para este cliente. El ZIP está vacío.'], 404);
            }

            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        }

        \Log::error('Error al crear el archivo ZIP', ['zipPath' => $zipPath]);
        return response()->json(['error' => 'Error al crear el archivo ZIP'], 500);
    }

    /**
     * Sube un JSON y genera automáticamente el informe SEO
     * Este endpoint está diseñado para ser llamado desde Python
     */
    public function uploadJsonAndGenerateReport(Request $request)
    {
        // Aumentar el tiempo de ejecución a 5 minutos
        set_time_limit(300);
        ini_set('max_execution_time', 300);
        
        try {
            // Validar la petición
            $request->validate([
                'id' => 'required|integer',
                'json' => 'required|file|mimetypes:application/json,text/plain',
            ]);

            $id = $request->input('id');
            $jsonFile = $request->file('json');

            // Verificar que el cliente existe
            $autoseo = Autoseo::find($id);
            if (!$autoseo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cliente no encontrado',
                    'id' => $id
                ], 404);
            }

            \Log::info("📤 Iniciando upload y generación de informe para cliente: {$autoseo->client_name} (ID: {$id})");

            // 1. Subir el JSON
            $filename = uniqid() . '_' . $id . '.json';
            $relativePath = "autoseo/json/$filename";

            // Leer el contenido del archivo JSON
            $fileContent = $jsonFile->get();

            if (empty($fileContent)) {
                return response()->json([
                    'success' => false,
                    'error' => 'El archivo JSON está vacío'
                ], 400);
            }

            $jsonContent = json_decode($fileContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'error' => 'El archivo no es un JSON válido: ' . json_last_error_msg()
                ], 400);
            }

            // Añadir campo de fecha al contenido JSON
            $jsonContent['uploaded_at'] = now()->toDateTimeString();

            // Incluir el contexto empresarial en el JSON si existe
            if ($autoseo->company_context) {
                $jsonContent['company_context'] = $autoseo->company_context;
            }

            // Guardar archivo en storage
            Storage::disk('public')->makeDirectory('autoseo/json');
            $saved = Storage::disk('public')->put('autoseo/json/' . $filename, json_encode($jsonContent, JSON_PRETTY_PRINT));

            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al guardar el archivo JSON'
                ], 500);
            }

            // Actualizar json_storage
            $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];
            $jsonStorage[] = [
                'id' => $id,
                'path' => $relativePath,
                'uploaded_at' => now()->toDateTimeString(),
            ];

            // Guardar cambios
            $autoseo->json_mesanterior = $relativePath;
            $autoseo->json_storage = json_encode($jsonStorage);
            $autoseo->save();

            \Log::info("✅ JSON subido correctamente: {$relativePath}");

            // 2. Generar el informe automáticamente
            \Log::info("📊 Generando informe SEO automáticamente...");

            $reportsController = new AutoseoReports();
            $reportRequest = new Request(['id' => $id]);
            $reportResponse = $reportsController->generateJsonOnlyReport($reportRequest);
            $reportData = $reportResponse->getData(true);

            if (!$reportData['success']) {
                \Log::warning("⚠️ No se pudo generar el informe: " . $reportData['message']);
                // Aunque falle el informe, el JSON se subió correctamente
                return response()->json([
                    'success' => true,
                    'message' => 'JSON subido correctamente, pero no se pudo generar el informe',
                    'json_upload' => [
                        'path' => $relativePath,
                        'uploaded_at' => now()->toDateTimeString(),
                    ],
                    'report_generation' => [
                        'success' => false,
                        'error' => $reportData['message']
                    ]
                ]);
            }

            \Log::info("✅ ¡Proceso completado exitosamente!");

            // Retornar respuesta completa
            return response()->json([
                'success' => true,
                'message' => 'JSON subido e informe generado correctamente',
                'client' => [
                    'id' => $autoseo->id,
                    'name' => $autoseo->client_name,
                    'email' => $autoseo->client_email,
                    'url' => $autoseo->url,
                    'company_context' => $autoseo->company_context,
                ],
                'json_upload' => [
                    'path' => $relativePath,
                    'filename' => $filename,
                    'uploaded_at' => now()->toDateTimeString(),
                    'total_jsons_stored' => count($jsonStorage),
                ],
                'report_generation' => [
                    'success' => true,
                    'report_id' => $reportData['data']['report_id'],
                    'report_url' => $reportData['data']['url'],
                    'report_path' => $reportData['data']['path'],
                    'summary' => $reportData['data']['summary'],
                    'trends' => $reportData['data']['trends'],
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("❌ Error en uploadJsonAndGenerateReport: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Error en el proceso: ' . $e->getMessage()
            ], 500);
        }
    }
}
