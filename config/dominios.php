<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Notificaciones de Dominios
    |--------------------------------------------------------------------------
    |
    | Configuración para el sistema de notificaciones automáticas de
    | dominios próximos a caducar.
    |
    */

    'notificaciones' => [
        // Días antes de caducar para enviar notificación
        'dias_antes_notificar' => env('DOMINIOS_DIAS_NOTIFICACION', 30),
        
        // Días de validez del token de verificación
        'dias_validez_token' => env('DOMINIOS_DIAS_VALIDEZ_TOKEN', 30),
        
        // Días mínimos entre notificaciones (para evitar spam)
        'dias_minimos_entre_notificaciones' => env('DOMINIOS_DIAS_MINIMOS_NOTIFICACIONES', 7),
        
        // Hora del día para enviar notificaciones (formato 24h)
        'hora_envio' => env('DOMINIOS_HORA_ENVIO', '09:00'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Métodos de Pago
    |--------------------------------------------------------------------------
    */

    'pagos' => [
        // Validar formato IBAN
        'validar_iban' => env('DOMINIOS_VALIDAR_IBAN', true),
        
        // Permitir múltiples métodos de pago
        'permitir_multiples_metodos' => env('DOMINIOS_PERMITIR_MULTIPLES_METODOS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Stripe
    |--------------------------------------------------------------------------
    */

    'stripe' => [
        // Crear suscripción automática al configurar método de pago
        'crear_suscripcion_automatica' => env('DOMINIOS_STRIPE_SUSCRIPCION_AUTOMATICA', false),
        
        // Plan de Stripe para renovación de dominios
        'plan_id' => env('DOMINIOS_STRIPE_PLAN_ID', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mensajes y Plantillas
    |--------------------------------------------------------------------------
    */

    'mensajes' => [
        'email' => [
            'asunto' => 'Acción requerida: Su dominio :dominio está próximo a caducar',
            'remitente' => [
                'email' => env('DOMINIOS_EMAIL_REMITENTE', 'dominios@crmhawkins.com'),
                'nombre' => env('DOMINIOS_EMAIL_NOMBRE', 'Los Creativos de Hawkins'),
            ],
        ],
        
        'whatsapp' => [
            'plantilla' => 'dominio_caducidad',
        ],
    ],
];
