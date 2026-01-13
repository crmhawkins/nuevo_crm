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
        
        // Días mínimos de período de prueba (trial) - Stripe requiere mínimo 2 días
        'dias_trial_minimo' => env('DOMINIOS_STRIPE_DIAS_TRIAL_MINIMO', 2),
        
        // Usar fecha de caducidad del dominio como trial_end (true) o usar días fijos (false)
        'usar_fecha_caducidad_como_trial' => env('DOMINIOS_STRIPE_USAR_FECHA_CADUCIDAD_TRIAL', true),
        
        // Días de prueba fijos si no se usa la fecha de caducidad
        'dias_trial_fijos' => env('DOMINIOS_STRIPE_DIAS_TRIAL_FIJOS', 0),
        
        // Texto personalizado para el período de prueba (se mostrará en la descripción del producto)
        // Ejemplo: "Período de prueba gratuito" o "30 días de prueba"
        'texto_periodo_prueba' => env('DOMINIOS_STRIPE_TEXTO_PERIODO_PRUEBA', null),
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
