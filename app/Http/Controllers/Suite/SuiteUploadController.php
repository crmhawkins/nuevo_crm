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

        $filename = time() . '_' . $file->getClientOriginalName();

        $path = Storage::putFileAs(
            "justificaciones/{$request->type}",  // carpeta
            $file,                               // archivo
            $filename                            // nombre del archivo
        );

        return response()->json(['path' => $path]);
    }
}
