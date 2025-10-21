<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Eleven Labs API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con la API de Eleven Labs
    |
    */

    'api_key' => env('ELEVENLABS_API_KEY'),
    'api_url' => env('ELEVENLABS_API_URL', 'https://api.elevenlabs.io'),
    'api_version' => env('ELEVENLABS_API_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Hawkins AI Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la IA local de Hawkins
    |
    */

    'ai_service_url' => env('ELEVENLABS_AI_URL', 'https://aiapi.hawkins.es/chat'),
    'ai_api_key' => env('HAWKINS_AI_API_KEY'),
    'ai_model' => env('HAWKINS_AI_MODEL', 'gpt-oss:120b-cloud'),

    /*
    |--------------------------------------------------------------------------
    | Sync Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de sincronización automática
    |
    */

    'sync_interval' => env('ELEVENLABS_SYNC_INTERVAL', 60), // minutos
    'auto_process' => env('ELEVENLABS_AUTO_PROCESS', true),
    'batch_size' => env('ELEVENLABS_BATCH_SIZE', 100), // conversaciones por lote

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    |
    | Categorías disponibles para clasificar conversaciones
    |
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
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para el procesamiento de conversaciones
    |
    */

    'retry_attempts' => env('ELEVENLABS_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('ELEVENLABS_RETRY_DELAY', 5), // segundos
    'timeout' => env('ELEVENLABS_TIMEOUT', 30), // segundos

    /*
    |--------------------------------------------------------------------------
    | AI Prompts
    |--------------------------------------------------------------------------
    |
    | Prompts para la IA
    |
    */

    'prompts' => [
        'categorization' => 'Analiza la siguiente transcripción de una conversación de atención al cliente y categorízala en UNA de las siguientes categorías:

1. Contento - El cliente está satisfecho con el servicio
2. Descontento - El cliente expresa insatisfacción
3. Pregunta - Consulta general o solicitud de información
4. Necesita asistencia extra - Requiere escalado o soporte adicional
5. Queja - Queja formal sobre el servicio o producto
6. Baja - El cliente solicita cancelar o darse de baja del servicio

Transcripción:
{transcript}

Responde ÚNICAMENTE en formato JSON con la siguiente estructura:
{
    "category": "nombre_categoria",
    "confidence": 0.95,
    "reason": "Breve explicación de por qué se eligió esta categoría"
}',

        'summarization' => 'Genera un resumen ejecutivo en español de España (castellano) de la siguiente conversación de atención al cliente.

El resumen debe:
- Ser conciso (máximo 3 párrafos)
- Incluir los puntos clave de la conversación
- Mencionar la resolución o estado final si aplica
- Usar lenguaje profesional

Transcripción:
{transcript}

Responde ÚNICAMENTE con el texto del resumen, sin formato JSON ni información adicional.',
    ],
];

