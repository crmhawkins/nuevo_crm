<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ElevenlabsConversation;
use App\Jobs\ProcessElevenlabsConversation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class ElevenlabsApiController extends Controller
{
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

        return response()->json([
            'total_conversations' => ElevenlabsConversation::whereBetween('conversation_date', [$startDate, $endDate])->count(),
            'processed_conversations' => ElevenlabsConversation::completed()->whereBetween('conversation_date', [$startDate, $endDate])->count(),
            'pending_conversations' => ElevenlabsConversation::pending()->count(),
            'failed_conversations' => ElevenlabsConversation::failed()->count(),
            'average_duration' => ElevenlabsConversation::getAverageDuration($startDate, $endDate),
            'satisfaction_rate' => ElevenlabsConversation::getSatisfactionRate($startDate, $endDate),
        ]);
    }

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
            // Buscar en la BD de categorías de agente
            $label = $stat->category;
            $color = '#6B7280';
            
            // Intentar obtener de cualquier agente (porque puede ser compartida)
            $agentCat = \App\Models\ElevenlabsAgentCategory::where('category_key', $stat->category)->first();
            if ($agentCat) {
                $label = $agentCat->category_label;
                $color = $agentCat->color;
            } else {
                // Fallback a config
                $categoryConfig = config("elevenlabs.categories.{$stat->category}");
                $label = $categoryConfig['label'] ?? ucfirst(str_replace('_', ' ', $stat->category));
                $color = $categoryConfig['color'] ?? '#6B7280';
            }
            
            $data['labels'][] = $label;
            $data['counts'][] = $stat->count;
            $data['colors'][] = $color;
        }

        return response()->json($data);
    }

    public function index(Request $request)
    {
        $query = ElevenlabsConversation::with('client');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('processing_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transcript', 'like', "%{$search}%")
                  ->orWhere('summary_es', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'conversation_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 20);
        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        $conversation = ElevenlabsConversation::with('client')->findOrFail($id);
        
        // Incluir atributos calculados
        $data = $conversation->toArray();
        $data['sentiment_label'] = $conversation->sentiment_label;
        $data['sentiment_color'] = $conversation->sentiment_color;
        $data['specific_label'] = $conversation->specific_label;
        $data['specific_color'] = $conversation->specific_color;
        $data['category_label'] = $conversation->category_label;
        $data['category_color'] = $conversation->category_color;
        $data['status_label'] = $conversation->status_label;
        $data['duration_formatted'] = $conversation->duration_formatted;
        $data['scheduled_call_datetime'] = $conversation->scheduled_call_datetime ? $conversation->scheduled_call_datetime->toIso8601String() : null;
        $data['attended'] = $conversation->attended;
        $data['attended_at'] = $conversation->attended_at ? $conversation->attended_at->format('d/m/Y H:i') : null;
        
        return response()->json($data);
    }

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

    public function process($id)
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
                'message' => 'Enviada a procesamiento',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
