<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElevenlabsAgent;
use App\Models\ElevenlabsAgentCategory;
use App\Services\ElevenlabsAIService;
use Illuminate\Support\Facades\Log;

class ElevenAgentsController extends Controller
{
    /**
     * Mostrar panel de gestión de agentes
     */
    public function index()
    {
        $agents = ElevenlabsAgent::with('categories')
            ->orderBy('name')
            ->get();

        return view('elevenlabs.agents', compact('agents'));
    }

    /**
     * Actualizar descripción del agente
     */
    public function updateDescription(Request $request, $agentId)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|min:10',
            ]);

            $agent = ElevenlabsAgent::where('agent_id', $agentId)->firstOrFail();
            $agent->description = $validated['description'];
            $agent->save();

            return response()->json([
                'success' => true,
                'message' => 'Descripción actualizada',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', array_map(fn($m) => implode(', ', $m), $e->errors())),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generar categorías sugeridas por IA basadas en la descripción
     */
    public function generateCategories(Request $request, $agentId)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string',
            ]);

            $agent = ElevenlabsAgent::where('agent_id', $agentId)->firstOrFail();
            $aiService = new ElevenlabsAIService();

            $prompt = "Eres un experto en diseño de sistemas de categorización para agentes de IA conversacional con años de experiencia en análisis de conversaciones.

## TAREA

Analiza la descripción del agente y genera MÍNIMO 4 categorías PERSONALIZADAS que sean ÚTILES y ESPECÍFICAS para este tipo de llamadas.

Puedes generar más de 4 si consideras que son necesarias para un análisis completo del agente, pero MÍNIMO 4.

## DESCRIPCIÓN DEL AGENTE

{$validated['description']}

## CATEGORÍAS QUE YA EXISTEN (NO las incluyas)

✅ contento - Cliente satisfecho (sentimiento)
✅ descontento - Cliente insatisfecho (sentimiento)
✅ sin_respuesta - Cliente no responde (sentimiento)
✅ baja - Cliente solicita darse de baja (acción específica)
✅ llamada_agendada - Se agenda una cita o llamada (acción específica)
✅ respuesta_ia - Contestó un contestador automático o asistente virtual

## REQUISITOS PARA LAS NUEVAS CATEGORÍAS (MÍNIMO 4)

1. **ESPECÍFICAS:** Deben reflejar situaciones ÚNICAS de este tipo de agente
2. **ACCIONABLES:** Que permitan tomar decisiones o hacer seguimiento
3. **CLARAS:** Sin ambigüedad sobre cuándo usar cada una
4. **NO SOLAPADAS:** Cada categoría debe ser distinta de las demás
5. **ÚTILES:** Que aporten valor real al análisis

## EJEMPLOS POR TIPO DE AGENTE

**Si es agente de VENTAS/PROMOCIONES:**
- `interesado` - Cliente muestra interés en la oferta
- `solicita_info` - Pide más información o documentación
- `no_interesado` - Rechaza la oferta amablemente
- `llamar_despues` - Cliente pide que se le contacte en otro momento
- `spam` - Cliente indica que es spam o llaman mucho

**Si es agente de RESERVAS:**
- `solicitud_reserva` - Cliente quiere hacer una reserva
- `consulta_disponibilidad` - Pregunta por fechas/precios
- `modificacion_reserva` - Cambiar una reserva existente
- `cancelacion` - Solicita cancelar una reserva
- `confirmacion` - Cliente confirma datos de su reserva

**Si es agente de SOPORTE:**
- `problema_resuelto` - Incidencia solucionada en la llamada
- `requiere_seguimiento` - Necesita atención posterior
- `problema_tecnico` - Fallo del servicio o sistema
- `informacion_general` - Consultas sobre cómo usar el servicio
- `reembolso` - Cliente solicita devolución de dinero

## FORMATO DE RESPUESTA

Responde ÚNICAMENTE con este JSON (sin texto adicional):

