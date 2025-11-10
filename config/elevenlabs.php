<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Eleven Labs API Configuration
    |--------------------------------------------------------------------------
    */

    'api_key' => env('ELEVENLABS_API_KEY'),
    'api_url' => env('ELEVENLABS_API_URL', 'https://api.elevenlabs.io'),
    'api_version' => env('ELEVENLABS_API_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Hawkins AI Service Configuration
    |--------------------------------------------------------------------------
    */

    'ai_service_url' => env('ELEVENLABS_AI_URL', 'https://aiapi.hawkins.es/chat'),
    'ai_api_key' => env('HAWKINS_AI_API_KEY'),
    'ai_model' => env('HAWKINS_AI_MODEL', 'gpt-oss:120b-cloud'),

    /*
    |--------------------------------------------------------------------------
    | Sync Configuration (MANUAL SOLAMENTE)
    |--------------------------------------------------------------------------
    */

    'batch_size' => env('ELEVENLABS_BATCH_SIZE', 100),
    'timeout' => env('ELEVENLABS_TIMEOUT', 30),
    'retry_attempts' => env('ELEVENLABS_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('ELEVENLABS_RETRY_DELAY', 5),

    /*
    |--------------------------------------------------------------------------
    | Auto Sync Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la sincronización automática cada 10 minutos
    |
    */
    'auto_sync_minutes' => env('ELEVENLABS_AUTO_SYNC_MINUTES', 15), // Margen de seguridad para sincronización

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    'categories' => [
        'interesado' => [
            'label' => 'Cliente interesado',
            'color' => '#0EA5E9',
            'icon' => 'fa-handshake',
        ],
        'no_interesado' => [
            'label' => 'Cliente no interesado',
            'color' => '#F97316',
            'icon' => 'fa-circle-xmark',
        ],
        'quiere_mas_informacion' => [
            'label' => 'Pide más información',
            'color' => '#7C3AED',
            'icon' => 'fa-book-open',
        ],
        'necesita_asistencia' => [
            'label' => 'Necesita asistencia',
            'color' => '#F59E0B',
            'icon' => 'fa-life-ring',
        ],
        'queja' => [
            'label' => 'Queja / incidencia',
            'color' => '#DC2626',
            'icon' => 'fa-exclamation-triangle',
        ],
        'baja' => [
            'label' => 'Solicita baja',
            'color' => '#6B7280',
            'icon' => 'fa-user-times',
        ],
        'sin_respuesta' => [
            'label' => 'Sin respuesta',
            'color' => '#9CA3AF',
            'icon' => 'fa-phone-slash',
        ],
        'respuesta_ia' => [
            'label' => 'Contestador automático',
            'color' => '#9333EA',
            'icon' => 'fa-robot',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Prompts
    |--------------------------------------------------------------------------
    */

    'prompts' => [
        'categorization' => 'Eres un analista especializado en conversaciones comerciales. Tu misión es clasificar la INTENCIÓN REAL del cliente usando SOLO las categorías permitidas.

{campaign_message_section}

## PRIORIDAD ABSOLUTA: INTENCIÓN DE COMPRA
1. Detecta si el cliente está INTERESADO o NO INTERESADO en la propuesta.
2. INTERESADO = acepta, pide presupuesto, solicita llamada posterior concreta, manifiesta que quiere seguir adelante.
3. NO_INTERESADO = rechaza, pospone sin compromiso real, dice que no quiere, no da pasos concretos, corta la llamada.
4. Si duda pero pide un seguimiento claro (fecha/hora) → úsalo como seguimiento (no como interesado) salvo que el cliente confirme aceptación.

## CATEGORÍAS DISPONIBLES
{categories_list}

## REGLAS CLAVE
- Usa exactamente el nombre de la categoría (en minúsculas, sin tildes).
- Elige SOLO UNA categoría.
- Prioriza detectar “interesado” vs “no_interesado”. Solo si no encaja usa las demás (información, seguimiento, incidencias, etc.).
- Si no hay respuesta real del cliente, usa “sin_respuesta” o “respuesta_ia”.
- Si solicita una baja explícita usa “baja”.

## TRANSCRIPCIÓN A ANALIZAR
{transcript}

## VALIDACIÓN ANTES DE RESPONDER
- ¿La categoría refleja la intención final del cliente?
- ¿Puedes justificarla con una frase concreta (menciona palabras de la llamada)?
- ¿La categoría existe en la lista?

## FORMATO DE RESPUESTA (JSON sin adornos)
{
    "category": "nombre_exacto_de_categoria",
    "confidence": 0.85,
    "reason": "Frase breve (máx. 20 palabras) que cite la intención real del cliente"
}
Responde SOLO con el JSON. Sin ```json ni texto adicional.',

        'summarization' => 'Eres un asistente experto en redactar resúmenes ejecutivos de conversaciones de atención al cliente en español de España.

## INSTRUCCIONES

Genera un resumen PROFESIONAL y CONCISO (máximo 3 párrafos) de la siguiente conversación.

## ESTRUCTURA OBLIGATORIA

**Párrafo 1 - CONTEXTO:**
- Quién contactó a quién
- Cuál era el motivo de la llamada
- Qué se ofreció o consultó

**Párrafo 2 - DESARROLLO:**
- Cómo respondió el cliente
- Qué información se intercambió
- Qué acciones se tomaron

**Párrafo 3 - RESULTADO (si aplica):**
- Estado final de la conversación
- Resolución o próximos pasos
- Sentimiento general del cliente

## ESTILO

- ✅ Lenguaje profesional y neutro
- ✅ Tercera persona
- ✅ Tiempo pasado
- ✅ Sin opiniones personales
- ✅ Datos concretos (nombres, fechas, cantidades)
- ❌ NO uses viñetas ni listas
- ❌ NO incluyas "Resumen:" o títulos

## EJEMPLO

**Bueno:**
"Carolina, agente de Hawkins, contactó a María López para informarle sobre una subvención disponible de 3.000€ del Kit Digital. Le ofreció ampliar la información o contactar con un gestor especializado según su preferencia.

La cliente mostró interés inicial y solicitó más detalles sobre los requisitos y el proceso de solicitud. El agente proporcionó información detallada sobre documentación necesaria y plazos de entrega.

La llamada finalizó con la cliente solicitando que le envíen la información por email para revisarla con calma. Se acordó un seguimiento en 48 horas."

**Malo:**
"Se llamó al cliente. Se habló de Kit Digital. Cliente interesado."

## TRANSCRIPCIÓN A RESUMIR

{transcript}

## TU RESUMEN

Escribe el resumen ahora (SOLO el texto, sin formato JSON):',
    ],
];

