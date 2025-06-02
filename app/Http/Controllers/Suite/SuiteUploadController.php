<?php

namespace App\Http\Controllers\Suite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class SuiteUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'file' => 'required|file',
        ]);

        $file = $request->file('file');


        $path = Storage::putFileAs(
            "justificaciones/{$request->type}",  // carpeta
            $file,                               // archivo
            $file->getClientOriginalName()       // nombre del archivo
        );

        return response()->json(['path' => $path]);
    }

    public function listarArchivos($type)
    {
        $archivos = Storage::files("justificaciones/{$type}");

        $datos = collect($archivos)->map(function ($archivo) {
            return [
                'nombre' => basename($archivo),
                'url' => route('suite.descargar', ['path' => encrypt($archivo)]),
            ];
        });

        return response()->json($datos);
    }

    public function descargarArchivo(Request $request)
    {
        $path = decrypt($request->get('path'));

        if (!Storage::exists($path)) {
            abort(404);
        }

        return Storage::download($path);
    }
}
