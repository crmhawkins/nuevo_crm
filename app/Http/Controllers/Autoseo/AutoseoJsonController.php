<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Autoseo\Autoseo;

class AutoseoJsonController extends Controller
{
    public function download($field, $id)
    {
        $autoseo = Autoseo::findOrFail($id);

        // Verifica si el campo existe en el modelo
        if (!in_array($field, ['json_home', 'json_nosotros', 'json_mes_anterior', 'json_mes_actual'])) {
            abort(400, "Campo no permitido");
        }

        $filename = $autoseo->{$field};

        if (!$filename) {
            abort(404, "Archivo no especificado para este cliente");
        }

        $path = storage_path("app/public/autoseo/{$filename}");

        if (!file_exists($path)) {
            abort(404, "Archivo no encontrado");
        }

        return Response::download($path, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function upload($field, $id, Request $request)
    {
        $autoseo = Autoseo::findOrFail($id);

        if ($field == 'json_home') {
            if ($autoseo->json_home) {
                Storage::disk('public')->delete($autoseo->json_home);
            }
            $autoseo->json_home = $request->file('file')->store('autoseo', 'public');
        } else if ($field == 'json_nosotros') {
            if ($autoseo->json_nosotros) {
                Storage::disk('public')->delete($autoseo->json_nosotros);
            }
            $autoseo->json_nosotros = $request->file('file')->store('autoseo', 'public');
        } else if ($field == 'json_mes_anterior') {
            if ($autoseo->json_mes_anterior) {
                Storage::disk('public')->delete($autoseo->json_mes_anterior);
            }
            $autoseo->json_mes_anterior = $request->file('file')->store('autoseo', 'public');
        }

        $autoseo->save();

        return response()->json(['message' => 'Archivo subido correctamente']);
    }
}
