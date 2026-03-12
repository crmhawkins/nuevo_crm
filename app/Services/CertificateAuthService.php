<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Client\ConnectionException;

class CertificateAuthService
{
    protected $authServerUrl;

    public function __construct()
    {
        $this->authServerUrl = config('certificate.auth_server_url', 'https://hawcert.hawkins.es');
    }

    /**
     * Valida un certificado X.509 y obtiene una access key
     *
     * @param string $certificatePEM Certificado en formato PEM
     * @param string $targetUrl URL destino para la cual se solicita acceso
     * @return array|null Datos del usuario y access key, o null si falla
     */
    public function validateCertificateAndGetAccessKey(string $certificatePEM, string $targetUrl): ?array
    {
        try {
            Log::info('🔐 Validando certificado con servidor central', [
                'auth_server' => $this->authServerUrl,
                'target_url' => $targetUrl
            ]);

            // Configurar verificación SSL según configuración
            // En desarrollo (local, testing) o si está explícitamente configurado, deshabilitar SSL
            $environment = app()->environment();
            $isDevelopment = in_array($environment, ['local', 'testing', 'test']) || config('app.debug', false);
            $verifySslConfig = config('certificate.verify_ssl');

            // Si no está configurado explícitamente, deshabilitar SSL en desarrollo
            // Si está configurado explícitamente, respetar esa configuración
            if ($verifySslConfig === null) {
                $verifySsl = !$isDevelopment; // false en desarrollo, true en producción
            } else {
                $verifySsl = (bool) $verifySslConfig;
            }

            Log::info('🔧 Configuración SSL', [
                'verify_ssl' => $verifySsl,
                'verify_ssl_config' => $verifySslConfig,
                'environment' => $environment,
                'is_development' => $isDevelopment,
                'app_debug' => config('app.debug', false),
                'auth_server' => $this->authServerUrl
            ]);

            // Construir el cliente HTTP con la configuración SSL apropiada
            $httpClient = Http::timeout(10);

            if (!$verifySsl) {
                $httpClient = $httpClient->withoutVerifying();
                Log::warning('⚠️ Verificación SSL deshabilitada (solo para desarrollo)', [
                    'auth_server' => $this->authServerUrl,
                    'environment' => $environment,
                    'reason' => $isDevelopment ? 'entorno de desarrollo' : 'configuración explícita'
                ]);
            }

            $response = $httpClient->post("{$this->authServerUrl}/api/validate-access", [
                'certificate' => $certificatePEM,
                'url' => $targetUrl,
            ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Error desconocido';

                Log::warning('❌ Error al validar certificado', [
                    'status' => $response->status(),
                    'error' => $errorMessage
                ]);

                // Retornar el mensaje de error para mostrarlo al usuario
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                $errorMessage = $data['message'] ?? 'Validación fallida';
                Log::warning('❌ Validación de certificado fallida', [
                    'message' => $errorMessage
                ]);
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }

            Log::info('✅ Certificado validado exitosamente', [
                'user_id' => $data['user']['id'] ?? null,
                'user_email' => $data['user']['email'] ?? null,
                'access_key' => substr($data['access_key'] ?? '', 0, 20) . '...'
            ]);

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Error de conexión (SSL, timeout, etc.)
            return $this->handleSslError($e);

        } catch (Exception $e) {
            // Verificar si es un error SSL incluso si no es ConnectionException
            $errorMessage = $e->getMessage();
            if (stripos($errorMessage, 'SSL') !== false ||
                stripos($errorMessage, 'certificate') !== false ||
                stripos($errorMessage, 'curl error 60') !== false) {
                return $this->handleSslError($e);
            }

            Log::error('❌ Excepción al validar certificado', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error al procesar el certificado: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Maneja errores SSL de manera centralizada
     */
    protected function handleSslError(Exception $e): array
    {
        $environment = app()->environment();
        $isDevelopment = in_array($environment, ['local', 'testing', 'test']) || config('app.debug', false);
        $verifySslConfig = config('certificate.verify_ssl');

        $errorMessage = 'Error de conexión con el servidor de certificados. ';

        if (stripos($e->getMessage(), 'SSL') !== false ||
            stripos($e->getMessage(), 'certificate') !== false ||
            stripos($e->getMessage(), 'curl error 60') !== false) {

            if ($isDevelopment) {
                $errorMessage .= 'Error de certificado SSL detectado. ';
                $errorMessage .= 'La verificación SSL debería estar deshabilitada automáticamente en desarrollo. ';
                $errorMessage .= 'Si el problema persiste, agrega CERT_VERIFY_SSL=false en tu archivo .env y ejecuta: php artisan config:clear';
            } else {
                $errorMessage .= 'Error de certificado SSL. Contacta al administrador del sistema.';
            }
        } else {
            $errorMessage .= 'Verifica tu conexión a internet o contacta al administrador.';
        }

        Log::error('❌ Error SSL al validar certificado', [
            'message' => $e->getMessage(),
            'type' => get_class($e),
            'environment' => $environment,
            'verify_ssl_config' => $verifySslConfig,
            'is_development' => $isDevelopment,
            'app_debug' => config('app.debug', false)
        ]);

        return [
            'success' => false,
            'message' => $errorMessage
        ];
    }

    /**
     * Valida una access key con el servidor central
     *
     * @param string $accessKey La access key a validar
     * @param string $targetUrl URL destino
     * @return array|null Datos de validación o null si falla
     */
    public function validateAccessKey(string $accessKey, string $targetUrl): ?array
    {
        try {
            $httpClient = Http::timeout(10);

            // Configurar verificación SSL según configuración
            $verifySsl = config('certificate.verify_ssl', true);
            if (!$verifySsl) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->post("{$this->authServerUrl}/api/validate-key", [
                'key' => $accessKey,
                'url' => $targetUrl,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success'] || !$data['valid']) {
                return null;
            }

            return $data;

        } catch (Exception $e) {
            Log::error('❌ Excepción al validar access key', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Busca un usuario por email en la base de datos local
     *
     * @param string $email Email del usuario
     * @return \App\Models\Users\User|null
     */
    public function findUserByEmail(string $email)
    {
        return \App\Models\Users\User::where('email', $email)->first();
    }
}
