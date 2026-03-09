<?php

namespace App\Http\Controllers\Justificaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Justificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
            if (!$request->filled('periodo') && $request->filled('fecha_periodo_prestacion_campo')) {
                $request->merge(['periodo' => $request->input('fecha_periodo_prestacion_campo')]);
            }

            Log::info('📝 Inicio store justificación', [
                'request_all' => $request->all()
            ]);

            // Validación diferente para cada tipo
            if ($request->tipo_justificacion === 'puesto_trabajo_seguro') {
                $request->validate([
                    'tipo_justificacion' => 'required|string',
                    'nombre_justificacion' => 'required|string',
                    'nombre_campo' => 'required|string',
                    'email_campo' => 'required|email',
                    'empresa_campo' => 'required|string',
                ]);
            } elseif ($request->tipo_justificacion === 'presencia_avanzada_2') {
                $request->validate([
                    'tipo_justificacion' => 'required|string',
                    'nombre_justificacion' => 'required|string',
                    'kd_campo' => 'required|string',
                    'fecha_campo' => 'required|date',
                    'nombre_presencia_campo' => 'required|string',
                    'url_presencia_campo' => 'required|url',
                    'keyword_campo' => 'nullable|string',
                    'phone_campo' => 'required|string',
                    'email_presencia_campo' => 'required|email',
                    'address_campo' => 'required|string',
                    'descripcion_campo' => 'required|string',
                ]);
            } elseif ($request->tipo_justificacion === 'crm_erp_factura') {
                $request->validate([
                    'tipo_justificacion' => 'required|string',
                    'nombre_justificacion' => 'required|string',
                    'tipo_sistema_campo' => 'required|in:crm,erp,factura',
                    'url_crm_campo' => 'required|url',
                    'username_campo' => 'nullable|string',
                    'password_campo' => 'nullable|string',
                ]);
            } else {
                $request->validate([
                    'tipo_justificacion' => 'required|string',
                    'nombre_justificacion' => 'required|string',
                    'url_campo' => 'required|url',
                    'tipo_analisis' => 'required|in:web,ecommerce',
                    'periodo' => 'required|date',
                ]);
            }

            $user = Auth::user();
            Log::info('✅ Usuario autenticado', ['user_id' => $user->id]);
        // Metadata diferente según el tipo de justificación
        if ($request->tipo_justificacion === 'puesto_trabajo_seguro') {
            $metadata = [
                'nombre' => $request->input('nombre_campo'),
                'email' => $request->input('email_campo'),
                'empresa' => $request->input('empresa_campo'),
                'estado' => 'pendiente'
            ];
        } elseif ($request->tipo_justificacion === 'presencia_avanzada_2') {
            $metadata = [
                'kd' => $request->input('kd_campo'),
                'fecha' => $request->input('fecha_campo'),
                'nombre' => $request->input('nombre_presencia_campo'),
                'url' => $request->input('url_presencia_campo'),
                'keyword_principal' => $request->input('keyword_campo'),
                'phone' => $request->input('phone_campo'),
                'email' => $request->input('email_presencia_campo'),
                'address' => $request->input('address_campo'),
                'descripcion' => $request->input('descripcion_campo'),
                'estado' => 'pendiente'
            ];
        } elseif ($request->tipo_justificacion === 'crm_erp_factura') {
            $metadata = [
                'tipo_sistema' => $request->input('tipo_sistema_campo'),
                'url' => $request->input('url_crm_campo'),
                'username' => $request->input('username_campo', 'admin'),
                'password' => $request->input('password_campo', '12345678'),
                'estado' => 'pendiente'
            ];
        } else {
            $metadata = [
                'url' => $request->input('url_campo'),
                'tipo_analisis' => $request->input('tipo_analisis'),
                'periodo' => $request->input('periodo', $request->input('fecha_periodo_prestacion_campo')),
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

        Log::info('✅ Justificación creada, encolando Job', [
            'justificacion_id' => $justificacion->id,
            'tipo' => $request->tipo_justificacion,
            'metadata' => $metadata
        ]);

        try {
            // Encolar Job genérico para TODOS los tipos de justificación
            $job = ProcessJustificacion::dispatch($justificacion->id);

            Log::info('📋 Job encolado exitosamente', [
                'justificacion_id' => $justificacion->id,
                'queue_connection' => config('queue.default'),
                'job_dispatched' => true
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error al encolar Job', [
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
            Log::error('❌ Error de validación', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error general en store', [
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
        $logMessage = "[" . now() . "] Petición recibida para ID: {$id}\n";
        $logMessage .= "Method: " . $request->method() . "\n";
        $logMessage .= "URL: " . $request->fullUrl() . "\n";
        $logMessage .= "Headers: " . json_encode($request->headers->all()) . "\n";
        $logMessage .= "Files: " . json_encode(array_keys($request->allFiles())) . "\n";
        $logMessage .= "All data: " . json_encode($request->all()) . "\n";
        $logMessage .= "---\n";
        
        file_put_contents(storage_path('logs/justificaciones_receive.log'),
            $logMessage,
            FILE_APPEND
        );

        try {
            Log::info("📥 Recibiendo archivos para justificación #{$id}");
            Log::info("Request method: " . $request->method());
            Log::info("Request URL: " . $request->fullUrl());
            Log::info("Request headers: " . json_encode($request->headers->all()));
            Log::info("Todos los inputs: " . json_encode($request->all()));
            Log::info("Archivos en request: " . json_encode(array_keys($request->allFiles())));

            $justificacion = Justificacion::findOrFail($id);

            // Validación más flexible - verificar que lleguen archivos
            $archivos = [];
            $userId = $justificacion->admin_user_id;

            Log::info("Usuario ID: {$userId}");

            // Guardar archivos recibidos (acepta cualquier archivo con nombre archivo_*)
            // Mapeo de nombres amigables
            $archivoMap = [
                'archivo_just' => 'just',
                'archivo_titularidad' => 'titularidad',
                'archivo_publicidad' => 'publicidad',
                'archivo_competencia' => 'competencia',
                'archivo_analytics' => 'analytics',
                'archivo_datos' => 'datos',
                'archivo_excel' => 'excel',
                'archivo_extra' => 'extra',
            ];

            // Procesar todos los archivos que empiecen con 'archivo_'
            foreach ($request->allFiles() as $fieldName => $file) {
                if (str_starts_with($fieldName, 'archivo_')) {
                    // Usar nombre del mapeo o el nombre del campo
                    $nombreArchivo = $archivoMap[$fieldName] ?? str_replace('archivo_', '', $fieldName);

                    // Guardar archivo
                    $path = $file->store('justificaciones/' . $userId, 'public');
                    $archivos[$nombreArchivo] = $path;

                    Log::info("✅ Archivo '{$nombreArchivo}' guardado: {$path}", [
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                        'original_name' => $file->getClientOriginalName()
                    ]);
                }
            }

            // También aceptar archivo único sin prefijo 'archivo_' (para compatibilidad con servidor Python)
            if (empty($archivos)) {
                foreach ($request->allFiles() as $fieldName => $file) {
                    // Si es un archivo PDF directo, aceptarlo como 'just'
                    if ($file->getClientOriginalExtension() === 'pdf' || $file->getMimeType() === 'application/pdf') {
                        $path = $file->store('justificaciones/' . $userId, 'public');
                        $archivos['just'] = $path;
                        
                        Log::info("✅ Archivo PDF único guardado como 'just': {$path}", [
                            'extension' => $file->getClientOriginalExtension(),
                            'size' => $file->getSize(),
                            'original_name' => $file->getClientOriginalName(),
                            'field_name' => $fieldName
                        ]);
                        break; // Solo tomar el primer PDF si hay múltiples
                    }
                }
            }

            if (empty($archivos)) {
                Log::error("❌ No se recibió ningún archivo");
                Log::error("Archivos recibidos: " . json_encode(array_keys($request->allFiles())));
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
            
            // Forzar actualización del estado a completado
            $metadata['estado'] = 'completado';
            $metadata['fecha_completado'] = now()->toDateTimeString();
            $metadata['archivos_recibidos'] = count($archivos);
            $metadata['archivos_enviados'] = true;

            Log::info("📝 Antes de actualizar - Estado a guardar: completado");
            Log::info("📝 Metadata antes de guardar: " . json_encode($metadata));

            // Usar update() explícito para asegurar que se guarde correctamente
            $updated = $justificacion->update([
                'archivos' => $archivos,
                'metadata' => $metadata
            ]);

            Log::info("📝 Resultado del update: " . ($updated ? 'TRUE' : 'FALSE'));
            
            // Refrescar el modelo desde la base de datos para verificar
            $justificacion->refresh();
            
            // Verificar el estado guardado directamente desde la BD
            $metadataVerificada = $justificacion->metadata;
            if (is_string($metadataVerificada)) {
                $metadataVerificada = json_decode($metadataVerificada, true) ?? [];
            }

            Log::info("✅ Justificación actualizada con " . count($archivos) . " archivos");
            Log::info("🔍 Estado guardado en BD: " . ($metadataVerificada['estado'] ?? 'NO GUARDADO'));
            Log::info("🔍 Metadata final desde BD: " . json_encode($metadataVerificada));
            
            // Verificación adicional: leer directamente desde la base de datos
            $justificacionRaw = \DB::table('justificacions')
                ->where('id', $id)
                ->first();
            
            if ($justificacionRaw) {
                $metadataRaw = json_decode($justificacionRaw->metadata, true) ?? [];
                Log::info("🔍 Estado desde consulta RAW: " . ($metadataRaw['estado'] ?? 'NO ENCONTRADO'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Archivos recibidos correctamente',
                'archivos_guardados' => count($archivos)
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error recibiendo archivos: " . $e->getMessage());
            Log::error("Trace: " . $e->getTraceAsString());

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
                    // Convertir arrays/objetos a JSON, otros valores a string
                    if (is_array($value) || is_object($value)) {
                        $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    }
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
