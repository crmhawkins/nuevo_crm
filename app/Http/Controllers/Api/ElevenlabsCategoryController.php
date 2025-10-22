<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsAgent;
use Illuminate\Support\Facades\Log;

class ElevenlabsCategoryController extends Controller
{
    /**
     * Actualizar categorías manualmente
     */
    public function updateCategories(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'sentiment_category' => 'nullable|string',
                'specific_category' => 'nullable|string',
            ]);

            $conversation = ElevenlabsConversation::findOrFail($id);
            
            $conversation->sentiment_category = $validated['sentiment_category'];
            $conversation->specific_category = $validated['specific_category'];
            
            // Si es baja o sin_respuesta, limpiar specific
            if (in_array($validated['sentiment_category'], ['baja', 'sin_respuesta'])) {
                $conversation->specific_category = null;
            }
            
            $conversation->save();

            Log::info('✏️ Categorías actualizadas manualmente', [
                'conversation_id' => $conversation->conversation_id,
                'sentiment' => $conversation->sentiment_category,
                'specific' => $conversation->specific_category,
                'user' => auth()->user()->name ?? 'unknown',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categorías actualizadas correctamente',
                'sentiment_label' => $conversation->sentiment_label,
                'sentiment_color' => $conversation->sentiment_color,
                'specific_label' => $conversation->specific_label,
                'specific_color' => $conversation->specific_color,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener categorías disponibles para un agente
     */
    public function getAvailableCategories($agentId)
    {
        try {
            $agent = ElevenlabsAgent::where('agent_id', $agentId)->first();
            
            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agente no encontrado',
                ], 404);
            }

            $categories = $agent->getCategories();
            
            // Separar por tipo
            // Sentimientos: solo las 5 categorías fijas
            $sentimentKeys = ['contento', 'descontento', 'sin_respuesta', 'baja', 'llamada_agendada'];
            $sentiment = array_values(array_filter($categories, fn($c) => in_array($c['category_key'], $sentimentKeys)));
            
            // Específicas: todas las demás (las personalizadas del agente)
            $specific = array_values(array_filter($categories, fn($c) => !in_array($c['category_key'], $sentimentKeys)));

            return response()->json([
                'success' => true,
                'sentiment_categories' => array_values($sentiment),
                'specific_categories' => array_values($specific),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
