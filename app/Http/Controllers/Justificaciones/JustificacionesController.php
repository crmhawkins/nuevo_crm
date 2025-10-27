<?php

namespace App\Http\Controllers\Justificaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Justificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Http;
use App\Jobs\ProcessJustificacion;

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
        try {
            \Log::info('📝 Inicio store justificación', [
                'request_all' => $request->all()
            ]);
            
            // Validación diferente para puesto_trabajo_seguro
            if ($request->tipo_justificacion === 'puesto_trabajo_seguro') {
                $request->validate([
                    'tipo_justificacion' => 'required|string',
                    'nombre_justificacion' => 'required|string',
                    'nombre_campo' => 'required|string',
                    'email_campo' => 'required|email',
                    'empresa_campo' => 'required|string',
                ]);
            } else {
                $request->validate([
                    'tipo_justificacion' => 'required|string',
                    'nombre_justificacion' => 'required|string',
                    'url_campo' => 'required|url',
                    'tipo_analisis' => 'required|in:web,ecommerce',
                ]);
            }

            $user = Auth::user();
            \Log::info('✅ Usuario autenticado', ['user_id' => $user->id]);
        // Metadata diferente según el tipo de justificación
        if ($request->tipo_justificacion === 'puesto_trabajo_seguro') {
            $metadata = [
                'nombre' => $request->input('nombre_campo'),
                'email' => $request->input('email_campo'),
                'empresa' => $request->input('empresa_campo'),
                'estado' => 'pendiente'
            ];
        } else {
            $metadata = [
                'url' => $request->input('url_campo'),
                'tipo_analisis' => $request->input('tipo_analisis'),
                'estado' => 'pendiente'
            ];
        }

        // Crear la justificación sin archivos (llegarán del servidor externo)
        $justificacion = Justificacion::create([
            'admin_user_id' => $user->id,
            'nombre_justificacion' => $request->nombre_justificacion,
            'tipo_justificacion' => $request->tipo_justificacion,
            'archivos' => [], // Inicialmente vacío - Laravel lo convierte a JSON automáticamente
            'metadata' => $metadata // Laravel lo convierte a JSON automáticamente
        ]);

        \Log::info('✅ Justificación creada, encolando Job', [
            'justificacion_id' => $justificacion->id,
            'tipo' => $request->tipo_justificacion,
            'metadata' => $metadata
        ]);
        
        try {
            // Encolar Job genérico para TODOS los tipos de justificación
            $job = ProcessJustificacion::dispatch($justificacion->id);
            
            \Log::info('📋 Job encolado exitosamente', [
                'justificacion_id' => $justificacion->id,
                'queue_connection' => config('queue.default'),
                'job_dispatched' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error al encolar Job', [
                'justificacion_id' => $justificacion->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Continuar de todas formas y retornar éxito
            // El usuario verá la justificación en estado pendiente
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Solicitud encolada correctamente. Se procesará en breve.',
            'id' => $justificacion->id
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Error de validación', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('❌ Error general en store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al crear justificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recibir archivos del servidor externo
     */
    public function receiveFiles(Request $request, $id)
    {
        // Log ANTES de todo para ver si llega la petición
        file_put_contents(storage_path('logs/justificaciones_receive.log'), 
            "[" . now() . "] Petición recibida para ID: {$id}\n", 
            FILE_APPEND
        );
        
        try {
            \Log::info("📥 Recibiendo archivos para justificación #{$id}");
            \Log::info("Request method: " . $request->method());
            \Log::info("Request URL: " . $request->fullUrl());
            \Log::info("Todos los inputs: " . json_encode($request->all()));
            \Log::info("Archivos en request: " . json_encode($request->allFiles()));
            
            $justificacion = Justificacion::findOrFail($id);

            // Validación más flexible - verificar que lleguen archivos
            $archivos = [];
            $userId = $justificacion->admin_user_id;
            
            \Log::info("Usuario ID: {$userId}");

            // Guardar archivos recibidos
            if ($request->hasFile('archivo_just')) {
                $path = $request->file('archivo_just')->store('justificaciones/' . $userId, 'public');
                $archivos['just'] = $path;
                \Log::info("✅ Archivo justificación guardado: {$path}");
            } else {
                \Log::warning("⚠️ No se recibió archivo_just");
            }

            if ($request->hasFile('archivo_titularidad')) {
                $path = $request->file('archivo_titularidad')->store('justificaciones/' . $userId, 'public');
                $archivos['titularidad'] = $path;
                \Log::info("✅ Archivo titularidad guardado: {$path}");
            } else {
                \Log::warning("⚠️ No se recibió archivo_titularidad");
            }

            if ($request->hasFile('archivo_publicidad')) {
                $path = $request->file('archivo_publicidad')->store('justificaciones/' . $userId, 'public');
                $archivos['publicidad'] = $path;
                \Log::info("✅ Archivo publicidad guardado: {$path}");
            } else {
                \Log::warning("⚠️ No se recibió archivo_publicidad");
            }

            if (empty($archivos)) {
                \Log::error("❌ No se recibió ningún archivo");
                return response()->json([
                    'success' => false,
                    'message' => 'No se recibieron archivos'
                ], 400);
            }

            // Actualizar justificación con los archivos
            // Manejar datos antiguos (string JSON) y nuevos (array)
            $metadata = $justificacion->metadata;
            if (is_string($metadata)) {
                $metadata = json_decode($metadata, true) ?? [];
            }
            $metadata = $metadata ?? [];
            $metadata['estado'] = 'completado';
            $metadata['fecha_completado'] = now()->toDateTimeString();
            $metadata['archivos_recibidos'] = count($archivos);

            $justificacion->update([
                'archivos' => $archivos, // Laravel lo convierte a JSON automáticamente
                'metadata' => $metadata  // Laravel lo convierte a JSON automáticamente
            ]);

            \Log::info("✅ Justificación actualizada con " . count($archivos) . " archivos");
            \Log::info("Metadata final: " . json_encode($metadata));

            return response()->json([
                'success' => true,
                'message' => 'Archivos recibidos correctamente',
                'archivos_guardados' => count($archivos)
            ]);
            
        } catch (\Exception $e) {
            \Log::error("❌ Error recibiendo archivos: " . $e->getMessage());
            \Log::error("Trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error procesando archivos: ' . $e->getMessage()
            ], 500);
        }
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

        // Manejar datos antiguos (string JSON) y nuevos (array)
        $archivos = $justificacion->archivos;
        if (is_string($archivos)) {
            $archivos = json_decode($archivos, true) ?? [];
        }

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
            // Manejar datos antiguos (string JSON) y nuevos (array)
            $metadata = $justificacion->metadata;
            if (is_string($metadata)) {
                $metadata = json_decode($metadata, true) ?? [];
            }
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
     * Actualizar estado de una justificación desde el servidor externo
     */
    public function updateEstado(Request $request, $id)
    {
        $justificacion = Justificacion::findOrFail($id);

        $estado = $request->input('estado');
        $mensaje = $request->input('mensaje', '');

        // Manejar datos antiguos (string JSON) y nuevos (array)
        $metadata = $justificacion->metadata;
        if (is_string($metadata)) {
            $metadata = json_decode($metadata, true) ?? [];
        }
        $metadata = $metadata ?? [];
        $metadata['estado'] = $estado;
        $metadata['ultimo_mensaje'] = $mensaje;
        $metadata['ultima_actualizacion'] = now()->toDateTimeString();

        $justificacion->update([
            'metadata' => $metadata // Laravel lo convierte a JSON automáticamente
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'estado' => $estado
        ]);
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
        // Manejar datos antiguos (string JSON) y nuevos (array)
        $archivos = $justificacion->archivos;
        if (is_string($archivos)) {
            $archivos = json_decode($archivos, true) ?? [];
        }
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
