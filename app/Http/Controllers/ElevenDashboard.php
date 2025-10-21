<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsSyncLog;
use App\Services\ElevenlabsService;
use App\Services\ElevenlabsAIService;
use App\Jobs\ProcessElevenlabsConversation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ElevenDashboard extends Controller
{
    /**
     * Mostrar dashboard principal
     */
    public function index(Request $request)
    {
        // Obtener rango de fechas del filtro
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Estadísticas generales
        $stats = [
            'total_conversations' => ElevenlabsConversation::whereBetween('conversation_date', [$startDate, $endDate])->count(),
            'processed_conversations' => ElevenlabsConversation::completed()->whereBetween('conversation_date', [$startDate, $endDate])->count(),
            'pending_conversations' => ElevenlabsConversation::pending()->count(),
            'average_duration' => ElevenlabsConversation::getAverageDuration($startDate, $endDate),
            'satisfaction_rate' => ElevenlabsConversation::getSatisfactionRate($startDate, $endDate),
        ];

        // Estadísticas por categoría
        $categoryStats = ElevenlabsConversation::getStatsByCategory($startDate, $endDate);

        // Últimas conversaciones
        $recentConversations = ElevenlabsConversation::with('client')
            ->orderBy('conversation_date', 'desc')
            ->limit(10)
            ->get();

        // Alertas
        $alerts = [
            'quejas' => ElevenlabsConversation::byCategory('queja')
                ->whereBetween('conversation_date', [$startDate, $endDate])
                ->count(),
            'bajas' => ElevenlabsConversation::byCategory('baja')
                ->whereBetween('conversation_date', [$startDate, $endDate])
                ->count(),
            'necesitan_asistencia' => ElevenlabsConversation::byCategory('necesita_asistencia')
                ->whereBetween('conversation_date', [$startDate, $endDate])
                ->count(),
        ];

        // Última sincronización
        $lastSync = ElevenlabsSyncLog::completed()
            ->orderBy('sync_finished_at', 'desc')
            ->first();

        return view('elevenlabs.dashboard', compact(
            'stats',
            'categoryStats',
            'recentConversations',
            'alerts',
            'lastSync',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Listado de conversaciones con filtros
     */
    public function conversations(Request $request)
    {
        $query = ElevenlabsConversation::with('client');

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filtro por estado de procesamiento
        if ($request->filled('status')) {
            $query->where('processing_status', $request->status);
        }

        // Filtro por rango de fechas
        if ($request->filled('start_date')) {
            $query->where('conversation_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('conversation_date', '<=', $request->end_date);
        }

        // Filtro por cliente
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Búsqueda en transcripción o resumen
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transcript', 'like', "%{$search}%")
                  ->orWhere('summary_es', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'conversation_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $conversations = $query->paginate(20);

        // Categorías para el filtro
        $categories = config('elevenlabs.categories');

        return view('elevenlabs.conversations', compact('conversations', 'categories'));
    }

    /**
     * Detalle de una conversación
     */
    public function show($id)
    {
        $conversation = ElevenlabsConversation::with('client')->findOrFail($id);

        return view('elevenlabs.conversation', compact('conversation'));
    }

    /**
     * Sincronizar conversaciones manualmente
     */
    public function sync(Request $request)
    {
        try {
            // Ejecutar comando de sincronización en background
            Artisan::queue('elevenlabs:sync');

            return response()->json([
                'success' => true,
                'message' => 'Sincronización iniciada. Revisa el log de sincronización para ver el progreso.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sincronización: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reprocesar una conversación específica
     */
    public function reprocess($id)
    {
        try {
            $conversation = ElevenlabsConversation::findOrFail($id);

            if (empty($conversation->transcript)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La conversación no tiene transcripción para procesar.',
                ], 400);
            }

            // Resetear estado a pendiente
            $conversation->update([
                'processing_status' => 'pending',
                'category' => null,
                'confidence_score' => null,
                'summary_es' => null,
                'processed_at' => null,
            ]);

            // Despachar job de procesamiento
            ProcessElevenlabsConversation::dispatch($conversation->id);

            return response()->json([
                'success' => true,
                'message' => 'Conversación enviada a reprocesamiento.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reprocesar conversación: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Estadísticas para gráficas (AJAX)
     */
    public function stats(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Estadísticas por categoría
        $categoryStats = ElevenlabsConversation::getStatsByCategory($startDate, $endDate);
        
        $categories = [];
        $counts = [];
        foreach ($categoryStats as $stat) {
            $categoryConfig = config("elevenlabs.categories.{$stat->category}");
            $categories[] = $categoryConfig['label'] ?? $stat->category;
            $counts[] = $stat->count;
        }

        // Tendencia temporal (últimos 30 días)
        $timeline = ElevenlabsConversation::whereBetween('conversation_date', [$startDate, $endDate])
            ->selectRaw('DATE(conversation_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $dailyCounts = [];
        foreach ($timeline as $day) {
            $dates[] = Carbon::parse($day->date)->format('d/m');
            $dailyCounts[] = $day->count;
        }

        // Distribución por categoría a lo largo del tiempo
        $categoryTrends = [];
        foreach (config('elevenlabs.categories') as $key => $category) {
            $trend = ElevenlabsConversation::where('category', $key)
                ->whereBetween('conversation_date', [$startDate, $endDate])
                ->selectRaw('DATE(conversation_date) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
            
            $categoryTrends[$key] = $trend;
        }

        return response()->json([
            'category_stats' => [
                'labels' => $categories,
                'data' => $counts,
            ],
            'timeline' => [
                'labels' => $dates,
                'data' => $dailyCounts,
            ],
            'category_trends' => $categoryTrends,
            'satisfaction_rate' => ElevenlabsConversation::getSatisfactionRate($startDate, $endDate),
            'total_conversations' => ElevenlabsConversation::whereBetween('conversation_date', [$startDate, $endDate])->count(),
        ]);
    }

    /**
     * Exportar conversaciones
     */
    public function export(Request $request)
    {
        $query = ElevenlabsConversation::with('client');

        // Aplicar filtros
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('start_date')) {
            $query->where('conversation_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('conversation_date', '<=', $request->end_date);
        }

        $conversations = $query->get();

        // Preparar datos para exportar
        $export = [];
        foreach ($conversations as $conversation) {
            $export[] = [
                'ID Conversación' => $conversation->conversation_id,
                'Fecha' => $conversation->conversation_date->format('Y-m-d H:i:s'),
                'Cliente' => $conversation->client ? $conversation->client->name : 'N/A',
                'Duración' => $conversation->duration_formatted,
                'Categoría' => $conversation->category_label,
                'Confianza' => $conversation->confidence_score,
                'Resumen' => $conversation->summary_es,
                'Estado' => $conversation->status_label,
            ];
        }

        // Generar CSV
        $filename = 'conversaciones_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($export) {
            $file = fopen('php://output', 'w');
            
            // Escribir BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            if (!empty($export)) {
                fputcsv($file, array_keys($export[0]));
            }
            
            // Datos
            foreach ($export as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
