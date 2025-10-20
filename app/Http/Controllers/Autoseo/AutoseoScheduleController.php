<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\SeoProgramacion;
use App\Models\Autoseo\ClienteServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoseoScheduleController extends Controller
{
    /**
     * Endpoint 1: Retorna clientes programados para hoy
     * GET /api/autoseo/seotoday
     */
    public function getSeoToday()
    {
        try {
            $today = Carbon::today()->toDateString();
            
            Log::info("📅 Consultando clientes programados para: {$today}");

            // Buscar programaciones para hoy que estén pendientes
            $programaciones = SeoProgramacion::with('autoseo')
                ->fechaProgramada($today)
                ->pendientes()
                ->get();

            // Si no hay programaciones, retornar array vacío
            if ($programaciones->isEmpty()) {
                Log::info("ℹ️ No hay clientes programados para hoy");
                return response()->json([]);
            }

            // Construir respuesta con los datos requeridos
            $clientes = [];
            foreach ($programaciones as $programacion) {
                $autoseo = $programacion->autoseo;
                
                if (!$autoseo) {
                    continue;
                }

                $cliente = [
                    'id' => $autoseo->id,
                    'client_name' => $autoseo->client_name,
                    'url' => $autoseo->url,
                    'username' => $autoseo->username,
                    'password' => $autoseo->password,
                    'user_app' => $autoseo->user_app,
                    'password_app' => $autoseo->password_app,
                ];

                // Agregar campos opcionales si existen
                if ($autoseo->client_email) {
                    $cliente['client_email'] = $autoseo->client_email;
                }
                if ($autoseo->pin) {
                    $cliente['pin'] = $autoseo->pin;
                }
                if ($autoseo->json_mesanterior) {
                    $cliente['json_mesanterior'] = $autoseo->json_mesanterior;
                }
                if ($autoseo->company_context) {
                    $cliente['company_context'] = $autoseo->company_context;
                }

                $clientes[] = $cliente;
            }

            Log::info("✅ Se encontraron " . count($clientes) . " clientes programados para hoy");

            return response()->json($clientes);

        } catch (\Exception $e) {
            Log::error("❌ Error obteniendo clientes programados: " . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener clientes programados',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint 2: Retorna servicios y ubicación de un cliente
     * GET /api/autoseo/servicios/{id}
     */
    public function getServicios($id)
    {
        try {
            Log::info("🔍 Consultando servicios para cliente ID: {$id}");

            // Buscar el cliente autoseo
            $autoseo = Autoseo::find($id);

            if (!$autoseo) {
                Log::warning("⚠️ Cliente autoseo ID {$id} no encontrado");
                return response()->json([
                    'error' => 'Cliente no encontrado'
                ], 404);
            }

            // Construir ubicación (formato: "Ciudad, Provincia")
            $ubicacion = $this->buildUbicacion($autoseo);

            // Obtener servicios
            $servicios = $this->getServiciosArray($autoseo);

            $response = [
                'ubicacion' => $ubicacion,
                'servicios' => $servicios
            ];

            Log::info("✅ Servicios obtenidos para cliente {$id}");

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error("❌ Error obteniendo servicios del cliente {$id}: " . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener servicios',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Construye la ubicación en formato "Ciudad, Provincia"
     */
    private function buildUbicacion($autoseo)
    {
        $ciudad = $autoseo->Locality ?? 'Madrid';
        $provincia = $autoseo->AdminDistrict ?? 'Comunidad de Madrid';

        return "{$ciudad}, {$provincia}";
    }

    /**
     * Obtiene el array de servicios del cliente
     */
    private function getServiciosArray($autoseo)
    {
        // Buscar servicios en la tabla cliente_servicios
        $serviciosDb = ClienteServicio::where('autoseo_id', $autoseo->id)
            ->principales()
            ->ordenado()
            ->limit(5)
            ->pluck('nombre_servicio')
            ->toArray();

        // Si tiene servicios guardados, retornarlos
        if (!empty($serviciosDb)) {
            return $serviciosDb;
        }

        // Si no tiene servicios guardados, retornar servicios por defecto basados en su industria/actividad
        return $this->getDefaultServicios($autoseo);
    }

    /**
     * Genera servicios por defecto basados en la información del cliente
     */
    private function getDefaultServicios($autoseo)
    {
        // Servicios genéricos por defecto
        $defaultServicios = [
            'Servicios profesionales',
            'Consultoría',
            'Atención al cliente'
        ];

        // Si tiene company_context, intentar extraer servicios de ahí
        if ($autoseo->company_context) {
            // Aquí podrías implementar lógica más sofisticada para extraer servicios
            // del contexto de la empresa
            return $defaultServicios;
        }

        return $defaultServicios;
    }

    /**
     * Endpoint auxiliar: Programar un cliente para una fecha específica
     * POST /api/autoseo/programar
     */
    public function programarCliente(Request $request)
    {
        try {
            $request->validate([
                'autoseo_id' => 'required|exists:autoseo,id',
                'fecha_programada' => 'required|date',
            ]);

            $programacion = SeoProgramacion::create([
                'autoseo_id' => $request->autoseo_id,
                'fecha_programada' => $request->fecha_programada,
                'estado' => 'pendiente'
            ]);

            Log::info("✅ Cliente {$request->autoseo_id} programado para {$request->fecha_programada}");

            return response()->json([
                'success' => true,
                'message' => 'Cliente programado exitosamente',
                'data' => $programacion
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error programando cliente: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al programar cliente',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint auxiliar: Actualizar estado de una programación
     * POST /api/autoseo/actualizar-estado
     */
    public function actualizarEstado(Request $request)
    {
        try {
            $request->validate([
                'programacion_id' => 'required|exists:seo_programaciones,id',
                'estado' => 'required|in:pendiente,completado,error',
            ]);

            $programacion = SeoProgramacion::findOrFail($request->programacion_id);
            $programacion->update([
                'estado' => $request->estado
            ]);

            Log::info("✅ Estado de programación {$request->programacion_id} actualizado a {$request->estado}");

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => $programacion
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error actualizando estado: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar estado',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint auxiliar: Guardar servicios de un cliente
     * POST /api/autoseo/guardar-servicios
     */
    public function guardarServicios(Request $request)
    {
        try {
            $request->validate([
                'autoseo_id' => 'required|exists:autoseo,id',
                'servicios' => 'required|array|min:1|max:5',
                'servicios.*' => 'required|string|max:255'
            ]);

            // Eliminar servicios anteriores
            ClienteServicio::where('autoseo_id', $request->autoseo_id)->delete();

            // Crear nuevos servicios
            $servicios = [];
            foreach ($request->servicios as $index => $nombreServicio) {
                $servicio = ClienteServicio::create([
                    'autoseo_id' => $request->autoseo_id,
                    'nombre_servicio' => $nombreServicio,
                    'principal' => true,
                    'orden' => $index + 1
                ]);
                $servicios[] = $servicio;
            }

            Log::info("✅ Servicios guardados para cliente {$request->autoseo_id}");

            return response()->json([
                'success' => true,
                'message' => 'Servicios guardados exitosamente',
                'data' => $servicios
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error guardando servicios: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al guardar servicios',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

