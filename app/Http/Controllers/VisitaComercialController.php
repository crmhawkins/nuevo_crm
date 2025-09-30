<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitaComercial;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\Log;

class VisitaComercialController extends Controller
{
    /**
     * Mostrar el dashboard comercial con visitas
     */
    public function index()
    {
        $user = auth()->user();
        $visitas = VisitaComercial::with(['cliente', 'comercial'])
            ->where('comercial_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
            
        $clientes = Client::where('is_client', 1)->get();
        
        // Datos para el dashboard (mantener compatibilidad)
        $diasDiferencia = now()->endOfMonth()->diffInDays(now());
        $pedienteCierre = 0;
        $comisionCurso = 0;
        $comisionPendiente = 0;
        $comisionTramitadas = 0;
        $comisionRestante = 0;
        $jornadaActiva = false;
        $pausaActiva = false;
        $timeWorkedToday = 0;
        
        return view('dashboards.dashboard_comercial_nuevo', compact(
            'user', 'visitas', 'clientes', 'diasDiferencia', 'pedienteCierre',
            'comisionCurso', 'comisionPendiente', 'comisionTramitadas', 'comisionRestante',
            'jornadaActiva', 'pausaActiva', 'timeWorkedToday'
        ));
    }

    /**
     * Guardar un nuevo lead
     */
    public function storeLead(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255'
            ]);

            $cliente = Client::create([
                'name' => $request->nombre,
                'phone' => $request->telefono,
                'email' => $request->email,
                'is_client' => 0, // Es un lead, no cliente final
                'admin_user_id' => auth()->id()
            ]);

            Log::info('Lead creado:', [
                'cliente_id' => $cliente->id,
                'nombre' => $cliente->name,
                'comercial_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead creado exitosamente',
                'cliente' => $cliente
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando lead:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar una nueva visita
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'comercial_id' => 'required|exists:admin_user,id',
                'cliente_id' => 'nullable|exists:clients,id',
                'nombre_cliente' => 'nullable|string|max:255',
                'telefono_cliente' => 'nullable|string|max:20',
                'email_cliente' => 'nullable|email|max:255',
                'tipo_visita' => 'required|in:presencial,telefonico',
                'valoracion' => 'required|integer|min:1|max:10',
                'comentarios' => 'nullable|string',
                'requiere_seguimiento' => 'boolean',
                'fecha_seguimiento' => 'nullable|date'
            ]);

            // Validar que tenga cliente_id o nombre_cliente
            if (!$request->cliente_id && !$request->nombre_cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar un cliente existente o proporcionar el nombre de un cliente nuevo'
                ], 400);
            }

            $clienteId = $request->cliente_id;
            
            // Si es un cliente nuevo, crear el lead
            if (!$clienteId && $request->nombre_cliente) {
                $cliente = Client::create([
                    'name' => $request->nombre_cliente,
                    'phone' => $request->telefono_cliente,
                    'email' => $request->email_cliente,
                    'is_client' => 0, // Es un lead
                    'admin_user_id' => $request->comercial_id
                ]);
                $clienteId = $cliente->id;
                
                Log::info('Lead creado desde visita:', [
                    'cliente_id' => $cliente->id,
                    'nombre' => $cliente->name,
                    'comercial_id' => $request->comercial_id
                ]);
            }

            $visita = VisitaComercial::create([
                'comercial_id' => $request->comercial_id,
                'cliente_id' => $clienteId,
                'nombre_cliente' => $request->nombre_cliente,
                'tipo_visita' => $request->tipo_visita,
                'valoracion' => $request->valoracion,
                'comentarios' => $request->comentarios,
                'requiere_seguimiento' => $request->requiere_seguimiento ?? false,
                'fecha_seguimiento' => $request->fecha_seguimiento
            ]);

            Log::info('Visita comercial creada:', [
                'visita_id' => $visita->id,
                'comercial_id' => $visita->comercial_id,
                'cliente_id' => $visita->cliente_id,
                'tipo_visita' => $visita->tipo_visita,
                'valoracion' => $visita->valoracion
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visita registrada exitosamente',
                'visita' => $visita->load(['cliente', 'comercial'])
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando visita comercial:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la visita: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener visitas del comercial
     */
    public function getVisitas(Request $request)
    {
        try {
            $visitas = VisitaComercial::with(['cliente', 'comercial'])
                ->where('comercial_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'visitas' => $visitas
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo visitas:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las visitas'
            ], 500);
        }
    }
}
