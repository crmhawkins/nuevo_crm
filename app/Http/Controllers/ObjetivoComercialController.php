<?php

namespace App\Http\Controllers;

use App\Models\ObjetivoComercial;
use App\Models\Users\User;
use App\Models\VisitaComercial;
use App\Models\Budgets\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ObjetivoComercialController extends Controller
{
    /**
     * Mostrar el panel de objetivos comerciales
     */
    public function index()
    {
        $objetivos = ObjetivoComercial::with(['comercial', 'admin'])
            ->activos()
            ->orderBy('created_at', 'desc')
            ->get();

        $comerciales = User::where('access_level_id', 6)
            ->where('inactive', 0)
            ->get();

        return view('admin.objetivos_comerciales.index', compact('objetivos', 'comerciales'));
    }

    /**
     * Crear un nuevo objetivo comercial
     */
    public function store(Request $request)
    {
        $request->validate([
            'comercial_id' => 'required|exists:admin_user,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'tipo_objetivo' => 'required|in:diario,mensual',
            
            // Objetivos de visitas
            'visitas_presenciales_diarias' => 'nullable|integer|min:0',
            'visitas_telefonicas_diarias' => 'nullable|integer|min:0',
            'visitas_mixtas_diarias' => 'nullable|integer|min:0',
            
            // Objetivos de ventas
            'planes_esenciales_mensuales' => 'nullable|integer|min:0',
            'planes_profesionales_mensuales' => 'nullable|integer|min:0',
            'planes_avanzados_mensuales' => 'nullable|integer|min:0',
            'ventas_euros_mensuales' => 'nullable|numeric|min:0',
            
            // Precios
            'precio_plan_esencial' => 'nullable|numeric|min:0',
            'precio_plan_profesional' => 'nullable|numeric|min:0',
            'precio_plan_avanzado' => 'nullable|numeric|min:0',
            
            'notas' => 'nullable|string'
        ]);

        // Desactivar objetivos anteriores del mismo comercial
        ObjetivoComercial::where('comercial_id', $request->comercial_id)
            ->where('activo', true)
            ->update(['activo' => false]);

        $objetivo = ObjetivoComercial::create([
            'comercial_id' => $request->comercial_id,
            'admin_user_id' => auth()->id(),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'tipo_objetivo' => $request->tipo_objetivo,
            'visitas_presenciales_diarias' => $request->visitas_presenciales_diarias ?? 0,
            'visitas_telefonicas_diarias' => $request->visitas_telefonicas_diarias ?? 0,
            'visitas_mixtas_diarias' => $request->visitas_mixtas_diarias ?? 0,
            'planes_esenciales_mensuales' => $request->planes_esenciales_mensuales ?? 0,
            'planes_profesionales_mensuales' => $request->planes_profesionales_mensuales ?? 0,
            'planes_avanzados_mensuales' => $request->planes_avanzados_mensuales ?? 0,
            'ventas_euros_mensuales' => $request->ventas_euros_mensuales ?? 0,
            'precio_plan_esencial' => $request->precio_plan_esencial ?? 19.00,
            'precio_plan_profesional' => $request->precio_plan_profesional ?? 49.00,
            'precio_plan_avanzado' => $request->precio_plan_avanzado ?? 129.00,
            'notas' => $request->notas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Objetivo creado exitosamente',
            'objetivo' => $objetivo->load(['comercial', 'admin'])
        ]);
    }

    /**
     * Obtener el progreso de un comercial
     */
    public function getProgreso($comercialId, $fechaInicio = null, $fechaFin = null)
    {
        $fechaInicio = $fechaInicio ? Carbon::parse($fechaInicio) : now()->startOfMonth();
        $fechaFin = $fechaFin ? Carbon::parse($fechaFin) : now()->endOfMonth();

        // Obtener objetivo vigente
        $objetivo = ObjetivoComercial::delComercial($comercialId)
            ->activos()
            ->vigentes()
            ->first();

        if (!$objetivo) {
            return response()->json([
                'success' => false,
                'message' => 'No hay objetivos establecidos para este comercial'
            ]);
        }

        // Calcular visitas realizadas
        $visitasRealizadas = VisitaComercial::where('comercial_id', $comercialId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->get();

        $visitasPresenciales = $visitasRealizadas->where('tipo_visita', 'presencial')->count();
        $visitasTelefonicas = $visitasRealizadas->where('tipo_visita', 'telefonico')->count();
        $visitasMixtas = $visitasRealizadas->where('tipo_visita', 'mixto')->count();

        // Calcular ventas realizadas (simplificado - basado en presupuestos)
        $ventasRealizadas = Budget::where('comercial_id', $comercialId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('budget_status_id', 2) // Aceptado
            ->get();

        $planesEsenciales = $ventasRealizadas->where('concept', 'like', '%esencial%')->count();
        $planesProfesionales = $ventasRealizadas->where('concept', 'like', '%profesional%')->count();
        $planesAvanzados = $ventasRealizadas->where('concept', 'like', '%avanzado%')->count();

        $ventasEuros = $ventasRealizadas->sum('total');

        // Calcular porcentajes
        $progresoVisitasPresenciales = $objetivo->visitas_presenciales_diarias > 0 
            ? ($visitasPresenciales / $objetivo->visitas_presenciales_diarias) * 100 
            : 0;

        $progresoVisitasTelefonicas = $objetivo->visitas_telefonicas_diarias > 0 
            ? ($visitasTelefonicas / $objetivo->visitas_telefonicas_diarias) * 100 
            : 0;

        $progresoVisitasMixtas = $objetivo->visitas_mixtas_diarias > 0 
            ? ($visitasMixtas / $objetivo->visitas_mixtas_diarias) * 100 
            : 0;

        $progresoPlanesEsenciales = $objetivo->planes_esenciales_mensuales > 0 
            ? ($planesEsenciales / $objetivo->planes_esenciales_mensuales) * 100 
            : 0;

        $progresoPlanesProfesionales = $objetivo->planes_profesionales_mensuales > 0 
            ? ($planesProfesionales / $objetivo->planes_profesionales_mensuales) * 100 
            : 0;

        $progresoPlanesAvanzados = $objetivo->planes_avanzados_mensuales > 0 
            ? ($planesAvanzados / $objetivo->planes_avanzados_mensuales) * 100 
            : 0;

        $progresoVentasEuros = $objetivo->ventas_euros_mensuales > 0 
            ? ($ventasEuros / $objetivo->ventas_euros_mensuales) * 100 
            : 0;

        return response()->json([
            'success' => true,
            'objetivo' => $objetivo,
            'progreso' => [
                'visitas' => [
                    'presenciales' => [
                        'objetivo' => $objetivo->visitas_presenciales_diarias,
                        'realizado' => $visitasPresenciales,
                        'progreso' => round($progresoVisitasPresenciales, 2)
                    ],
                    'telefonicas' => [
                        'objetivo' => $objetivo->visitas_telefonicas_diarias,
                        'realizado' => $visitasTelefonicas,
                        'progreso' => round($progresoVisitasTelefonicas, 2)
                    ],
                    'mixtas' => [
                        'objetivo' => $objetivo->visitas_mixtas_diarias,
                        'realizado' => $visitasMixtas,
                        'progreso' => round($progresoVisitasMixtas, 2)
                    ]
                ],
                'ventas' => [
                    'planes_esenciales' => [
                        'objetivo' => $objetivo->planes_esenciales_mensuales,
                        'realizado' => $planesEsenciales,
                        'progreso' => round($progresoPlanesEsenciales, 2)
                    ],
                    'planes_profesionales' => [
                        'objetivo' => $objetivo->planes_profesionales_mensuales,
                        'realizado' => $planesProfesionales,
                        'progreso' => round($progresoPlanesProfesionales, 2)
                    ],
                    'planes_avanzados' => [
                        'objetivo' => $objetivo->planes_avanzados_mensuales,
                        'realizado' => $planesAvanzados,
                        'progreso' => round($progresoPlanesAvanzados, 2)
                    ],
                    'ventas_euros' => [
                        'objetivo' => $objetivo->ventas_euros_mensuales,
                        'realizado' => $ventasEuros,
                        'progreso' => round($progresoVentasEuros, 2)
                    ]
                ]
            ]
        ]);
    }

    /**
     * Actualizar un objetivo
     */
    public function update(Request $request, $id)
    {
        $objetivo = ObjetivoComercial::findOrFail($id);

        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'visitas_presenciales_diarias' => 'nullable|integer|min:0',
            'visitas_telefonicas_diarias' => 'nullable|integer|min:0',
            'visitas_mixtas_diarias' => 'nullable|integer|min:0',
            'planes_esenciales_mensuales' => 'nullable|integer|min:0',
            'planes_profesionales_mensuales' => 'nullable|integer|min:0',
            'planes_avanzados_mensuales' => 'nullable|integer|min:0',
            'ventas_euros_mensuales' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string'
        ]);

        $objetivo->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Objetivo actualizado exitosamente',
            'objetivo' => $objetivo->load(['comercial', 'admin'])
        ]);
    }

    /**
     * Desactivar un objetivo
     */
    public function destroy($id)
    {
        $objetivo = ObjetivoComercial::findOrFail($id);
        $objetivo->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Objetivo desactivado exitosamente'
        ]);
    }
}
