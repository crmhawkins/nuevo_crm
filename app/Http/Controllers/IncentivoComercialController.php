<?php

namespace App\Http\Controllers;

use App\Models\IncentivoComercial;
use App\Models\Users\User;
use App\Models\VisitaComercial;
use App\Models\Budgets\Budget;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IncentivoComercialController extends Controller
{
    /**
     * Mostrar el panel de incentivos comerciales
     */
    public function index()
    {
        $incentivos = IncentivoComercial::with(['comercial', 'admin'])
            ->activos()
            ->orderBy('created_at', 'desc')
            ->get();

        $comerciales = User::where('access_level_id', 6)
            ->where('inactive', 0)
            ->get();

        return view('admin.incentivos_comerciales.index', compact('incentivos', 'comerciales'));
    }

    /**
     * Crear un nuevo incentivo comercial
     */
    public function store(Request $request)
    {
        $request->validate([
            'comercial_id' => 'required|exists:admin_user,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'porcentaje_venta' => 'required|numeric|min:0|max:100',
            'porcentaje_adicional' => 'required|numeric|min:0|max:100',
            'min_clientes_mensuales' => 'required|integer|min:1',
            'min_ventas_mensuales' => 'nullable|numeric|min:0',
            'precio_plan_esencial' => 'required|numeric|min:0',
            'precio_plan_profesional' => 'required|numeric|min:0',
            'precio_plan_avanzado' => 'required|numeric|min:0',
            'notas' => 'nullable|string'
        ]);

        // Desactivar incentivos anteriores del mismo comercial
        IncentivoComercial::where('comercial_id', $request->comercial_id)
            ->where('activo', true)
            ->update(['activo' => false]);

        $incentivo = IncentivoComercial::create([
            'comercial_id' => $request->comercial_id,
            'admin_user_id' => auth()->id(),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'porcentaje_venta' => $request->porcentaje_venta,
            'porcentaje_adicional' => $request->porcentaje_adicional,
            'min_clientes_mensuales' => $request->min_clientes_mensuales,
            'min_ventas_mensuales' => $request->min_ventas_mensuales ?? 0,
            'precio_plan_esencial' => $request->precio_plan_esencial,
            'precio_plan_profesional' => $request->precio_plan_profesional,
            'precio_plan_avanzado' => $request->precio_plan_avanzado,
            'notas' => $request->notas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incentivo creado exitosamente',
            'incentivo' => $incentivo->load(['comercial', 'admin'])
        ]);
    }

    /**
     * Obtener el progreso de incentivos de un comercial
     */
    public function getProgresoIncentivos($comercialId, $fechaInicio = null, $fechaFin = null)
    {
        $fechaInicio = $fechaInicio ? Carbon::parse($fechaInicio) : now()->startOfMonth();
        $fechaFin = $fechaFin ? Carbon::parse($fechaFin) : now()->endOfMonth();

        // Obtener incentivo vigente
        $incentivo = IncentivoComercial::delComercial($comercialId)
            ->activos()
            ->vigentes()
            ->first();

        if (!$incentivo) {
            return response()->json([
                'success' => false,
                'message' => 'No hay incentivos establecidos para este comercial'
            ]);
        }

        // Calcular ventas realizadas
        $ventasRealizadas = Budget::where('comercial_id', $comercialId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('budget_status_id', 2) // Aceptado
            ->get();

        $ventasTotales = $ventasRealizadas->sum('total');

        // Calcular clientes únicos
        $clientesUnicos = $ventasRealizadas->pluck('client_id')->unique()->count();

        // Calcular incentivos
        $incentivos = $incentivo->calcularIncentivo($ventasTotales, $clientesUnicos);

        // Calcular ventas por plan
        $ventasPorPlan = [
            'esencial' => $ventasRealizadas->where('concept', 'like', '%esencial%')->sum('total'),
            'profesional' => $ventasRealizadas->where('concept', 'like', '%profesional%')->sum('total'),
            'avanzado' => $ventasRealizadas->where('concept', 'like', '%avanzado%')->sum('total')
        ];

        return response()->json([
            'success' => true,
            'incentivo' => $incentivo,
            'progreso' => [
                'ventas_totales' => $ventasTotales,
                'clientes_unicos' => $clientesUnicos,
                'ventas_por_plan' => $ventasPorPlan,
                'incentivos' => $incentivos,
                'cumple_minimo_clientes' => $clientesUnicos >= $incentivo->min_clientes_mensuales,
                'cumple_minimo_ventas' => $ventasTotales >= $incentivo->min_ventas_mensuales
            ]
        ]);
    }

    /**
     * Actualizar un incentivo
     */
    public function update(Request $request, $id)
    {
        $incentivo = IncentivoComercial::findOrFail($id);

        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'porcentaje_venta' => 'required|numeric|min:0|max:100',
            'porcentaje_adicional' => 'required|numeric|min:0|max:100',
            'min_clientes_mensuales' => 'required|integer|min:1',
            'min_ventas_mensuales' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string'
        ]);

        $incentivo->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Incentivo actualizado exitosamente',
            'incentivo' => $incentivo->load(['comercial', 'admin'])
        ]);
    }

    /**
     * Desactivar un incentivo
     */
    public function destroy($id)
    {
        $incentivo = IncentivoComercial::findOrFail($id);
        $incentivo->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Incentivo desactivado exitosamente'
        ]);
    }
}
