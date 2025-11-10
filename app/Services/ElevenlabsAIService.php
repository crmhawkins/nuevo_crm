<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ElevenlabsConversation;
use Exception;

class ElevenlabsAIService
{
    protected $aiServiceUrl;
    protected $aiApiKey;
    protected $aiModel;
    protected $timeout;
    protected $retryAttempts;
    protected $retryDelay;

    public function __construct()
    {
        $this->aiServiceUrl = config('elevenlabs.ai_service_url');
        $this->aiApiKey = config('elevenlabs.ai_api_key');
        $this->aiModel = config('elevenlabs.ai_model');
        $this->timeout = config('elevenlabs.timeout', 30);
        $this->retryAttempts = config('elevenlabs.retry_attempts', 3);
        $this->retryDelay = config('elevenlabs.retry_delay', 5);
    }

    /**
     * Procesar conversación completa
     */
    public function processConversation(ElevenlabsConversation $conversation): bool
    {
        try {
            $conversation->markAsProcessing();

            // Primera pasada: Categorización de sentimiento (contento/descontento/sin_respuesta)
            $sentimentResult = $this->categorizeSentiment($conversation->transcript);

            if ($sentimentResult) {
                $conversation->sentiment_category = $sentimentResult['category'] ?? null;
                Log::info('✅ Sentimiento categorizado', [
                    'conversation_id' => $conversation->conversation_id,
                    'sentiment' => $conversation->sentiment_category,
                ]);
            }

            // Segunda pasada: Categorización específica (SOLO si NO es sin_respuesta, baja o respuesta_ia)
            $skipCategories = ['sin_respuesta', 'baja', 'respuesta_ia'];

            if (in_array($conversation->sentiment_category, $skipCategories)) {
                Log::info('ℹ️ Categoría de sentimiento no requiere categorización específica', [
                    'conversation_id' => $conversation->conversation_id,
                    'sentiment' => $conversation->sentiment_category,
                    'razon' => 'Sin interacción, solicitud de baja o contestador automático',
                ]);
                $conversation->specific_category = null;
                $conversation->confidence_score = 1.0;
                $conversation->save();
            } else {
                // Categorizar específicamente solo si hubo interacción
                $specificResult = $this->categorizeSpecific(
                    $conversation->transcript,
                    $conversation->agent_id,
                    $conversation->campaign_initial_prompt
                );

                if (!$specificResult) {
                    Log::error('❌ No se pudo categorizar específicamente', [
                        'conversation_id' => $conversation->conversation_id,
                    ]);
                    $conversation->markAsFailed();
                    return false;
                }

                $conversation->specific_category = $specificResult['category'] ?? null;
                $conversation->confidence_score = $specificResult['confidence'] ?? null;
                $conversation->save();

                Log::info('✅ Categorizada', [
                    'conversation_id' => $conversation->conversation_id,
                    'sentiment' => $conversation->sentiment_category,
                    'specific' => $conversation->specific_category,
                    'confidence' => $conversation->confidence_score,
                    'agent_id' => $conversation->agent_id,
                ]);
            }

            // Tercera pasada: Detectar fecha/hora si es llamada agendada
            if ($conversation->sentiment_category === 'llamada_agendada') {
                $scheduledData = $this->extractScheduledCallInfo($conversation->transcript, $conversation->conversation_date);
                if ($scheduledData) {
                    $conversation->scheduled_call_datetime = $scheduledData['datetime'] ?? null;
                    $conversation->scheduled_call_notes = $scheduledData['notes'] ?? null;
                    $conversation->save();
                    Log::info('✅ Información de cita extraída', [
                        'conversation_id' => $conversation->conversation_id,
                        'scheduled_datetime' => $scheduledData['datetime'] ?? null,
                    ]);
                }
            }

            // Cuarta pasada: Resumen (solo si hubo interacción real)
            if (!in_array($conversation->sentiment_category, ['sin_respuesta'])) {
                $summary = $this->summarizeConversation($conversation->transcript);

                if ($summary) {
                    $conversation->summary_es = $summary;
                    $conversation->save();
                    Log::info('✅ Resumen generado', [
                        'conversation_id' => $conversation->conversation_id,
                    ]);
                }
            } else {
                Log::info('ℹ️ Omitiendo resumen (sin interacción)', [
                    'conversation_id' => $conversation->conversation_id,
                ]);
            }

            $conversation->markAsCompleted();

            // Verificar si debe crear alerta para incidencias de Maria Apartamentos
            $this->checkAndCreateIncidenciaAlert($conversation);

            $this->syncCampaignCall($conversation);

            return true;

        } catch (Exception $e) {
            Log::error('❌ Error al procesar conversación', [
                'conversation_id' => $conversation->conversation_id,
                'error' => $e->getMessage(),
            ]);

            $conversation->markAsFailed();
            return false;
        }
    }

