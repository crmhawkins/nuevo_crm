<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Clients\Client;
use App\Models\Users\User;
use App\Models\Petitions\Petition;
use App\Models\Alerts\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ElevenLabsController extends Controller
{
    /**
     * Obtener citas disponibles para un rango de fechas
     */
    public function getCitasDisponibles(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'gestor_id' => 'nullable|exists:admin_user,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $fechaInicio = Carbon::parse($request->fecha_inicio);
            $fechaFin = Carbon::parse($request->fecha_fin);

            $query = Cita::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                        ->where('estado', '!=', 'cancelada');

            if ($request->gestor_id) {
                $query->where('gestor_id', $request->gestor_id);
            }

            $citas = $query->with(['cliente', 'gestor'])->get();

            $citasFormateadas = $citas->map(function ($cita) {
                return [
                    'id' => $cita->id,
                    'titulo' => $cita->titulo,
                    'descripcion' => $cita->descripcion,
                    'fecha_inicio' => $cita->fecha_inicio->format('Y-m-d H:i:s'),
                    'fecha_fin' => $cita->fecha_fin->format('Y-m-d H:i:s'),
                    'estado' => $cita->estado,
                    'tipo' => $cita->tipo,
                    'ubicacion' => $cita->ubicacion,
                    'cliente' => $cita->cliente ? [
                        'id' => $cita->cliente->id,
                        'nombre' => $cita->cliente->name,
                        'empresa' => $cita->cliente->company
                    ] : null,
                    'gestor' => $cita->gestor ? [
                        'id' => $cita->gestor->id,
                        'nombre' => $cita->gestor->name
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $citasFormateadas,
                'total' => $citasFormateadas->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agendar una nueva cita
     */
    public function agendarCita(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_inicio' => 'required|date',
                'duracion_minutos' => 'nullable|integer|min:15|max:480', // Entre 15 min y 8 horas
                'tipo' => 'required|in:reunion,llamada,visita,presentacion,seguimiento,otro',
                'cliente_id' => 'nullable|exists:clients,id',
                'gestor_id' => 'nullable|exists:admin_user,id',
                'ubicacion' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:7',
                'notas_internas' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Calcular duración automática basada en el tipo de cita
            $duracionMinutos = $request->duracion_minutos ?? $this->getDuracionPorTipo($request->tipo);
            
            // Calcular fecha de fin
            $fechaInicio = Carbon::parse($request->fecha_inicio);
            $fechaFin = $fechaInicio->copy()->addMinutes($duracionMinutos);

            $cita = Cita::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'tipo' => $request->tipo,
                'cliente_id' => $request->cliente_id,
                'gestor_id' => $request->gestor_id,
                'ubicacion' => $request->ubicacion,
                'color' => $request->color ?? $this->getColorPorTipo($request->tipo),
                'estado' => 'programada',
                'notas_internas' => $request->notas_internas,
                'creado_por' => 1, // Usuario del sistema para citas creadas por Eleven Labs
                'notificar_gestor' => true,
                'minutos_recordatorio' => 15
            ]);

            // Crear alerta para el gestor asignado
            if ($request->gestor_id) {
                $this->crearAlertaCita($cita);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cita agendada exitosamente',
                'data' => [
                    'id' => $cita->id,
                    'titulo' => $cita->titulo,
                    'fecha_inicio' => $cita->fecha_inicio->format('Y-m-d H:i:s'),
                    'fecha_fin' => $cita->fecha_fin->format('Y-m-d H:i:s'),
                    'estado' => $cita->estado
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva petición
     */
    public function crearPeticion(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cliente_id' => 'required|exists:clients,id',
                'gestor_id' => 'required|exists:admin_user,id',
                'nota' => 'required|string',
                'prioridad' => 'nullable|in:baja,media,alta,urgente'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $peticion = Petition::create([
                'admin_user_id' => $request->gestor_id,
                'client_id' => $request->cliente_id,
                'note' => $request->nota,
                'finished' => false
            ]);

            // Crear alerta para el gestor
            $this->crearAlertaPeticion($peticion);

            return response()->json([
                'success' => true,
                'message' => 'Petición creada exitosamente',
                'data' => [
                    'id' => $peticion->id,
                    'cliente' => $peticion->cliente->name,
                    'gestor' => $peticion->usuario->name,
                    'nota' => $peticion->note,
                    'estado' => $peticion->getEstado(),
                    'fecha_creacion' => $peticion->created_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener gestores disponibles
     */
    public function getGestores()
    {
        try {
            $gestores = User::where('access_level_id', 4)
                          ->where('inactive', false)
                          ->select('id', 'name', 'email')
                          ->get();

            return response()->json([
                'success' => true,
                'data' => $gestores
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener clientes disponibles
     */
    public function getClientes()
    {
        try {
            $clientes = Client::where('is_client', 1)
                            ->select('id', 'name', 'company', 'email', 'phone')
                            ->get();

            return response()->json([
                'success' => true,
                'data' => $clientes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar cliente por nombre o empresa
     */
    public function buscarCliente(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'busqueda' => 'required|string|min:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $busqueda = $request->busqueda;
            
            $clientes = Client::where('is_client', 1)
                            ->where(function($query) use ($busqueda) {
                                $query->where('name', 'like', "%{$busqueda}%")
                                      ->orWhere('company', 'like', "%{$busqueda}%")
                                      ->orWhere('email', 'like', "%{$busqueda}%");
                            })
                            ->select('id', 'name', 'company', 'email', 'phone')
                            ->limit(10)
                            ->get();

            return response()->json([
                'success' => true,
                'data' => $clientes,
                'total' => $clientes->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo cliente
     */
    public function crearCliente(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'company' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'gestor_id' => 'nullable|exists:admin_user,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $cliente = Client::create([
                'name' => $request->name,
                'company' => $request->company,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_client' => 1,
                'admin_user_id' => $request->gestor_id ?? 1, // Asignar al gestor o al usuario por defecto
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'data' => [
                    'id' => $cliente->id,
                    'name' => $cliente->name,
                    'company' => $cliente->company,
                    'email' => $cliente->email,
                    'phone' => $cliente->phone
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear alerta para una cita
     */
    private function crearAlertaCita($cita)
    {
        try {
            Alert::create([
                'reference_id' => $cita->id,
                'admin_user_id' => $cita->gestor_id,
                'stage_id' => 10, // ID para alertas de citas (nuevo stage)
                'status_id' => 1, // Activa
                'activation_datetime' => Carbon::now(),
                'cont_postpone' => 0,
                'description' => 'Nueva cita agendada: ' . $cita->titulo . ' para ' . $cita->fecha_inicio->format('d/m/Y H:i')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creando alerta de cita: ' . $e->getMessage());
        }
    }

    /**
     * Crear alerta para una petición
     */
    private function crearAlertaPeticion($peticion)
    {
        try {
            Alert::create([
                'reference_id' => $peticion->id,
                'admin_user_id' => $peticion->admin_user_id,
                'stage_id' => 1, // ID para alertas de peticiones
                'status_id' => 1, // Activa
                'activation_datetime' => Carbon::now(),
                'cont_postpone' => 0,
                'description' => 'Nueva petición de ' . $peticion->cliente->name . ': ' . substr($peticion->note, 0, 50) . '...'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creando alerta de petición: ' . $e->getMessage());
        }
    }

    /**
     * Obtener duración automática por tipo de cita
     */
    private function getDuracionPorTipo($tipo)
    {
        $duraciones = [
            'llamada' => 30,        // 30 minutos
            'reunion' => 60,        // 1 hora
            'visita' => 120,        // 2 horas
            'presentacion' => 90,   // 1.5 horas
            'seguimiento' => 45,    // 45 minutos
            'otro' => 60            // 1 hora por defecto
        ];

        return $duraciones[$tipo] ?? 60;
    }

    /**
     * Obtener color automático por tipo de cita
     */
    private function getColorPorTipo($tipo)
    {
        $colores = [
            'llamada' => '#10b981',      // Verde
            'reunion' => '#3b82f6',      // Azul
            'visita' => '#f59e0b',       // Amarillo
            'presentacion' => '#8b5cf6', // Púrpura
            'seguimiento' => '#06b6d4',  // Cian
            'otro' => '#6b7280'          // Gris
        ];

        return $colores[$tipo] ?? '#3b82f6';
    }
}
