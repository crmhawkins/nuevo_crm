<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetConcept;
use App\Models\Budgets\BudgetReferenceAutoincrement;
use App\Models\Budgets\BudgetSend;
use App\Models\Cita;
use App\Models\Clients\Client;
use App\Models\Logs\LogsEmail;
use App\Models\Petitions\Petition;
use App\Models\Projects\Project;
use App\Models\Services\ServiceCategories;
use App\Models\Users\User;
use App\Mail\MailBudget;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ElevenLabsController extends Controller
{
    /**
     * Obtener horarios disponibles para agendar citas
     */
    public function getCitasDisponibles(Request $request)
    {
        try {
            Log::info('=== INICIO getCitasDisponibles ===');
            Log::info('Datos recibidos:', $request->all());

            $validator = Validator::make($request->all(), [
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'gestor_id' => 'nullable|exists:admin_user,id',
                'duracion_minutos' => 'nullable|integer|min:15|max:480'
            ]);

            if ($validator->fails()) {
                Log::error('Validación fallida:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $fechaInicio = Carbon::parse($request->fecha_inicio);
            $fechaFin = Carbon::parse($request->fecha_fin);
            $duracionMinutos = $request->duracion_minutos ?? 60; // Por defecto 1 hora
            $gestorId = $request->gestor_id;

            Log::info('Parámetros procesados:', [
                'fecha_inicio' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_fin' => $fechaFin->format('Y-m-d H:i:s'),
                'duracion_minutos' => $duracionMinutos,
                'gestor_id' => $gestorId
            ]);

            // Obtener citas existentes en el rango de fechas
            $query = Cita::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                        ->where('estado', '!=', 'cancelada');

            if ($gestorId) {
                $query->where('gestor_id', $gestorId);
            }

            $citasExistentes = $query->get();
            Log::info('Citas existentes encontradas:', [
                'total' => $citasExistentes->count(),
                'citas' => $citasExistentes->map(function($cita) {
                    return [
                        'id' => $cita->id,
                        'fecha_inicio' => $cita->fecha_inicio,
                        'fecha_fin' => $cita->fecha_fin,
                        'estado' => $cita->estado
                    ];
                })->toArray()
            ]);

            // Generar horarios disponibles
            $horariosDisponibles = $this->generarHorariosDisponibles($fechaInicio, $fechaFin, $citasExistentes, $duracionMinutos);

            Log::info('Generación completada', [
                'total_horarios' => count($horariosDisponibles),
                'primeros_3_horarios' => array_slice($horariosDisponibles, 0, 3)
            ]);

            return response()->json([
                'success' => true,
                'data' => $horariosDisponibles,
                'total' => count($horariosDisponibles),
                'filtros' => [
                    'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                    'fecha_fin' => $fechaFin->format('Y-m-d'),
                    'duracion_minutos' => $duracionMinutos,
                    'gestor_id' => $gestorId
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en getCitasDisponibles:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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
            Log::info('=== INICIO agendarCita ===');
            Log::info('Datos recibidos para agendar cita:', $request->all());

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
                Log::error('Validación fallida en agendarCita:', $validator->errors()->toArray());
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

            Log::info('Parámetros calculados:', [
                'fecha_inicio' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_fin' => $fechaFin->format('Y-m-d H:i:s'),
                'duracion_minutos' => $duracionMinutos,
                'tipo' => $request->tipo,
                'cliente_id' => $request->cliente_id,
                'gestor_id' => $request->gestor_id
            ]);

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

            Log::info('Cita creada exitosamente:', [
                'id' => $cita->id,
                'titulo' => $cita->titulo,
                'fecha_inicio' => $cita->fecha_inicio->format('Y-m-d H:i:s'),
                'fecha_fin' => $cita->fecha_fin->format('Y-m-d H:i:s'),
                'estado' => $cita->estado,
                'cliente_id' => $cita->cliente_id,
                'gestor_id' => $cita->gestor_id
            ]);

            // Crear alerta para el gestor asignado
            if ($request->gestor_id) {
                Log::info('Creando alerta para gestor:', ['gestor_id' => $request->gestor_id]);
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
            Log::error('Error en agendarCita:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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
            // Limpiar y formatear los datos antes de la validación
            $data = $request->all();
            
            // Log para debugging
            Log::info('Datos recibidos para crear cliente', $data);
            
            // Limpiar el teléfono: quitar espacios, guiones y caracteres especiales
            if (isset($data['phone']) && !empty($data['phone'])) {
                $data['phone'] = preg_replace('/[^0-9+]/', '', $data['phone']);
            }
            
            // Limpiar el email: quitar espacios
            if (isset($data['email']) && !empty($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            
            // Limpiar el nombre: quitar espacios extra
            if (isset($data['name']) && !empty($data['name'])) {
                $data['name'] = trim(preg_replace('/\s+/', ' ', $data['name']));
            }
            
            // Limpiar la empresa: quitar espacios extra
            if (isset($data['company']) && !empty($data['company'])) {
                $data['company'] = trim(preg_replace('/\s+/', ' ', $data['company']));
            }
            
            // Log de datos limpios
            Log::info('Datos limpios para crear cliente', $data);

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'company' => 'nullable|string|max:255',
                'email' => 'nullable|string|max:255',
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
                'name' => $data['name'],
                'company' => $data['company'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'is_client' => 0,
                'admin_user_id' => $data['gestor_id'] ?? 1, // Asignar al gestor o al usuario por defecto
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
            Log::info('=== INICIO crearAlertaCita ===');
            Log::info('Datos de la cita para alerta:', [
                'cita_id' => $cita->id,
                'gestor_id' => $cita->gestor_id,
                'titulo' => $cita->titulo,
                'fecha_inicio' => $cita->fecha_inicio->format('Y-m-d H:i:s')
            ]);

            $alerta = Alert::create([
                'reference_id' => $cita->id,
                'admin_user_id' => $cita->gestor_id,
                'stage_id' => 15, // Alerta Custom - Para alertas de ElevenLabs
                'status_id' => 1, // Activa
                'activation_datetime' => Carbon::now(),
                'cont_postpone' => 0,
                'description' => '[ELEVENLABS] Nueva cita agendada: ' . $cita->titulo . ' para ' . $cita->fecha_inicio->format('d/m/Y H:i')
            ]);

            Log::info('Alerta creada exitosamente:', [
                'alerta_id' => $alerta->id,
                'reference_id' => $alerta->reference_id,
                'admin_user_id' => $alerta->admin_user_id,
                'description' => $alerta->description
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando alerta de cita:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'cita_id' => $cita->id ?? 'N/A'
            ]);
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
                'stage_id' => 15, // Alerta Custom - Para alertas de ElevenLabs
                'status_id' => 1, // Activa
                'activation_datetime' => Carbon::now(),
                'cont_postpone' => 0,
                'description' => '[ELEVENLABS] Nueva petición de ' . $peticion->cliente->name . ': ' . substr($peticion->note, 0, 50) . '...'
            ]);
            
            Log::info('Alerta de ElevenLabs creada:', [
                'peticion_id' => $peticion->id,
                'cliente' => $peticion->cliente->name,
                'stage_id' => 15,
                'description' => '[ELEVENLABS] Nueva petición de ' . $peticion->cliente->name
            ]);
        } catch (\Exception $e) {
            Log::error('Error creando alerta de petición ElevenLabs: ' . $e->getMessage());
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

    /**
     * Generar horarios disponibles considerando horarios de trabajo y citas existentes
     */
    private function generarHorariosDisponibles($fechaInicio, $fechaFin, $citasExistentes, $duracionMinutos)
    {
        Log::info('=== INICIO generarHorariosDisponibles ===');
        
        $horariosDisponibles = [];
        $fechaActual = $fechaInicio->copy();

        // Horarios de trabajo: 09:30-13:30 y 16:30-18:30
        $horariosTrabajo = [
            ['inicio' => 9, 'minuto_inicio' => 30, 'fin' => 13, 'minuto_fin' => 30], // Mañana
            ['inicio' => 16, 'minuto_inicio' => 30, 'fin' => 18, 'minuto_fin' => 30] // Tarde
        ];

        Log::info('Parámetros de generación:', [
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'duracion_minutos' => $duracionMinutos,
            'citas_existentes_count' => $citasExistentes->count()
        ]);

        $diasProcesados = 0;
        $slotsGenerados = 0;

        while ($fechaActual->lte($fechaFin)) {
            $diasProcesados++;
            
            // Saltar fines de semana
            if ($fechaActual->isWeekend()) {
                Log::info('Saltando fin de semana: ' . $fechaActual->format('Y-m-d'));
                $fechaActual->addDay();
                continue;
            }

            Log::info('Procesando día: ' . $fechaActual->format('Y-m-d'));

            foreach ($horariosTrabajo as $horario) {
                $horaInicio = $fechaActual->copy()->setHour($horario['inicio'])->setMinute($horario['minuto_inicio'])->setSecond(0);
                $horaFin = $fechaActual->copy()->setHour($horario['fin'])->setMinute($horario['minuto_fin'])->setSecond(0);

                Log::info('Procesando horario de trabajo:', [
                    'hora_inicio' => $horaInicio->format('Y-m-d H:i:s'),
                    'hora_fin' => $horaFin->format('Y-m-d H:i:s'),
                    'tipo' => $horario['inicio'] < 12 ? 'mañana' : 'tarde'
                ]);

                // Generar slots de tiempo dentro del horario de trabajo
                $slotActual = $horaInicio->copy();
                $slotsEnHorario = 0;
                
                while ($slotActual->copy()->addMinutes($duracionMinutos)->lte($horaFin)) {
                    $slotFin = $slotActual->copy()->addMinutes($duracionMinutos);
                    $slotsEnHorario++;
                    
                    // Verificar si este slot está disponible (no hay citas existentes)
                    $disponible = $this->esSlotDisponible($slotActual, $slotFin, $citasExistentes);
                    
                    if ($disponible) {
                        $horariosDisponibles[] = [
                            'fecha_inicio' => $slotActual->format('Y-m-d H:i:s'),
                            'fecha_fin' => $slotFin->format('Y-m-d H:i:s'),
                            'fecha_formateada' => $slotActual->format('d/m/Y'),
                            'hora_inicio' => $slotActual->format('H:i'),
                            'hora_fin' => $slotFin->format('H:i'),
                            'duracion_minutos' => $duracionMinutos,
                            'disponible' => true,
                            'tipo_horario' => $horario['inicio'] < 12 ? 'mañana' : 'tarde'
                        ];
                        
                        $slotsGenerados++;
                        
                        Log::info('Slot disponible encontrado:', [
                            'slot_inicio' => $slotActual->format('Y-m-d H:i:s'),
                            'slot_fin' => $slotFin->format('Y-m-d H:i:s'),
                            'total_slots' => $slotsGenerados
                        ]);
                    } else {
                        Log::info('Slot ocupado:', [
                            'slot_inicio' => $slotActual->format('Y-m-d H:i:s'),
                            'slot_fin' => $slotFin->format('Y-m-d H:i:s')
                        ]);
                    }
                    
                    // Avanzar 30 minutos para el siguiente slot
                    $slotActual->addMinutes(30);
                }
                
                Log::info('Horario procesado:', [
                    'tipo' => $horario['inicio'] < 12 ? 'mañana' : 'tarde',
                    'slots_en_horario' => $slotsEnHorario,
                    'slots_disponibles' => $slotsEnHorario
                ]);
            }

            $fechaActual->addDay();
        }

        Log::info('Generación completada:', [
            'dias_procesados' => $diasProcesados,
            'total_horarios' => count($horariosDisponibles),
            'slots_generados' => $slotsGenerados
        ]);

        return $horariosDisponibles;
    }

    /**
     * Verificar si un slot de tiempo está disponible (no hay citas existentes)
     */
    private function esSlotDisponible($slotInicio, $slotFin, $citasExistentes)
    {
        foreach ($citasExistentes as $cita) {
            $citaInicio = Carbon::parse($cita->fecha_inicio);
            $citaFin = Carbon::parse($cita->fecha_fin);

            // Verificar si hay solapamiento
            if ($this->haySolapamiento($slotInicio, $slotFin, $citaInicio, $citaFin)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verificar si dos rangos de tiempo se solapan
     */
    private function haySolapamiento($inicio1, $fin1, $inicio2, $fin2)
    {
        return $inicio1->lt($fin2) && $fin1->gt($inicio2);
    }

    /**
     * Obtener citas existentes para el calendario
     */
    public function getCitas(Request $request)
    {
        try {
            Log::info('=== INICIO getCitas ===');
            Log::info('Datos recibidos:', $request->all());

            $fechaInicio = $request->input('start', now()->startOfMonth());
            $fechaFin = $request->input('end', now()->endOfMonth());
            $gestorId = $request->input('gestor_id');

            Log::info('Parámetros procesados:', [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'gestor_id' => $gestorId
            ]);

            $query = \App\Models\Cita::with(['cliente', 'gestor'])
                        ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin]);

            if ($gestorId) {
                $query->where('gestor_id', $gestorId);
            }

            $citas = $query->get();

            Log::info('Citas encontradas:', [
                'total' => $citas->count(),
                'citas' => $citas->map(function($cita) {
                    return [
                        'id' => $cita->id,
                        'titulo' => $cita->titulo,
                        'fecha_inicio' => $cita->fecha_inicio,
                        'fecha_fin' => $cita->fecha_fin,
                        'estado' => $cita->estado
                    ];
                })->toArray()
            ]);

            $eventos = $citas->map(function ($cita) {
                return [
                    'id' => $cita->id,
                    'title' => $cita->titulo,
                    'start' => $cita->fecha_inicio->toISOString(),
                    'end' => $cita->fecha_fin->toISOString(),
                    'color' => $cita->color ?? '#3b82f6',
                    'backgroundColor' => $cita->color ?? '#3b82f6',
                    'borderColor' => $cita->color ?? '#3b82f6',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'descripcion' => $cita->descripcion,
                        'estado' => $cita->estado,
                        'tipo' => $cita->tipo,
                        'ubicacion' => $cita->ubicacion,
                        'cliente' => $cita->cliente ? $cita->cliente->name : 'Sin cliente',
                        'gestor' => $cita->gestor ? $cita->gestor->name : 'Sin gestor'
                    ]
                ];
            });

            Log::info('Eventos formateados:', [
                'total' => $eventos->count(),
                'primeros_3' => $eventos->take(3)->toArray()
            ]);

            return response()->json($eventos);

        } catch (\Exception $e) {
            Log::error('Error en getCitas:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información del día de hoy
     */
    public function getDiaHoy(Request $request)
    {
        try {
            Log::info('=== INICIO getDiaHoy ===');
            
            $hoy = now();
            $fechaFormateada = $hoy->format('d/m/Y');
            $diaSemana = $hoy->locale('es')->dayName;
            $mes = $hoy->locale('es')->monthName;
            $año = $hoy->year;
            $hora = $hoy->format('H:i');
            $fechaCompleta = $hoy->format('d/m/Y H:i');
            
            $informacion = [
                'fecha' => $fechaFormateada,
                'dia_semana' => $diaSemana,
                'mes' => $mes,
                'año' => $año,
                'hora' => $hora,
                'fecha_completa' => $fechaCompleta,
                'timestamp' => $hoy->timestamp,
                'formato_iso' => $hoy->toISOString(),
                'zona_horaria' => $hoy->timezone->getName(),
                'descripcion' => "Hoy es {$diaSemana}, {$fechaFormateada} y son las {$hora}"
            ];

            Log::info('Información del día generada:', $informacion);

            return response()->json([
                'success' => true,
                'message' => 'Información del día de hoy obtenida exitosamente',
                'data' => $informacion
            ]);

        } catch (\Exception $e) {
            Log::error('Error en getDiaHoy:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo presupuesto
     */
    public function crearPresupuesto(Request $request)
    {
        try {
            Log::info('=== INICIO crearPresupuesto ===', $request->all());

            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'client_id' => 'required|exists:clients,id',
                'project_id' => 'required|exists:projects,id', // Campaña es obligatoria
                'admin_user_id' => 'required|exists:admin_user,id',
                'concept' => 'required|string|max:200',
                'description' => 'nullable|string',
                'note' => 'nullable|string',
                'commercial_id' => 'nullable|exists:admin_user,id',
                'payment_method_id' => 'nullable|exists:payment_method,id',
                'conceptos' => 'required|array|min:1',
                'conceptos.*.title' => 'required|string|max:255',
                'conceptos.*.concept' => 'required|string',
                'conceptos.*.units' => 'required|numeric|min:1',
                'conceptos.*.sale_price' => 'required|numeric|min:0',
                'conceptos.*.concept_type_id' => 'nullable|integer',
                'conceptos.*.service_id' => 'nullable|integer',
                'conceptos.*.services_category_id' => 'nullable|integer',
                'iva_percentage' => 'nullable|numeric|min:0|max:100',
                'discount' => 'nullable|numeric|min:0',
                'expiration_date' => 'nullable|date|after:today'
            ]);

            if ($validator->fails()) {
                Log::warning('Validación fallida en crearPresupuesto:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            DB::beginTransaction();

            // Generar referencia temporal primero (como hace el sistema original)
            $budgetTemporal = Budget::where('temp', true)->orderBy('created_at', 'desc')->first();
            $referenceTemp = $budgetTemporal === null ? 'temp_00' : $this->generateReferenceTemp($budgetTemporal->reference);
            
            // Calcular totales
            $conceptos = $request->conceptos;
            $subtotal = 0;
            
            foreach ($conceptos as $concepto) {
                $total_concepto = $concepto['units'] * $concepto['sale_price'];
                $subtotal += $total_concepto;
            }

            $discount = $request->discount ?? 0;
            $base = $subtotal - $discount;
            $iva_percentage = $request->iva_percentage ?? 21;
            $iva = $base * ($iva_percentage / 100);
            $total = $base + $iva;

            // Crear el presupuesto temporal (como hace el sistema original)
            $budget = Budget::create([
                'reference' => $referenceTemp,
                'admin_user_id' => $request->admin_user_id,
                'client_id' => $request->client_id,
                'project_id' => $request->project_id, // Campaña obligatoria
                'payment_method_id' => $request->payment_method_id,
                'commercial_id' => $request->commercial_id,
                'concept' => $request->concept,
                'description' => $request->description,
                'note' => $request->note,
                'creation_date' => now()->format('Y-m-d'),
                'gross' => $subtotal,
                'base' => $base,
                'iva' => $iva,
                'iva_percentage' => $iva_percentage,
                'total' => $total,
                'discount' => $discount,
                'budget_status_id' => 1, // Estado inicial: Borrador
                'temp' => true, // Temporal inicialmente
                'expiration_date' => $request->expiration_date
            ]);

            // Crear los conceptos del presupuesto
            foreach ($conceptos as $concepto) {
                $total_concepto = $concepto['units'] * $concepto['sale_price'];
                
                BudgetConcept::create([
                    'budget_id' => $budget->id,
                    'concept_type_id' => $concepto['concept_type_id'] ?? 2, // Por defecto: Propio
                    'service_id' => $concepto['service_id'] ?? null,
                    'services_category_id' => $concepto['services_category_id'] ?? null,
                    'title' => $concepto['title'],
                    'concept' => $concepto['concept'],
                    'units' => $concepto['units'],
                    'sale_price' => $concepto['sale_price'],
                    'total' => $total_concepto,
                    'total_no_discount' => $total_concepto
                ]);
            }

            // Generar referencia definitiva y actualizar el presupuesto
            $referenceData = $this->generateBudgetReferenceCorrect();
            $budget->update([
                'reference' => $referenceData['reference'],
                'reference_autoincrement_id' => $referenceData['id'],
                'temp' => false // Ya no es temporal
            ]);

            // Crear alerta de presupuesto creado
            $this->crearAlertaPresupuesto($budget);

            DB::commit();

            Log::info('Presupuesto creado exitosamente:', [
                'budget_id' => $budget->id,
                'reference' => $budget->reference,
                'client_id' => $budget->client_id,
                'project_id' => $budget->project_id,
                'total' => $budget->total
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presupuesto creado exitosamente',
                'data' => [
                    'budget_id' => $budget->id,
                    'reference' => $budget->reference,
                    'total' => $budget->total,
                    'client_name' => $budget->cliente->name ?? 'Cliente no encontrado',
                    'project_name' => $budget->proyecto->name ?? 'Proyecto no encontrado'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en crearPresupuesto:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar presupuesto por email en PDF
     */
    public function enviarPresupuestoPDF(Request $request)
    {
        try {
            Log::info('=== INICIO enviarPresupuestoPDF ===', $request->all());

            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'budget_id' => 'required|exists:budgets,id',
                'email' => 'required|email',
                'cc' => 'nullable|email',
                'cc2' => 'nullable|email',
                'message' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                Log::warning('Validación fallida en enviarPresupuestoPDF:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $budget = Budget::with(['cliente', 'usuario'])->find($request->budget_id);
            
            if (!$budget) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presupuesto no encontrado'
                ], 404);
            }

            // Generar PDF del presupuesto
            $filename = $this->saveBudgetPDF($budget);

            // Registrar el envío
            $data = [
                'admin_user_id' => $budget->admin_user_id,
                'budget_id' => $budget->id,
                'budget_reference' => $budget->reference,
                'client_id' => $budget->client_id,
                'file_name' => $filename,
                'date_send' => Carbon::now()->format('Y-m-d')
            ];

            $budgetExist = BudgetSend::where('budget_id', $budget->id)->first();
            
            if ($budgetExist) {
                DB::table("budgets_sends")->where("budget_id", $budget->id)->update($data);
            } else {
                BudgetSend::create($data);
            }

            // Preparar datos del email
            $mailBudget = new \stdClass();
            $mailBudget->url = 'https://crm.hawkins.es/budget/cliente/' . $filename;
            $mailBudget->gestor = $budget->usuario->name . " " . $budget->usuario->surname;
            $mailBudget->gestorMail = $budget->usuario->email;
            $mailBudget->gestorTel = '956 662 942';
            $mailBudget->files = false;

            // Configurar destinatarios
            $mail = $request->email;
            $mailsCC = [];
            $mailsBCC = [];

            $mailsBCC[] = "emma@lchawkins.com";
            $mailsBCC[] = "ivan@lchawkins.com";
            $mailsBCC[] = $mailBudget->gestorMail;

            if ($request->cc) {
                $mailsCC[] = $request->cc;
            }

            if ($request->cc2) {
                $mailsCC[] = $request->cc2;
            }

            // Enviar email
            $email = new MailBudget($mailBudget, []);
            
            Mail::to($mail)
                ->bcc($mailsBCC)
                ->cc($mailsCC)
                ->send($email);

            // Crear alerta de seguimiento
            $fechaNow = Carbon::now();
            Alert::create([
                'admin_user_id' => $budget->admin_user_id,
                'stage_id' => 21, // Etapa Pendiente de confirmar
                'activation_datetime' => $fechaNow->addDays(2)->format('Y-m-d H:i:s'),
                'status_id' => 1, // Estado pendiente
                'reference_id' => $budget->id
            ]);

            // Registrar log de email
            $logData = [
                'mailEmisor' => "budget@crmhawkins.com",
                'mailReceptor' => $mail,
                'status' => 4, // Tipo de log para budgets
                'mensaje' => $request->message ?? "Nuestro equipo ha trabajado para ofrecerte la mejor propuesta. Espero que sea de tu agrado, recuerda que estoy disponible para ayudarte en lo que necesites"
            ];

            $this->registrarLogEmail($logData);

            Log::info('Presupuesto enviado exitosamente:', [
                'budget_id' => $budget->id,
                'reference' => $budget->reference,
                'email' => $mail,
                'filename' => $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presupuesto enviado exitosamente por email',
                'data' => [
                    'budget_id' => $budget->id,
                    'reference' => $budget->reference,
                    'email' => $mail,
                    'pdf_url' => $mailBudget->url
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en enviarPresupuestoPDF:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar referencia temporal para presupuesto
     */
    private function generateReferenceTemp($reference)
    {
        // Extrae el número de la referencia temporal
        preg_match('/temp_(\d{2})/', $reference, $matches);
        // Incrementa el número primero
        if(count($matches) >= 1){
            $incrementedNumber = intval($matches[1]) + 1;
            // Asegura que el número tenga dos dígitos
            $formattedNumber = str_pad($incrementedNumber, 2, '0', STR_PAD_LEFT);
            // Concatena con la cadena "temp_"
            return "temp_" . $formattedNumber;
        }
        return "temp_01";
    }

    /**
     * Generar referencia definitiva para presupuesto (sistema correcto)
     */
    private function generateBudgetReferenceCorrect()
    {
        // Obtener la fecha actual del presupuesto
        $budgetCreationDate = now();
        $datetimeBudgetCreationDate = new \DateTime($budgetCreationDate);

        // Formatear la fecha para obtener los componentes necesarios
        $year = $datetimeBudgetCreationDate->format('Y');
        $monthNum = $datetimeBudgetCreationDate->format('m');

        // Buscar la última referencia autoincremental para el año y mes actual
        $latestReference = BudgetReferenceAutoincrement::where('year', $year)
                            ->where('month_num', $monthNum)
                            ->orderBy('reference_autoincrement', 'desc')
                            ->first();

        // Si no existe, empezamos desde 1, de lo contrario, incrementamos
        $newReferenceAutoincrement = $latestReference ? $latestReference->reference_autoincrement + 1 : 1;

        // Formatear el número autoincremental a 6 dígitos
        $formattedAutoIncrement = str_pad($newReferenceAutoincrement, 6, '0', STR_PAD_LEFT);

        // Crear la referencia con formato YYYY/MM/NNNNNN
        $reference = $year . '/' . $monthNum . '/' . $formattedAutoIncrement;

        // Guardar la referencia autoincremental en BudgetReferenceAutoincrement
        $referenceToSave = new BudgetReferenceAutoincrement([
            'reference_autoincrement' => $newReferenceAutoincrement,
            'year' => $year,
            'month_num' => $monthNum,
            'month' => $monthNum,
            'month_full' => $datetimeBudgetCreationDate->format('F'),
            'day' => $datetimeBudgetCreationDate->format('d'),
            'letter_months' => $datetimeBudgetCreationDate->format('M')
        ]);
        $referenceToSave->save();

        // Devolver el resultado
        return [
            'id' => $referenceToSave->id,
            'reference' => $reference,
            'reference_autoincrement' => $newReferenceAutoincrement,
            'budget_reference_autoincrements' => [
                'year' => $year,
                'month_num' => $monthNum,
            ]
        ];
    }

    /**
     * Guardar PDF del presupuesto
     */
    private function saveBudgetPDF($budget)
    {
        // Nombre del archivo basado en la referencia del presupuesto
        $name = 'presupuesto_' . $budget->reference;
        // Cifrar el nombre del archivo
        $encrypted = $this->encrypt_decrypt('encrypt', $name);
        // Ruta completa para guardar el archivo PDF
        $pathToSaveBudget = storage_path('app/public/assets/budgets/' . $encrypted . '.pdf');
        // Verificar si el directorio existe, si no, crearlo
        $directory = storage_path('app/public/assets/budgets/');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        // Generar el PDF
        $sumatorio = false;
        $pdf = $this->createBudgetPdf($budget, $sumatorio);
        // Guardar el PDF en la ruta especificada
        $pdf->save($pathToSaveBudget);
        return $encrypted;
    }

    /**
     * Crear PDF del presupuesto
     */
    private function createBudgetPdf($budget, $sumatorio)
    {
        // Los conceptos de este presupuesto
        $thisBudgetConcepts = BudgetConcept::where('budget_id', $budget->id)->get();
        
        // Condiciones de categoría de los servicios
        $conceptCategoriesID = array();
        foreach($thisBudgetConcepts as $concept){
            if(!in_array($concept->services_category_id, $conceptCategoriesID)){
                array_push($conceptCategoriesID, $concept->services_category_id);
            }
        }
        
        foreach($conceptCategoriesID as $key => $value){
            $category = ServiceCategories::where('id', $value)->first();
            if($category){
                // Definir los conceptos del PDF y precios
                $conceptosPDF = '';
                $precioSinIvaPDF = 0;
                if(count($thisBudgetConcepts) >= 2){
                    foreach($thisBudgetConcepts as $concepto){
                        if ($conceptosPDF == '') {
                            $conceptosPDF = $concepto->title;
                        }else{
                            $conceptosPDF = $conceptosPDF . ', ' . $concepto->title;
                        }
                        $precioSinIvaPDF += $concepto->total;
                    }
                } else {
                    $conceptosPDF = $thisBudgetConcepts[0]->title;
                }
            }
        }
        
        // Título
        $title = "Presupuesto - ".$budget['reference'];
        
        // PDF personalización
        $data = [
            'title' => $title,
            'budget_reference' => $budget['reference'],
        ];
        
        // Array de conceptos para utilizar en la vista, formatea cadenas para que cuadre
        $budgetConceptsFormated = array();
        foreach($thisBudgetConcepts as $budgetConcept){
            // Título
            $budgetConceptsFormated[$budgetConcept->id]['title'] = $budgetConcept['title'];
            // Unidades
            $budgetConceptsFormated[$budgetConcept->id]['units'] = $budgetConcept['units'];
            // Precio
            $budgetConceptsFormated[$budgetConcept->id]['sale_price'] = number_format((float)$budgetConcept['sale_price'], 2, ',', '');
            // Total
            $budgetConceptsFormated[$budgetConcept->id]['total'] = number_format((float)$budgetConcept['total'], 2, ',', '');
            // Descripción
            $rawConcepts = $budgetConcept['concept'];
            // Descripción dividida en cadenas y saltos de linea
            $arrayConceptStringsAndBreakLines = explode(PHP_EOL, $rawConcepts);
            $budgetConceptsFormated[$budgetConcept->id]['description'] = $arrayConceptStringsAndBreakLines;
        }

        $pdf = PDF::loadView('budgets.previewPDF', compact('budget','data', 'budgetConceptsFormated','sumatorio'));
        return $pdf;
    }

    /**
     * Crear alerta de presupuesto creado
     */
    private function crearAlertaPresupuesto($budget)
    {
        try {
            Alert::create([
                'reference_id' => $budget->id,
                'admin_user_id' => $budget->admin_user_id,
                'stage_id' => 15, // Alerta ElevenLabs
                'status_id' => 1,
                'activation_datetime' => Carbon::now(),
                'description' => '[ELEVENLABS] Presupuesto ' . $budget->reference . ' creado para cliente: ' . ($budget->cliente->name ?? 'Cliente no encontrado')
            ]);

            Log::info('Alerta de presupuesto creada:', [
                'budget_id' => $budget->id,
                'reference' => $budget->reference,
                'client_name' => $budget->cliente->name ?? 'Cliente no encontrado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando alerta de presupuesto:', [
                'budget_id' => $budget->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Registrar log de email
     */
    private function registrarLogEmail($logData)
    {
        try {
            LogsEmail::create([
                'mail_emisor' => $logData['mailEmisor'],
                'mail_receptor' => $logData['mailReceptor'],
                'status' => $logData['status'],
                'mensaje' => $logData['mensaje'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Log de email registrado:', $logData);

        } catch (\Exception $e) {
            Log::error('Error registrando log de email:', [
                'error' => $e->getMessage(),
                'logData' => $logData
            ]);
        }
    }

    /**
     * Función de encriptación/desencriptación
     */
    private function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'c0c0dr1l0s3n3ln1l0';
        $secret_iv = 'c0c0dr1l0s3n3ln1l0';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    /**
     * Obtener lista de proyectos/campañas disponibles
     */
    public function getProyectos(Request $request)
    {
        try {
            Log::info('=== INICIO getProyectos ===');

            $proyectos = Project::select('id', 'name', 'description', 'client_id')
                ->with(['cliente:id,name,company'])
                ->orderBy('name')
                ->get();

            Log::info('Proyectos obtenidos:', ['count' => $proyectos->count()]);

            return response()->json([
                'success' => true,
                'message' => 'Proyectos obtenidos exitosamente',
                'data' => $proyectos
            ]);

        } catch (\Exception $e) {
            Log::error('Error en getProyectos:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