    protected function syncCampaignCall(ElevenlabsConversation $conversation): void
    {
        try {
            $campaignCall = $conversation->campaignCall;

            if (!$campaignCall) {
                return;
            }

            $campaignCall->sentiment_category = $conversation->sentiment_category;
            $campaignCall->specific_category = $conversation->specific_category;
            $campaignCall->confidence_score = $conversation->confidence_score;
            $campaignCall->summary = $conversation->summary_es;
            $campaignCall->save();

            if ($campaignCall->campaign) {
                $campaignCall->campaign->refreshCounters();
            }

            Log::info('📊 Campaña actualizada con resultados de llamada', [
                'campaign_call_id' => $campaignCall->id,
                'conversation_id' => $conversation->conversation_id,
                'sentiment' => $conversation->sentiment_category,
                'specific' => $conversation->specific_category,
            ]);
        } catch (Exception $e) {
            Log::error('❌ Error sincronizando llamada de campaña', [
                'conversation_id' => $conversation->conversation_id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Verificar y crear alerta si es una incidencia de Maria Apartamentos
     */
    protected function checkAndCreateIncidenciaAlert(ElevenlabsConversation $conversation): void
    {
        try {
            // Verificar si tiene categoría específica
            if (empty($conversation->specific_category)) {
                return;
            }

            // Categorías de incidencias que deben generar alerta
            $incidenciaCategories = [
                'incidencia_general',
                'incidencia_limpieza',
                'incidencia_mantenimiento',
                'incidencia_de_limpieza',
                'incidencia_de_mantenimiento',
            ];

            // Verificar si la categoría es una incidencia
            if (!in_array($conversation->specific_category, $incidenciaCategories)) {
                return;
            }

            // Obtener el agente
            $agent = \App\Models\ElevenlabsAgent::findByAgentId($conversation->agent_id);

            // Verificar si el agente es Maria Apartamentos
            if (!$agent || stripos($agent->name, 'Maria Apartamentos') === false) {
                return;
            }

            // Crear alerta para el usuario ID 8
            $this->createIncidenciaAlert($conversation, $agent);

            Log::info('✅ Alerta de incidencia creada (WhatsApp será enviado por cron)', [
                'conversation_id' => $conversation->conversation_id,
                'agent_name' => $agent->name,
                'category' => $conversation->specific_category,
            ]);

        } catch (Exception $e) {
            Log::error('❌ Error al crear alerta de incidencia', [
                'conversation_id' => $conversation->conversation_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Crear alerta de incidencia para usuario específico
     */
    protected function createIncidenciaAlert(ElevenlabsConversation $conversation, \App\Models\ElevenlabsAgent $agent): void
    {
        $categoryLabels = [
            'incidencia_general' => 'Incidencia General',
            'incidencia_limpieza' => 'Incidencia de Limpieza',
            'incidencia_mantenimiento' => 'Incidencia de Mantenimiento',
            'incidencia_de_limpieza' => 'Incidencia de Limpieza',
            'incidencia_de_mantenimiento' => 'Incidencia de Mantenimiento',
        ];

        $categoryLabel = $categoryLabels[$conversation->specific_category] ?? 'Incidencia';
        $phoneNumber = $conversation->numero ? ' (' . $conversation->numero . ')' : '';

        $description = "[{$agent->name}] {$categoryLabel}{$phoneNumber} - " .
                       ($conversation->summary_es ? substr($conversation->summary_es, 0, 150) : 'Revisar llamada');

        \App\Models\Alerts\Alert::create([
            'reference_id' => $conversation->id,
            'admin_user_id' => 8, // Usuario que debe recibir la alerta
            'stage_id' => 15, // Stage para alertas de ElevenLabs
            'status_id' => 1, // Alerta activa
            'activation_datetime' => \Carbon\Carbon::now(),
            'cont_postpone' => 0,
            'description' => $description,
        ]);
    }

    /**
     * Categorizar sentimiento (contento/descontento/sin_respuesta)
     */
    protected function categorizeSentiment(string $transcript): ?array
    {
        $prompt = "Analiza el SENTIMIENTO general del cliente en esta conversación.

Clasifica en UNA de estas 6 opciones:
1. **contento** - Cliente satisfecho, acepta, agradece, tono positivo, o responde de forma educada aunque sea breve
2. **descontento** - Cliente rechaza, no interesado, tono negativo o borde
3. **sin_respuesta** - Cliente NO responde EN ABSOLUTO. Solo hay silencio, \"...\", o el agente habla solo sin NINGUNA respuesta del cliente. Esta categoria no es para clientes que no llegan a una conclusion, esta categoria es unicamente para llamadas en las que el cliente no habla absolutamente nada.
4. **baja** - Cliente solicita EXPLÍCITAMENTE darse de baja del servicio
5. **llamada_agendada** - Se agenda una cita, llamada o follow-up con el cliente
6. **respuesta_ia** - Contestó un CONTESTADOR AUTOMÁTICO, buzón de voz, asistente virtual o sistema de respuesta automática

IMPORTANTE:
- Si responde un contestador/buzón de voz/máquina → usa \"respuesta_ia\"
- Si cliente dice \"darme de baja\", no renovar o similar → usa \"baja\"
- Si se agenda una cita/llamada para después → usa \"llamada_agendada\"
- Si el cliente responde algo, aunque sea negativo → usa \"descontento\" si rechaza o \"contento\" si acepta
- Si solo pide info sin agendar → usa \"contento\" si acepta o \"descontento\" si rechaza

TRANSCRIPCIÓN:
{$transcript}

Responde ÚNICAMENTE con el objeto JSON (sin bloques de código markdown):
{
    \"category\": \"una_de_las_5_opciones\",
    \"confidence\": 0.85
}";

        $response = $this->sendChatRequest($prompt);
        if (!$response) {
            return null;
        }

        return $this->parseCategorizationResponse($response);
    }

    /**
     * Categorizar con categoría específica del agente
     */
    public function categorizeSpecific(string $transcript, ?string $agentId = null, ?string $campaignMessage = null): ?array
    {
        if (empty($transcript)) {
            return null;
        }

        // Obtener categorías permitidas del agente
        $allowedCategories = $this->getAllowedCategories($agentId);
        $categoriesList = $this->getCategoriesListForPrompt($agentId);

        Log::info('📋 Categorías permitidas para este agente', [
            'agent_id' => $agentId,
            'categorias' => $allowedCategories,
        ]);

        $prompt = config('elevenlabs.prompts.categorization');
        $prompt = str_replace('{campaign_message_section}', $this->buildCampaignMessageSection($campaignMessage), $prompt);
        $prompt = str_replace('{categories_list}', $categoriesList, $prompt);
        $prompt = str_replace('{transcript}', $transcript, $prompt);

        $response = $this->sendChatRequest($prompt);

        if (!$response) {
            return null;
        }

        $result = $this->parseCategorizationResponse($response);

        Log::info('🔍 Resultado parseado de IA', [
            'category' => $result['category'] ?? 'null',
            'confidence' => $result['confidence'] ?? 'null',
        ]);

        // VALIDAR que la categoría esté en la lista permitida
        if ($result && isset($result['category'])) {
            // Rechazar automáticamente si es una categoría de sentimiento (fijas)
            if (in_array($result['category'], ['sin_respuesta', 'baja', 'llamada_agendada', 'respuesta_ia'])) {
                Log::error('❌ IA devolvió categoría de uso reservado', [
                    'categoria_devuelta' => $result['category'],
                    'categorias_permitidas' => $allowedCategories,
                ]);

                if (count($allowedCategories) > 0) {
                    $fallback = $allowedCategories[0];
                    Log::warning('⚠️ Usando categoría fallback', [
                        'fallback' => $fallback,
                    ]);

                    $result['category'] = $fallback;
                    $result['confidence'] = 0.4; // Baja confianza
                } else {
                    return null;
                }
            }

            $isValid = in_array($result['category'], $allowedCategories);

            Log::info('✅ Validando categoría', [
                'categoria_recibida' => $result['category'],
                'es_valida' => $isValid ? 'SÍ' : 'NO',
                'categorias_permitidas' => $allowedCategories,
            ]);

            if (!$isValid) {
                Log::warning('⚠️ IA devolvió categoría NO PERMITIDA', [
                    'categoria_devuelta' => $result['category'],
                    'categorias_permitidas' => $allowedCategories,
                    'agent_id' => $agentId,
                ]);

                // Intentar mapear a una categoría válida
                $mapped = $this->mapToValidCategory($result['category'], $allowedCategories);
                if ($mapped) {
                    Log::info('🔄 Categoría mapeada exitosamente', [
                        'original' => $result['category'],
                        'mapeada' => $mapped,
                    ]);
                    $result['category'] = $mapped;
                    $result['confidence'] = max(0.5, ($result['confidence'] ?? 0.7) - 0.15);
                } else {
                    Log::error('❌ No se pudo mapear categoría inválida, usando primera categoría como fallback');
                    if (count($allowedCategories) > 0) {
                        $result['category'] = $allowedCategories[0];
                        $result['confidence'] = 0.4;
                    } else {
                        return null;
                    }
                }
            } else {
                Log::info('✅ Categoría válida, se usará tal cual');
            }
        }

        Log::info('📤 Retornando categoría final', [
            'category' => $result['category'] ?? 'null',
            'confidence' => $result['confidence'] ?? 'null',
        ]);

        return $result;
    }

    protected function buildCampaignMessageSection(?string $campaignMessage): string
    {
        if (!$campaignMessage || trim($campaignMessage) === '') {
            return "## CONTEXTO DE LA CAMPAÑA\nNo se proporcionó un mensaje inicial específico para esta campaña.\n";
        }

        $cleanedMessage = trim($campaignMessage);

        return "## CONTEXTO DE LA CAMPAÑA\nEste fue el mensaje inicial enviado automáticamente al cliente antes de su respuesta:\n\"{$cleanedMessage}\"\n\nConsidera este contexto para entender la intención original de la llamada.\n";
    }

    /**
     * Obtener categorías permitidas para un agente (solo específicas, sin sentimiento)
     */
    protected function getAllowedCategories(?string $agentId): array
    {
        if ($agentId) {
            $agent = \App\Models\ElevenlabsAgent::findByAgentId($agentId);
            if ($agent) {
                $categories = $agent->getCategories();
                // Filtrar solo las categorías NO default (las específicas del agente)
                $specificCategories = array_filter($categories, fn($cat) => !$cat['is_default']);
                return array_column($specificCategories, 'category_key');
            }
        }

        // Categorías por defecto (sin las de sentimiento)
        return ['interesado', 'no_interesado', 'quiere_mas_informacion', 'seguimiento_programado', 'necesita_asistencia', 'queja'];
    }

    /**
     * Mapear categoría inválida a una válida
     */
    protected function mapToValidCategory(string $invalidCategory, array $allowedCategories): ?string
    {
        // Primero verificar si ya está en las permitidas (por si acaso)
        if (in_array($invalidCategory, $allowedCategories)) {
            return $invalidCategory;
        }

        // Mapeos de categorías genéricas a específicas
        $mappings = [
            'pregunta' => ['quiere_mas_informacion', 'solicita_informacion'],
            'consulta' => ['quiere_mas_informacion', 'solicita_informacion'],
            'solicita_info' => ['quiere_mas_informacion', 'solicita_informacion'],
            'reserva' => ['seguimiento_programado'],
            'seguimiento' => ['seguimiento_programado'],
            'mantenimiento' => ['necesita_asistencia'],
            'problema' => ['necesita_asistencia', 'queja'],
            'incidencia' => ['queja'],
            'acepta' => ['interesado'],
            'contrata' => ['interesado'],
            'comprar' => ['interesado'],
            'me interesa' => ['interesado'],
            'interesado' => ['interesado'],
            'me encaja' => ['interesado'],
            'adelante' => ['interesado'],
            'si me interesa' => ['interesado'],
            'quiero' => ['interesado'],
            'enviame' => ['quiere_mas_informacion', 'seguimiento_programado'],
            'envia' => ['quiere_mas_informacion', 'seguimiento_programado'],
            'presupuesto' => ['seguimiento_programado'],
            'desinteres' => ['no_interesado'],
            'no interesado' => ['no_interesado'],
            'no me interesa' => ['no_interesado'],
            'rechaza' => ['no_interesado'],
            'cancelar' => ['no_interesado'],
            'llama otro dia' => ['no_interesado'],
            'no ahora' => ['no_interesado'],
            'no quiero' => ['no_interesado'],
            'ya tengo' => ['no_interesado'],
        ];

        // Buscar en los mapeos si hay una categoría válida
        foreach ($mappings as $invalid => $possibleValid) {
            if (stripos($invalidCategory, $invalid) !== false) {
                foreach ($possibleValid as $valid) {
                    if (in_array($valid, $allowedCategories)) {
                        return $valid;
                    }
                }
            }
        }

        // Si no hay mapeo, intentar buscar categoría similar por texto
        foreach ($allowedCategories as $allowed) {
            if (stripos($allowed, $invalidCategory) !== false || stripos($invalidCategory, $allowed) !== false) {
                return $allowed;
            }
        }

        // Si no encuentra nada, loguear y devolver null
        Log::error('❌ No se pudo mapear categoría', [
            'categoria_invalida' => $invalidCategory,
            'categorias_disponibles' => $allowedCategories,
        ]);

        return null;
    }

    /**
     * Obtener lista de categorías formateada para el prompt
     */
    protected function getCategoriesListForPrompt(?string $agentId): string
    {
        if ($agentId) {
            $agent = \App\Models\ElevenlabsAgent::findByAgentId($agentId);
            if ($agent) {
                $categories = $agent->getCategories();

                if (!empty($categories)) {
                    $list = '';
                    foreach ($categories as $index => $cat) {
                        $num = $index + 1;
                        $key = $cat['category_key'];
                        $desc = $cat['category_description'] ?? $cat['category_label'];
                        $list .= "{$num}. {$key} - {$desc}\n";
                    }
                    return $list;
                }
            }
        }

        // Categorías por defecto
        return "1. interesado - Cliente acepta, muestra intención de comprar o pide avanzar (presupuesto, documentación, reunión de cierre).
2. no_interesado - Cliente rechaza, corta la conversación o pospone sin compromiso real.
3. quiere_mas_informacion - Pide datos adicionales concretos para evaluar.
4. seguimiento_programado - Acordaron un seguimiento con fecha/hora o paso claro.
5. necesita_asistencia - Requiere soporte técnico, incidencia o derivación.
6. queja - Reclama, manifiesta un problema o disconformidad seria.";
    }

    /**
     * Extraer información de llamada agendada
     */
    protected function extractScheduledCallInfo(string $transcript, $conversationDate): ?array
    {
        $callDateTime = $conversationDate instanceof \Carbon\Carbon
            ? $conversationDate
            : \Carbon\Carbon::parse($conversationDate);

        $callDateFormatted = $callDateTime->format('d/m/Y H:i:s');
        $dayOfWeek = $callDateTime->locale('es')->dayName;

        $prompt = "Extrae la información de la LLAMADA AGENDADA de esta conversación.

CONTEXTO IMPORTANTE:
Esta llamada se realizó el: {$dayOfWeek}, {$callDateFormatted}
Usa esta fecha como referencia para calcular fechas relativas.

EJEMPLOS DE CÁLCULO DE FECHAS:
- Si dicen \"mañana\" → Calcula desde {$callDateFormatted} + 1 día
- Si dicen \"pasado mañana\" → Calcula desde {$callDateFormatted} + 2 días
- Si dicen \"el lunes\" → Calcula el próximo lunes desde {$callDateFormatted}
- Si dicen \"la semana que viene\" → Calcula 7 días desde {$callDateFormatted}

CONVERSIÓN DE HORAS (FORMATO 24 HORAS - MUY IMPORTANTE):
En España, cuando dicen horas de la tarde/noche, usa FORMATO 24 HORAS:

MAÑANA (8:00 - 12:59):
- \"9 de la mañana\" = 09:00
- \"10 de la mañana\" = 10:00
- \"11 de la mañana\" = 11:00
- \"12 del mediodía\" = 12:00

TARDE (13:00 - 19:59):
- \"1 de la tarde\" = 13:00
- \"2 de la tarde\" = 14:00
- \"3 de la tarde\" = 15:00
- \"4 de la tarde\" = 16:00
- \"5 de la tarde\" = 17:00
- \"6 de la tarde\" = 18:00
- \"7 de la tarde\" = 19:00

NOCHE (20:00 - 23:59):
- \"8 de la noche\" = 20:00
- \"9 de la noche\" = 21:00
- \"10 de la noche\" = 22:00

REFERENCIAS GENERALES:
- \"por la mañana\" sin hora específica = 10:00
- \"al mediodía\" = 12:00
- \"por la tarde\" sin hora específica = 16:00
- \"por la noche\" sin hora específica = 20:00
- \"a la misma hora\" = {$callDateTime->format('H:i')}

TRANSCRIPCIÓN:
{$transcript}

INSTRUCCIONES:
1. Busca la fecha mencionada (exacta o relativa)
2. Busca la hora mencionada (SIEMPRE en formato 24h)
3. Si dicen \"4 de la tarde\", es 16:00 (no 18:00)
4. Si dicen \"5 y media de la tarde\", es 17:30
5. Anota cualquier detalle adicional sobre la cita

FORMATO DE RESPUESTA:
Responde ÚNICAMENTE con JSON (sin markdown, sin código):
{
    \"datetime\": \"2025-10-25 16:00:00\",
    \"notes\": \"Llamar para seguimiento de la oferta\"
}

Si no hay información clara de fecha/hora:
{
    \"datetime\": null,
    \"notes\": \"Llamada agendada sin fecha específica\"
}";

        $response = $this->sendChatRequest($prompt);
        if (!$response) {
            return null;
        }

        try {
            $cleaned = preg_replace('/```json\s*|\s*```/', '', $response);
            $data = json_decode(trim($cleaned), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return [
                    'datetime' => $data['datetime'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ];
            }
        } catch (Exception $e) {
            Log::warning('⚠️ Error extrayendo info de cita', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Resumir conversación
     */
    public function summarizeConversation(string $transcript): ?string
    {
        if (empty($transcript)) {
            return null;
        }

        $prompt = str_replace('{transcript}', $transcript, config('elevenlabs.prompts.summarization'));
        $response = $this->sendChatRequest($prompt);

        if (!$response) {
            return null;
        }

        return trim($response);
    }

    /**
     * Enviar petición a IA local (público para uso externo)
     */
    public function sendChatRequest(string $prompt): ?string
    {
        $attempt = 0;

        while ($attempt < $this->retryAttempts) {
            try {
                $attempt++;

                Log::info("🤖 Petición a IA (intento {$attempt})", [
                    'prompt_length' => strlen($prompt),
                ]);

                $requestData = [
                    'modelo' => $this->aiModel,
                    'prompt' => $prompt,
                ];

                $headers = [
                    'Content-Type' => 'application/json',
                    'X-API-Key' => $this->aiApiKey,
                ];

                Log::info("📤 Enviando petición a IA", [
                    'url' => $this->aiServiceUrl,
                    'modelo' => $this->aiModel,
                    'api_key' => $this->aiApiKey ? substr($this->aiApiKey, 0, 20) . '...' : 'NO CONFIGURADA',
                    'headers' => array_keys($headers),
                    'request_data_keys' => array_keys($requestData),
                ]);

                $response = Http::withHeaders($headers)
                ->withOptions(['verify' => false])
                ->timeout((int) $this->timeout)
                ->post($this->aiServiceUrl, $requestData);

                Log::info("📥 Respuesta de IA", [
                    'status' => $response->status(),
                    'successful' => $response->successful(),
                    'body_preview' => substr($response->body(), 0, 500),
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    Log::debug("📊 Datos JSON recibidos", [
                        'keys' => is_array($data) ? array_keys($data) : 'not_array',
                        'data_type' => gettype($data),
                    ]);

                    // Extraer respuesta según diferentes formatos posibles
                    $result = $data['respuesta'] ?? $data['response'] ?? $data['text'] ?? $data['message'] ?? $data['content'] ?? null;

                    if ($result) {
                        Log::info('✅ Respuesta de IA extraída correctamente', [
                            'length' => strlen($result),
                        ]);
                        return $result;
                    }

                    if (is_string($data)) {
                        Log::info('✅ Respuesta es string directo');
                        return $data;
                    }

                    Log::warning('⚠️ Formato de respuesta no reconocido, devolviendo como JSON', [
                        'data' => $data,
                    ]);
                    return json_encode($data);
                }

                Log::warning("⚠️ Intento {$attempt} falló", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

            } catch (Exception $e) {
                Log::warning("⚠️ Error en intento {$attempt}", [
                    'error' => $e->getMessage(),
                ]);
            }

            if ($attempt < $this->retryAttempts) {
                sleep($this->retryDelay);
            }
        }

        Log::error('❌ No se pudo obtener respuesta de IA');
        return null;
    }

    /**
     * Parsear respuesta de categorización
     */
    protected function parseCategorizationResponse(string $response): ?array
    {
        try {
            // Limpiar markdown code blocks (```json ... ```)
            $cleaned = preg_replace('/```json\s*|\s*```/', '', $response);
            $cleaned = trim($cleaned);

            Log::debug('🧹 Limpiando respuesta', [
                'original_length' => strlen($response),
                'cleaned_length' => strlen($cleaned),
                'cleaned_preview' => substr($cleaned, 0, 200),
            ]);

            // Intentar parsear JSON
            $data = json_decode($cleaned, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($data['category'])) {
                $category = $this->normalizeCategory($data['category']);

                Log::info('✅ JSON parseado correctamente', [
                    'category_raw' => $data['category'],
                    'category_normalized' => $category,
                    'confidence' => $data['confidence'] ?? 0.5,
                ]);

                return [
                    'category' => $category,
                    'confidence' => $data['confidence'] ?? 0.5,
                    'reason' => $data['reason'] ?? null,
                ];
            }

            Log::warning('⚠️ JSON inválido o sin campo category', [
                'json_error' => json_last_error_msg(),
                'has_category' => isset($data['category']),
                'data_keys' => is_array($data) ? array_keys($data) : 'not_array',
            ]);

            // Intentar extraer JSON manualmente si hay error de sintaxis
            // Buscar "category": "valor" o category: "valor" (con o sin comillas en la clave)
            if (preg_match('/["\']?category["\']?\s*:\s*["\']([^"\']+)["\']/', $cleaned, $matches)) {
                $category = $this->normalizeCategory($matches[1]);

                // Buscar confidence: 0.96 o "confidence": 0.96
                $confidence = 0.5;
                if (preg_match('/["\']?confidence["\']?\s*:\s*([0-9.]+)/', $cleaned, $confMatches)) {
                    $confidence = (float) $confMatches[1];
                }

                Log::info('🔧 JSON extraído manualmente con regex', [
                    'category' => $category,
                    'confidence' => $confidence,
                ]);

                return [
                    'category' => $category,
                    'confidence' => $confidence,
                    'reason' => 'Extraído manualmente',
                ];
            }

            Log::error('❌ No se pudo parsear la respuesta de ninguna forma');
            return null;

        } catch (Exception $e) {
            Log::error('❌ Error parseando categorización', [
                'error' => $e->getMessage(),
                'response_preview' => substr($response, 0, 500),
            ]);
            return null;
        }
    }

    /**
     * Normalizar categoría (ya no mapea a categorías fijas, solo limpia)
     */
    protected function normalizeCategory(string $category): string
    {
        // Solo limpiar y devolver tal cual
        return strtolower(trim($category));
    }

}


