<?php

namespace App\Http\Controllers\Justificaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Justificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Http;

class JustificacionesController extends Controller
{
    /**
     * Mostrar el panel de descargas del usuario
     */
    public function index()
    {
        $user = Auth::user();
        $justificaciones = Justificacion::where('admin_user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('justificaciones.index', compact('justificaciones'));
    }

    /**
     * Almacenar una nueva justificación con múltiples archivos
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_justificacion' => 'required|string',
            'nombre_justificacion' => 'required|string',
            'url_campo' => 'required|url',
        ]);

        $user = Auth::user();
        $metadata = [
            'url' => $request->input('url_campo'),
            'estado' => 'pendiente' // Estado: pendiente, procesando, completado, error
        ];

        // Crear la justificación sin archivos (llegarán del servidor externo)
        $justificacion = Justificacion::create([
            'admin_user_id' => $user->id,
            'nombre_justificacion' => $request->nombre_justificacion,
            'tipo_justificacion' => $request->tipo_justificacion,
            'archivos' => json_encode([]), // Inicialmente vacío
            'metadata' => json_encode($metadata)
        ]);

        // Enviar POST a aiapi.hawkins.es/sgbasc
        try {
            $response = Http::post('https://aiapi.hawkins.es/sgbasc', [
                'url' => $request->input('url_campo'),
                'justificacion_id' => $justificacion->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'nombre_justificacion' => $request->nombre_justificacion,
                'tipo_justificacion' => $request->tipo_justificacion,
                'callback_url' => route('justificaciones.receive', $justificacion->id),
                'timestamp' => now()->toDateTimeString()
            ]);

            if ($response->successful()) {
                $metadata['estado'] = 'procesando';
                $justificacion->update(['metadata' => json_encode($metadata)]);
            }
        } catch (\Exception $e) {
            \Log::error('Error al enviar a aiapi.hawkins.es: ' . $e->getMessage());
            $metadata['estado'] = 'error';
            $metadata['error'] = $e->getMessage();
            $justificacion->update(['metadata' => json_encode($metadata)]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Justificación creada. Los archivos serán procesados en breve.',
            'id' => $justificacion->id
        ]);
    }

    /**
     * Recibir archivos del servidor externo
     */
    public function receiveFiles(Request $request, $id)
    {
        $justificacion = Justificacion::findOrFail($id);

        $request->validate([
            'archivo_just' => 'required|file|max:10240',
            'archivo_titularidad' => 'required|file|max:10240',
            'archivo_publicidad' => 'required|file|max:10240',
        ]);

        $archivos = [];
        $userId = $justificacion->admin_user_id;

        // Guardar archivos recibidos
        if ($request->hasFile('archivo_just')) {
            $path = $request->file('archivo_just')->store('justificaciones/' . $userId, 'public');
            $archivos['just'] = $path;
        }

        if ($request->hasFile('archivo_titularidad')) {
            $path = $request->file('archivo_titularidad')->store('justificaciones/' . $userId, 'public');
            $archivos['titularidad'] = $path;
        }

        if ($request->hasFile('archivo_publicidad')) {
            $path = $request->file('archivo_publicidad')->store('justificaciones/' . $userId, 'public');
            $archivos['publicidad'] = $path;
        }

        // Actualizar justificación con los archivos
        $metadata = json_decode($justificacion->metadata, true);
        $metadata['estado'] = 'completado';
        $metadata['fecha_completado'] = now()->toDateTimeString();

        $justificacion->update([
            'archivos' => json_encode($archivos),
            'metadata' => json_encode($metadata)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Archivos recibidos correctamente'
        ]);
    }

    /**
     * Descargar una justificación como ZIP
     */
    public function download($id)
    {
        $user = Auth::user();
        $justificacion = Justificacion::where('id', $id)
            ->where('admin_user_id', $user->id)
            ->firstOrFail();

        $archivos = json_decode($justificacion->archivos, true);

        if (empty($archivos)) {
            return back()->with('error', 'No hay archivos para descargar');
        }

        // Crear un archivo ZIP temporal
        $zipFileName = 'justificacion_' . $justificacion->id . '_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Crear directorio temp si no existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($archivos as $tipo => $path) {
                $fullPath = storage_path('app/public/' . $path);
                if (file_exists($fullPath)) {
                    $fileName = $tipo . '_' . basename($path);
                    $zip->addFile($fullPath, $fileName);
                }
            }

            // Añadir metadata como txt si existe
            $metadata = json_decode($justificacion->metadata, true);
            if (!empty($metadata)) {
                $metadataContent = "INFORMACIÓN ADICIONAL\n\n";
                foreach ($metadata as $key => $value) {
                    $metadataContent .= strtoupper($key) . ": " . $value . "\n";
                }
                $zip->addFromString('info.txt', $metadataContent);
            }

            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Eliminar una justificación
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $justificacion = Justificacion::where('id', $id)
            ->where('admin_user_id', $user->id)
            ->firstOrFail();

        // Eliminar archivos físicos
        $archivos = json_decode($justificacion->archivos, true);
        if (!empty($archivos)) {
            foreach ($archivos as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $justificacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Justificación eliminada correctamente'
        ]);
    }
}
