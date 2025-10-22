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
        'contento' => [
            'label' => 'Contento',
            'color' => '#10B981',
            'icon' => 'fa-smile',
        ],
        'descontento' => [
            'label' => 'Descontento',
            'color' => '#EF4444',
            'icon' => 'fa-frown',
        ],
        'pregunta' => [
            'label' => 'Pregunta',
            'color' => '#3B82F6',
            'icon' => 'fa-question-circle',
        ],
        'necesita_asistencia' => [
            'label' => 'Necesita Asistencia Extra',
            'color' => '#F59E0B',
            'icon' => 'fa-hand-paper',
        ],
        'queja' => [
            'label' => 'Queja',
            'color' => '#DC2626',
            'icon' => 'fa-exclamation-triangle',
        ],
        'baja' => [
            'label' => 'Baja',
            'color' => '#6B7280',
            'icon' => 'fa-user-times',
        ],
        'sin_respuesta' => [
            'label' => 'Sin Respuesta',
            'color' => '#9CA3AF',
            'icon' => 'fa-phone-slash',
        ],
        'respuesta_ia' => [
            'label' => 'Respuesta de IA/Contestador',
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
        'categorization' => 'Eres un analista experto en conversaciones de atención al cliente. Tu tarea es categorizar conversaciones de manera PRECISA usando SOLO las categorías proporcionadas.

## ⚠️ REGLA FUNDAMENTAL - LEE ESTO PRIMERO

Debes elegir ÚNICAMENTE una de las categorías listadas abajo. 

❌ NO puedes usar: contento, descontento, sin_respuesta, baja, llamada_agendada, respuesta_ia
❌ NO puedes usar ninguna categoría que no esté en la lista

## CATEGORÍAS PERMITIDAS (ÚNICAS OPCIONES VÁLIDAS):

{categories_list}

⚠️ ESTAS SON LAS ÚNICAS CATEGORÍAS QUE PUEDES USAR. Si tu respuesta usa otra categoría, será rechazada.

## INSTRUCCIONES DE ANÁLISIS

Lee TODA la conversación y evalúa:
- El TONO del cliente (amable, neutral, molesto, agresivo)
- El RESULTADO de la llamada (resuelto, pendiente, rechazado)
- La INTENCIÓN del cliente (consultar, quejarse, cancelar, etc.)
- La RESPUESTA del cliente (participa, ignora, rechaza)

## INSTRUCCIONES ESPECÍFICAS

⚠️ NO PIENSES EN SENTIMIENTOS (contento/descontento/sin_respuesta). Eso YA fue analizado en otra fase.

ENFÓCATE SOLO EN: ¿Qué está HACIENDO o PIDIENDO el cliente?

**Ejemplos de análisis correcto:**
- Cliente pide información sobre precios → Usa categoría de "consulta" o "informacion" SI EXISTE
- Cliente quiere hacer una reserva → Usa categoría de "reserva" o "solicitud" SI EXISTE  
- Cliente reporta un problema → Usa categoría de "incidencia" o "problema" SI EXISTE
- Cliente solicita darse de baja → Usa categoría de "baja" SI EXISTE
- Cliente muestra interés en oferta → Usa categoría de "interesado" SI EXISTE

Lee la DESCRIPCIÓN detallada de cada categoría arriba y elige la que MEJOR describe la ACCIÓN principal del cliente.

## TRANSCRIPCIÓN A ANALIZAR

{transcript}

## REGLAS DE DECISIÓN PRIORITARIAS

1. Si hay "..." constantes y cliente NO responde → Busca "sin_respuesta" en la lista
2. Si cliente dice "darme de baja" → Busca "baja" en la lista
3. Si cliente muy molesto/agresivo → Busca "queja" en la lista si existe
4. Si cliente rechaza o no está contento → Busca "descontento" en la lista
5. Si cliente acepta/agradece → Busca "contento" en la lista
6. Para OTROS casos → Usa las categorías PERSONALIZADAS del agente según su descripción

## ⚠️ VALIDACIÓN FINAL

Antes de responder, verifica que la categoría que elegiste:
✅ Está en la lista de CATEGORÍAS PERMITIDAS (arriba)
✅ Es el nombre exacto de la clave (key)
❌ NO uses categorías que no están en la lista

## FORMATO DE RESPUESTA

Analiza cuidadosamente y responde ÚNICAMENTE con el objeto JSON (SIN bloques de código markdown, SIN texto adicional):

{
    "category": "nombre_exacto_de_categoria",
    "confidence": 0.85,
    "reason": "Razón clara y específica"
}

**IMPORTANTE:**
- NO uses ```json ni ``` en tu respuesta
- `category`: DEBE ser exactamente uno de los nombres de las categorías disponibles
- `confidence`: Número entre 0.5 y 1.0 (usa 0.95+ si estás muy seguro)
- `reason`: Explica QUÉ señales te llevaron a esta categoría

RESPONDE SOLO EL JSON:',

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

