<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\AutoseoReportsModel; // 👈 Importar el modelo correcto
use Illuminate\Http\Request;

class AutoseoReports extends Controller
{
    public function show() {
        return view('autoseo.reports');
    }

    public function login(Request $request) {
        $pin = $request->input('pin');

        $cliente = Autoseo::where('pin', $pin)->first();
        if ($cliente) {
            $reports = AutoseoReportsModel::where('autoseo_id', $cliente->id)
            ->get(['id', 'path', 'created_at', 'autoseo_id']);

            return response()->json([
            'success' => true,
            'reports' => $reports->map(function ($report) {
                return [
                'id' => $report->id,
                'autoseo_id' => $report->autoseo_id,
                'created_at' => $report->created_at,
                ];
            }),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ]);
        }
    }

    public function upload(Request $request) {
        $id = $request->input('id');
        if (!$id) {
            $id = $request->id;
        }
        $file = $request->file('file');

        $cliente = Autoseo::where('id', $id)->first();
        if ($cliente) {
            if ($file) {
                $path = $file->store('autoseo_reports', 'public');
                AutoseoReportsModel::create([
                    'autoseo_id' => $cliente->id,
                    'path' => $path
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Reporte almacenado correctamente',
                    'path' => $path
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se ha proporcionado un archivo'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ]);
        }
    }

    public function showReport($userid, $id, Request $request) {
        $report = AutoseoReportsModel::where('id', $id)->where('autoseo_id', $userid)->first();
        if ($report) {
            $filePath = storage_path('app/public/' . $report->path);
            if (file_exists($filePath)) {
                return response()->file($filePath);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo no encontrado'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Reporte no encontrado'
            ]);
        }
    }
}
