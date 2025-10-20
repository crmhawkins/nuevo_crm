<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Autoseo\SeoProgramacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SeoProgramacionController extends Controller
{
    /**
     * Mapeo de estados numéricos a textuales
     * 1 = pendiente
     * 2 = procesando
     * 3 = completado
     * 4 = fallido
     */
    private const STATUS_MAP = [
        1 => 'pendiente',
        2 => 'procesando',
        3 => 'completado',
        4 => 'error'
    ];

    /**
     * Cambia el estado de una programación SEO
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cambiarEstado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:seo_programaciones,id',
            'status' => 'required|integer|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $programacion = SeoProgramacion::findOrFail($request->id);
            $nuevoEstado = self::STATUS_MAP[$request->status];
            $estadoAnterior = $programacion->estado;

            $programacion->estado = $nuevoEstado;
            $programacion->save();

            Log::info("📊 Estado de SEO Programación cambiado", [
                'id' => $programacion->id,
                'autoseo_id' => $programacion->autoseo_id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $nuevoEstado,
                'status_code' => $request->status,
                'fecha_programada' => $programacion->fecha_programada
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'data' => [
                    'id' => $programacion->id,
                    'autoseo_id' => $programacion->autoseo_id,
                    'fecha_programada' => $programacion->fecha_programada,
                    'estado' => $nuevoEstado,
                    'status_code' => $request->status,
                    'updated_at' => $programacion->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error("❌ Error al cambiar estado de SEO Programación", [
                'id' => $request->id,
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene solo las programaciones SEO con prioridad alta (priority = 1)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPriority()
    {
        try {
            $programaciones = SeoProgramacion::with('autoseo:id,client_name,url,client_email')
                ->where('priority', 1)
                ->where('estado', 'pendiente') // Solo los pendientes con prioridad
                ->orderBy('fecha_programada', 'asc')
                ->get();

            // Convertir a array con formato específico
            $data = $programaciones->map(function ($prog) {
                $statusCode = array_search($prog->estado, self::STATUS_MAP);
                return [
                    'id' => $prog->id,
                    'autoseo_id' => $prog->autoseo_id,
                    'client_name' => $prog->autoseo->client_name ?? null,
                    'client_email' => $prog->autoseo->client_email ?? null,
                    'url' => $prog->autoseo->url ?? null,
                    'fecha_programada' => $prog->fecha_programada->format('Y-m-d H:i:s'),
                    'estado' => $prog->estado,
                    'status_code' => $statusCode !== false ? $statusCode : null,
                    'priority' => $prog->priority,
                    'created_at' => $prog->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $prog->updated_at->format('Y-m-d H:i:s'),
                ];
            })->values()->toArray();

            Log::info("🚀 Obteniendo SEOs con prioridad alta", [
                'total' => count($data)
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ], 200);

        } catch (\Exception $e) {
            Log::error("❌ Error al obtener SEOs prioritarios", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las programaciones prioritarias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el listado de programaciones SEO con sus estados
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listar(Request $request)
    {
        try {
            $query = SeoProgramacion::with('autoseo:id,client_name,url');

            // Filtros opcionales
            if ($request->has('autoseo_id')) {
                $query->where('autoseo_id', $request->autoseo_id);
            }

            if ($request->has('estado')) {
                $estado = self::STATUS_MAP[$request->estado] ?? null;
                if ($estado) {
                    $query->where('estado', $estado);
                }
            }

            if ($request->has('fecha')) {
                $query->whereDate('fecha_programada', $request->fecha);
            }

            $programaciones = $query->orderBy('fecha_programada', 'desc')->get();

            // Convertir a array con formato específico
            $data = $programaciones->map(function ($prog) {
                $statusCode = array_search($prog->estado, self::STATUS_MAP);
                return [
                    'id' => $prog->id,
                    'autoseo_id' => $prog->autoseo_id,
                    'client_name' => $prog->autoseo->client_name ?? null,
                    'url' => $prog->autoseo->url ?? null,
                    'fecha_programada' => $prog->fecha_programada->format('Y-m-d H:i:s'),
                    'estado' => $prog->estado,
                    'status_code' => $statusCode !== false ? $statusCode : null,
                    'created_at' => $prog->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $prog->updated_at->format('Y-m-d H:i:s'),
                ];
            })->values()->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ], 200);

        } catch (\Exception $e) {
            Log::error("❌ Error al listar programaciones SEO", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las programaciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

