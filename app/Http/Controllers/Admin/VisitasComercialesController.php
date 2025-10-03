<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitaComercial;
use App\Models\Users\User;
use App\Models\Clients\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VisitasComercialesController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitaComercial::with(['comercial', 'cliente'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('comercial_id')) {
            $query->where('comercial_id', $request->comercial_id);
        }

        if ($request->filled('tipo_visita')) {
            $query->where('tipo_visita', $request->tipo_visita);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('con_audio')) {
            if ($request->con_audio == 'si') {
                $query->whereNotNull('audio_file');
            } else {
                $query->whereNull('audio_file');
            }
        }

        $visitas = $query->paginate(20);
        
        // Datos para filtros
        $comerciales = User::where('access_level_id', 6)->get();
        $clientes = Client::all();
        
        return view('admin.visitas-comerciales.index', compact('visitas', 'comerciales', 'clientes'));
    }

    public function show(VisitaComercial $visita)
    {
        $visita->load(['comercial', 'cliente']);
        return view('admin.visitas-comerciales.show', compact('visita'));
    }

    public function destroy(VisitaComercial $visita)
    {
        // Eliminar audio si existe
        if ($visita->audio_file && \Storage::disk('public')->exists($visita->audio_file)) {
            \Storage::disk('public')->delete($visita->audio_file);
        }

        $visita->delete();

        return redirect()->route('visitas-comerciales.index')
            ->with('success', 'Visita comercial eliminada correctamente.');
    }

    public function getAudio(VisitaComercial $visita)
    {
        if (!$visita->audio_file || !\Storage::disk('public')->exists($visita->audio_file)) {
            return response()->json(['success' => false, 'message' => 'Audio no encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'audio' => [
                'url' => \Storage::url($visita->audio_file),
                'duration' => $visita->audio_duration,
                'recorded_at' => $visita->audio_recorded_at
            ]
        ]);
    }

    public function deleteAudio(VisitaComercial $visita)
    {
        if ($visita->audio_file && \Storage::disk('public')->exists($visita->audio_file)) {
            \Storage::disk('public')->delete($visita->audio_file);
        }

        $visita->update([
            'audio_file' => null,
            'audio_duration' => null,
            'audio_recorded_at' => null
        ]);

        return redirect()->back()
            ->with('success', 'Audio eliminado correctamente.');
    }

    public function estadisticas()
    {
        $totalVisitas = VisitaComercial::count();
        $visitasPresenciales = VisitaComercial::where('tipo_visita', 'presencial')->count();
        $visitasTelefonicas = VisitaComercial::where('tipo_visita', 'telefonico')->count();
        $visitasConAudio = VisitaComercial::whereNotNull('audio_file')->count();
        
        // Visitas por mes
        $visitasPorMes = VisitaComercial::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Visitas por comercial
        $visitasPorComercial = VisitaComercial::with('comercial')
            ->selectRaw('comercial_id, COUNT(*) as total')
            ->groupBy('comercial_id')
            ->orderBy('total', 'desc')
            ->get();

        // Estados de propuestas
        $estadosPropuestas = VisitaComercial::selectRaw('estado, COUNT(*) as total')
            ->whereNotNull('estado')
            ->groupBy('estado')
            ->get();

        return view('admin.visitas-comerciales.estadisticas', compact(
            'totalVisitas',
            'visitasPresenciales',
            'visitasTelefonicas',
            'visitasConAudio',
            'visitasPorMes',
            'visitasPorComercial',
            'estadosPropuestas'
        ));
    }
}