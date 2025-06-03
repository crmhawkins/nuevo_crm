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
            abort(602, "Cliente no encontrado");
        }
        // Verifica si el campo existe en el modelo
        if (!in_array($field, ['home', 'nosotros', 'mesanterior', 'mesactual'])) {
            abort(400, "Campo no permitido");
        }
        $field = 'json_' . $field;
        $filename = $autoseo->{$field};
        if (!$filename) {
            abort(601, "Archivo no especificado para este cliente");
        }

        $path = public_path("storage/{$filename}");

        if (!file_exists($path)) {
            abort(600, "Archivo no encontrado");
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

        // Convertir de nuevo a JSON
        $modifiedJsonContent = json_encode($jsonData, JSON_PRETTY_PRINT);

        return Response::make($modifiedJsonContent, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $safeFilename . '"'
        ]);
    }


    public function upload($field, $id, Request $request)
    {
        $autoseo = Autoseo::findOrFail($id);

        if ($field == 'home') {
            if ($autoseo->json_home) {
                Storage::disk('public')->delete($autoseo->json_home);
            }
            $autoseo->json_home = $request->file('file')->store('autoseo', 'public');
        } else if ($field == 'nosotros') {
            if ($autoseo->json_nosotros) {
                Storage::disk('public')->delete($autoseo->json_nosotros);
            }
            $autoseo->json_nosotros = $request->file('file')->store('autoseo', 'public');
        } else if ($field == 'mesanterior') {
            if ($autoseo->json_mes_anterior) {
                Storage::disk('public')->delete($autoseo->json_mes_anterior);
            }
            $autoseo->json_mes_anterior = $request->file('file')->store('autoseo', 'public');
        }

        $autoseo->save();

        return response()->json(['message' => 'Archivo subido correctamente']);
    }
}
