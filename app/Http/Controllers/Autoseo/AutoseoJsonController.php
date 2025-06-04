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
            abort(501, "Cliente no encontrado");
        }

        if ($field === 'reporte') {
            $index = request('index', 0);
            if (!$autoseo->reports || !isset($autoseo->reports[$index])) {
                abort(502, "Reporte no encontrado");
            }

            $report = $autoseo->reports[$index];
            $filename = $report['path'];
            $path = public_path("storage/{$filename}");

            if (!file_exists($path)) {
                abort(503, "Archivo no encontrado");
            }

            return Response::download($path, basename($filename));
        }

        // Verifica si el campo existe en el modelo
        if (!in_array($field, ['home', 'nosotros', 'mesanterior', 'mesactual'])) {
            abort(400, "Campo no permitido");
        }
        $field = 'json_' . $field;
        $filename = $autoseo->{$field};
        if (!$filename) {
            abort(502, "Archivo no especificado para este cliente");
        }

        $path = public_path("storage/{$filename}");

        if (!file_exists($path)) {
            abort(503, "Archivo no encontrado");
        }

        // Sanitizar el nombre para la cabecera (remplaza / y \ por _)
        $safeFilename = preg_replace('/[\/\\\\]/', '_', $filename);

        // Leer el contenido del archivo JSON
        $jsonContent = file_get_contents($path);
        $jsonData = json_decode($jsonContent, true);

        // Añadir las credenciales al JSON
        $jsonData['credentials'] = [
            'username' => $autoseo->username,
            'password' => $autoseo->password
        ];

        $jsonData['wp_login'] = [
            'username' => $autoseo->user_app,
            'password' => $autoseo->password_app
        ];

        // Convertir de nuevo a JSON
        $modifiedJsonContent = json_encode($jsonData, JSON_PRETTY_PRINT);

        return Response::make($modifiedJsonContent, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $safeFilename . '"'
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
                'original_name' => $file->getClientOriginalName()
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

}
