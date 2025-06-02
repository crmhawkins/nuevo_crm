<?php

namespace App\Http\Controllers\Suite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->store("justificaciones/{$request->type}");

        return response()->json(['path' => $path]);
    }
}
