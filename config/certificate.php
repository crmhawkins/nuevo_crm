<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Certificate Authentication Server URL
    |--------------------------------------------------------------------------
    |
    | URL del servidor central de autenticación con certificados (HawCert)
    |
    */

    'auth_server_url' => env('CERT_AUTH_SERVER_URL', 'https://hawcert.hawkins.es'),

    /*
    |--------------------------------------------------------------------------
    | Verify SSL Certificate
    |--------------------------------------------------------------------------
    |
    | En desarrollo local, puedes deshabilitar la verificación SSL si tienes
    | problemas con certificados. En producción siempre debe estar en true.
    |
    */

    'verify_ssl' => env('CERT_VERIFY_SSL', true),

    /*
    |--------------------------------------------------------------------------
    | Service Slug
    |--------------------------------------------------------------------------
    |
    | Identificador del servicio en HawCert. Si no se proporciona, se intentará
    | inferir de la URL. Para desarrollo local, puedes especificar un slug aquí.
    |
    */

    'service_slug' => env('CERT_SERVICE_SLUG', null),

];
