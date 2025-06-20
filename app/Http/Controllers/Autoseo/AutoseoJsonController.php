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

        // Guardar archivo en storage/app/autoseo/json
        Storage::disk('public')->makeDirectory('autoseo/json');
        Storage::disk('public')->putFileAs('autoseo/json', $jsonFile, $filename);

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
        $autoseo = Autoseo::find($request->id);
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
        $autoseo = Autoseo::find($request->id);
        if (!$autoseo) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];        $zip = new \ZipArchive();
        $zipName = 'autoseo_' . $autoseo->id . '_' . date('Y-m-d') . '.zip';
        $zipPath = storage_path('app/public/' . $zipName);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
            foreach ($jsonStorage as $json) {
                if (isset($json['path']) && Storage::disk('public')->exists($json['path'])) {
                    $zip->addFile(storage_path('app/public/' . $json['path']), basename($json['path']));
                }
            }
            $zip->close();

            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        }

        return response()->json(['error' => 'Error al crear el archivo ZIP'], 500);
    }
}
