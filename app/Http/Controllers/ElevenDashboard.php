<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsAgentCategory;
use App\Jobs\ProcessElevenlabsConversation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class ElevenDashboard extends Controller
{
    /**
     * Dashboard principal
     */
    public function index(Request $request)
    {
        // Estadísticas generales (últimos 30 días por defecto)
        $startDate = now()->subDays(30);
        $endDate = now();

        // Filtro por rango de fechas (si existe)
        $dateRange = $request->get('date_range');
        $filterStartDate = null;
        $filterEndDate = null;

        if ($dateRange && str_contains($dateRange, ' a ')) {
            // Flatpickr con formato "YYYY-MM-DD a YYYY-MM-DD"
            $dates = explode(' a ', $dateRange);
            $filterStartDate = Carbon::parse($dates[0])->startOfDay();
            $filterEndDate = Carbon::parse($dates[1])->endOfDay();
        }

        $stats = [
            'total_conversations' => ElevenlabsConversation::count(),
            'processed_conversations' => ElevenlabsConversation::completed()->count(),
            'pending_conversations' => ElevenlabsConversation::pending()->count(),
            'last_30_days' => ElevenlabsConversation::where('conversation_date', '>=', $startDate)->count(),
            'average_duration' => ElevenlabsConversation::getAverageDuration($startDate, $endDate),
            'satisfaction_rate' => ElevenlabsConversation::getSatisfactionRate($startDate, $endDate),
        ];

        $categoryStats = ElevenlabsConversation::getStatsByCategory($startDate, $endDate);

        // Obtener categorías de agentes para colores dinámicos
        $agentCategories = ElevenlabsAgentCategory::all()->pluck('category_label', 'category_key')->toArray();
        $agentCategoryColors = ElevenlabsAgentCategory::all()->pluck('color', 'category_key')->toArray();

        // Conversaciones recientes con paginación y ordenamiento
        $sortBy = $request->get('sort_by', 'conversation_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $categoryFilter = $request->get('category');
        $agentFilter = $request->get('agent_id');

        // Checkbox: si tiene valor '1' está marcado, si no existe (null) está desmarcado
        // Por defecto ocultar si no se ha enviado el formulario aún
        $hideNoResponse = $request->has('hide_no_response')
            ? $request->get('hide_no_response') === '1'
            : (!$request->has('agent_id') && !$request->has('category') && !$request->has('date_range')); // Si no hay filtros aplicados, ocultar por defecto

        $query = ElevenlabsConversation::with('client');

        // Filtro por rango de fechas
        if ($filterStartDate && $filterEndDate) {
            $query->whereBetween('conversation_date', [$filterStartDate, $filterEndDate]);
        }

        // Filtro por agente
        if ($agentFilter) {
            $query->where('agent_id', $agentFilter);
        }

        // Filtro por categoría (sentimiento o específica)
        if ($categoryFilter) {
            $query->where(function($q) use ($categoryFilter) {
                $q->where('sentiment_category', $categoryFilter)
                  ->orWhere('specific_category', $categoryFilter);
            });
        }

        // Ocultar conversaciones sin respuesta si el checkbox está marcado
        // Solo aplicar si no hay filtro de categoría específico
        if ($hideNoResponse && !$categoryFilter) {
            $query->where('sentiment_category', '!=', 'sin_respuesta');
        }

        // Ordenamiento
        $query->orderBy($sortBy, $sortOrder);

        // Paginación configurable
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15; // Validar valores permitidos
        
        $recentConversations = $query->paginate($perPage)->appends($request->query());

        $alerts = [
            'contentos' => ElevenlabsConversation::bySentiment('contento')->where('conversation_date', '>=', $startDate)->count(),
            'descontentos' => ElevenlabsConversation::bySentiment('descontento')->where('conversation_date', '>=', $startDate)->count(),
            'sin_respuesta' => ElevenlabsConversation::bySentiment('sin_respuesta')->where('conversation_date', '>=', $startDate)->count(),
        ];

        return view('elevenlabs.dashboard', compact(
            'stats',
            'categoryStats',
            'agentCategories',
            'agentCategoryColors',
            'recentConversations',
            'alerts',
            'sortBy',
            'sortOrder',
            'categoryFilter',
            'agentFilter',
            'hideNoResponse'
        ));
    }

    /**
     * Listado de conversaciones con filtros avanzados
     */
    public function conversations(Request $request)
    {
        $query = ElevenlabsConversation::with('client');

        // Búsqueda general (ID, agente, transcripción, resumen)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('conversation_id', 'like', "%{$search}%")
                  ->orWhere('agent_name', 'like', "%{$search}%")
                  ->orWhere('transcript', 'like', "%{$search}%")
                  ->orWhere('summary_es', 'like', "%{$search}%");
            });
        }

        // Filtro por agente
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filtro por categoría (puede ser sentimiento o específica)
        if ($request->filled('category')) {
            if ($request->category === 'sin_categoria') {
                $query->whereNull('sentiment_category')->whereNull('specific_category');
            } else {
                // Buscar en ambas columnas
                $query->where(function($q) use ($request) {
                    $q->where('sentiment_category', $request->category)
                      ->orWhere('specific_category', $request->category);
                });
            }
        }

        // Filtro por estado de procesamiento
        if ($request->filled('status')) {
            $query->where('processing_status', $request->status);
        }

        // Filtro por satisfacción
        if ($request->filled('satisfaction')) {
            if ($request->satisfaction === 'contentos') {
                $query->where('sentiment_category', 'contento');
            } elseif ($request->satisfaction === 'descontentos') {
                $query->where('sentiment_category', 'descontento');
            }
        }

        // Filtro por tiene transcripción
        if ($request->filled('has_transcript')) {
            if ($request->has_transcript === 'yes') {
                $query->whereNotNull('transcript');
            } else {
                $query->whereNull('transcript');
            }
        }

        // Filtro por tiene resumen
        if ($request->filled('has_summary')) {
            if ($request->has_summary === 'yes') {
                $query->whereNotNull('summary_es');
            } else {
                $query->whereNull('summary_es');
            }
        }

        // Filtro por rango de fechas
        if ($request->filled('start_date')) {
            $query->where('conversation_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('conversation_date', '<=', $request->end_date);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'conversation_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $conversations = $query->paginate(20)->appends($request->all());
        $categories = config('elevenlabs.categories');

        return view('elevenlabs.conversations', compact('conversations', 'categories'));
    }

    /**
     * Detalle de conversación
     */
    public function show($id)
    {
        $conversation = ElevenlabsConversation::with('client')->findOrFail($id);
        return view('elevenlabs.conversation', compact('conversation'));
    }

    /**
     * Sincronizar manualmente
     */
    public function sync(Request $request)
    {
        try {
            Artisan::queue('elevenlabs:sync', [
                '--limit' => $request->get('limit', 10),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sincronización iniciada',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reprocesar conversación
     */
    public function reprocess($id)
    {
        try {
            $conversation = ElevenlabsConversation::findOrFail($id);

            if (empty($conversation->transcript)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sin transcripción',
                ], 400);
            }

            $conversation->update([
                'processing_status' => 'pending',
                'category' => null,
                'confidence_score' => null,
                'summary_es' => null,
                'processed_at' => null,
            ]);

            ProcessElevenlabsConversation::dispatch($conversation->id);

            return response()->json([
                'success' => true,
                'message' => 'Enviada a reprocesamiento',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Estadísticas para gráficas
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

        $categoryStats = ElevenlabsConversation::getStatsByCategory($startDate, $endDate);

        $categories = [];
        $counts = [];
        foreach ($categoryStats as $stat) {
            $categoryConfig = config("elevenlabs.categories.{$stat->category}");
            $categories[] = $categoryConfig['label'] ?? $stat->category;
            $counts[] = $stat->count;
        }

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

        return response()->json([
            'category_stats' => [
                'labels' => $categories,
                'data' => $counts,
            ],
            'timeline' => [
                'labels' => $dates,
                'data' => $dailyCounts,
            ],
        ]);
    }

    /**
     * Exportar a CSV
     */
    public function export(Request $request)
    {
        $query = ElevenlabsConversation::with('client');

        if ($request->filled('category')) {
            // Buscar en ambas columnas
            $query->where(function($q) use ($request) {
                $q->where('sentiment_category', $request->category)
                  ->orWhere('specific_category', $request->category);
            });
        }
        if ($request->filled('start_date')) {
            $query->where('conversation_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('conversation_date', '<=', $request->end_date);
        }

        $conversations = $query->get();

        $export = [];
        foreach ($conversations as $conversation) {
            $export[] = [
                'ID' => $conversation->conversation_id,
                'Fecha' => $conversation->conversation_date->format('Y-m-d H:i:s'),
                'Agente' => $conversation->agent_name ?? 'N/A',
                'Cliente' => $conversation->client ? $conversation->client->name : 'N/A',
                'Duración' => $conversation->duration_formatted,
                'Sentimiento' => $conversation->sentiment_label ?? '-',
                'Categoría Específica' => $conversation->specific_label ?? '-',
                'Confianza' => $conversation->confidence_score ? round($conversation->confidence_score * 100, 1) . '%' : '-',
                'Resumen' => $conversation->summary_es ?? '-',
                'Estado' => $conversation->status_label,
            ];
        }

        $filename = 'conversaciones_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($export) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if (!empty($export)) {
                fputcsv($file, array_keys($export[0]));
            }

            foreach ($export as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Marcar conversación como atendida
     */
    public function markAsAttended(Request $request, $id)
    {
        $conversation = ElevenlabsConversation::findOrFail($id);

        $conversation->attended = true;
        $conversation->attended_at = now();
        $conversation->attended_by = auth()->id();
        $conversation->save();

        return response()->json([
            'success' => true,
            'message' => 'Conversación marcada como atendida',
            'attended' => true,
            'attended_at' => $conversation->attended_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Desmarcar conversación como atendida
     */
    public function unmarkAsAttended(Request $request, $id)
    {
        $conversation = ElevenlabsConversation::findOrFail($id);

        $conversation->attended = false;
        $conversation->attended_at = null;
        $conversation->attended_by = null;
        $conversation->save();

        return response()->json([
            'success' => true,
            'message' => 'Conversación desmarcada como atendida',
            'attended' => false,
        ]);
    }
}