```json
{
    \"suggested_categories\": [
        {
            \"key\": \"primera_categoria\",
            \"label\": \"Primera Categoría\",
            \"description\": \"Descripción detallada con ejemplos de frases clave\"
        },
        {
            \"key\": \"segunda_categoria\",
            \"label\": \"Segunda Categoría\",
            \"description\": \"Cuándo usar, señales claras, ejemplos\"
        },
        {
            \"key\": \"tercera_categoria\",
            \"label\": \"Tercera Categoría\",
            \"description\": \"Cuándo aplicar, indicadores, casos de uso\"
        },
        {
            \"key\": \"cuarta_categoria\",
            \"label\": \"Cuarta Categoría\",
            \"description\": \"Cuándo aplicar, señales, ejemplos\"
        }
        // Puedes agregar más si es necesario
    ]
}
```

Genera MÍNIMO 4, pero si necesitas más categorías para cubrir todos los casos típicos de este agente, agrégalas.

**IMPORTANTE:**
- `key`: minúsculas, sin espacios, sin acentos (ej: `solicita_info`, `problema_tecnico`)
- `label`: Nombre claro en español (ej: \"Solicita Información\", \"Problema Técnico\")
- `description`: Mínimo 20 palabras, máximo 100. Debe ser muy claro cuándo usar.

GENERA LAS 3 CATEGORÍAS AHORA:";

            $response = $aiService->sendChatRequest($prompt);
            
            if (!$response) {
                throw new \Exception('No se pudo obtener respuesta de la IA');
            }

            // Parsear respuesta
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Intentar extraer JSON del texto
                if (preg_match('/\{[\s\S]*\}/', $response, $matches)) {
                    $data = json_decode($matches[0], true);
                }
            }

            if (!isset($data['suggested_categories'])) {
                throw new \Exception('Formato de respuesta inválido');
            }

            // Colores predefinidos para las categorías sugeridas
            $colors = ['#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899', '#14B8A6'];

            foreach ($data['suggested_categories'] as $index => &$category) {
                $category['color'] = $colors[$index % count($colors)];
                $category['icon'] = ''; // Sin iconos
            }

            return response()->json([
                'success' => true,
                'categories' => $data['suggested_categories'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', array_map(fn($m) => implode(', ', $m), $e->errors())),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error generando categorías', [
                'agent_id' => $agentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Guardar categorías del agente
     */
    public function saveCategories(Request $request, $agentId)
    {
        try {
            $validated = $request->validate([
                'categories' => 'required|array|min:4',  // Mínimo 4, sin máximo
                'categories.*.key' => 'required|string',
                'categories.*.label' => 'required|string',
                'categories.*.description' => 'nullable|string',
                'categories.*.color' => 'required|string',
                'categories.*.icon' => 'nullable|string',
            ]);

            $agent = ElevenlabsAgent::where('agent_id', $agentId)->firstOrFail();

            // Asegurarse de que existan las categorías por defecto
            ElevenlabsAgentCategory::createDefaultCategories($agentId);

            // Eliminar categorías no-default anteriores
            ElevenlabsAgentCategory::where('agent_id', $agentId)
                ->where('is_default', false)
                ->delete();

            // Crear nuevas categorías personalizadas
            foreach ($validated['categories'] as $index => $cat) {
                // Saltar si es una categoría obligatoria (ya existe)
                if (in_array($cat['key'], ['contento', 'descontento', 'sin_respuesta', 'baja', 'llamada_agendada', 'respuesta_ia'])) {
                    continue;
                }

                ElevenlabsAgentCategory::create([
                    'agent_id' => $agentId,
                    'category_key' => $cat['key'],
                    'category_label' => $cat['label'],
                    'category_description' => $cat['description'] ?? null,
                    'color' => $cat['color'],
                    'icon' => $cat['icon'] ?? '',
                    'is_default' => false,
                    'order' => $index + 3, // Después de las 3 obligatorias
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Categorías guardadas exitosamente',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', array_map(fn($m) => implode(', ', $m), $e->errors())),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error guardando categorías', [
                'agent_id' => $agentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener categorías de un agente
     */
    public function getCategories($agentId)
    {
        $agent = ElevenlabsAgent::where('agent_id', $agentId)->firstOrFail();
        $categories = $agent->getCategories();

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }
}
