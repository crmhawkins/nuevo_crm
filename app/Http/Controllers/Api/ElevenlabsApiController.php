<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ElevenlabsConversation;
use App\Services\ElevenlabsService;
use App\Jobs\ProcessElevenlabsConversation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class ElevenlabsApiController extends Controller
{
    /**
     * Estadísticas generales
     */
    public function statsOverview(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $stats = [
            'total_conversations' => ElevenlabsConversation::whereBetween('conversation_date', [$startDate, $endDate])->count(),
            'processed_conversations' => ElevenlabsConversation::completed()->whereBetween('conversation_date', [$startDate, $endDate])->count(),
            'pending_conversations' => ElevenlabsConversation::pending()->count(),
            'failed_conversations' => ElevenlabsConversation::failed()->count(),
            'average_duration' => ElevenlabsConversation::getAverageDuration($startDate, $endDate),
            'satisfaction_rate' => ElevenlabsConversation::getSatisfactionRate($startDate, $endDate),
        ];

        return response()->json($stats);
    }

    /**
     * Estadísticas por categoría
     */
    public function statsCategories(Request $request)
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

        $data = [
            'labels' => [],
            'counts' => [],
            'colors' => [],
        ];

        foreach ($categoryStats as $stat) {
            $categoryConfig = config("elevenlabs.categories.{$stat->category}");
            $data['labels'][] = $categoryConfig['label'] ?? $stat->category;
            $data['counts'][] = $stat->count;
            $data['colors'][] = $categoryConfig['color'] ?? '#6B7280';
        }

        return response()->json($data);
    }

    /**
     * Línea temporal de conversaciones
     */
    public function statsTimeline(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Formato de agrupación según el tipo
        $dateFormat = match($groupBy) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $timeline = ElevenlabsConversation::whereBetween('conversation_date', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(conversation_date, '{$dateFormat}') as period, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $data = [
            'labels' => [],
            'data' => [],
        ];

        foreach ($timeline as $period) {
            $data['labels'][] = $period->period;
            $data['data'][] = $period->count;
        }

        return response()->json($data);
    }

    /**
     * Lista de conversaciones
     */
    public function index(Request $request)
    {
        $query = ElevenlabsConversation::with('client');

        // Filtros
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('processing_status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('conversation_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('conversation_date', '<=', $request->end_date);
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transcript', 'like', "%{$search}%")
                  ->orWhere('summary_es', 'like', "%{$search}%")
                  ->orWhere('conversation_id', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'conversation_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 20);
        $conversations = $query->paginate($perPage);

        return response()->json($conversations);
    }

    /**
     * Detalle de una conversación
     */
    public function show($id)
    {
        $conversation = ElevenlabsConversation::with('client')->findOrFail($id);

        return response()->json($conversation);
    }

    /**
     * Sincronizar conversaciones
     */
    public function sync(Request $request)
    {
        try {
            $fromDate = $request->get('from_date');
            $force = $request->boolean('force', false);

            $command = 'elevenlabs:sync';
            if ($fromDate) {
                $command .= " --from={$fromDate}";
            }
            if ($force) {
                $command .= " --force";
            }

            Artisan::queue($command);

            return response()->json([
                'success' => true,
                'message' => 'Sincronización iniciada en segundo plano',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sincronización: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Procesar una conversación específica
     */
    public function process($id)
    {
        try {
            $conversation = ElevenlabsConversation::findOrFail($id);

            if (empty($conversation->transcript)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La conversación no tiene transcripción',
                ], 400);
            }

            // Resetear a pendiente
            $conversation->update([
                'processing_status' => 'pending',
                'category' => null,
                'confidence_score' => null,
                'summary_es' => null,
                'processed_at' => null,
            ]);

            // Despachar job
            ProcessElevenlabsConversation::dispatch($conversation->id);

            return response()->json([
                'success' => true,
                'message' => 'Conversación enviada a procesamiento',
                'conversation' => $conversation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
