<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoseoTestController extends Controller
{
    /**
     * Prueba autenticación básica de WordPress
     */
    public function testWordPressAuth(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $url = rtrim($request->url, '/');
        
        try {
            // Intentar conectar al endpoint de usuarios (requiere autenticación)
            // En desarrollo local, deshabilitar verificación SSL si falla
            $httpClient = Http::timeout(10)
                ->withBasicAuth($request->username, $request->password);
            
            // Solo deshabilitar verificación SSL en desarrollo
            if (config('app.env') !== 'production') {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->get($url . '/wp-json/wp/v2/users/me');

            if ($response->successful()) {
                $user = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'Autenticación exitosa - Usuario: ' . ($user['name'] ?? 'Desconocido')
                ]);
            } elseif ($response->status() === 401) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas (401 Unauthorized)'
                ]);
            } elseif ($response->status() === 403) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado (403 Forbidden)'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error HTTP ' . $response->status()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error testWordPressAuth: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Prueba un endpoint específico de WordPress
     */
    public function testEndpoint(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'endpoint' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'method' => 'nullable|string|in:GET,POST,OPTIONS'
        ]);

        $url = rtrim($request->url, '/') . $request->endpoint;
        $method = $request->method ?? 'GET';
        
        try {
            $httpRequest = Http::timeout(10)
                ->withBasicAuth($request->username, $request->password);
            
            // Solo deshabilitar verificación SSL en desarrollo
            if (config('app.env') !== 'production') {
                $httpRequest = $httpRequest->withoutVerifying();
            }

            if ($method === 'OPTIONS') {
                // Para OPTIONS, solo verificar si el endpoint existe con HEAD
                $response = $httpRequest->head($url);
            } elseif ($method === 'GET') {
                $response = $httpRequest->get($url);
            } elseif ($method === 'POST') {
                $response = $httpRequest->post($url, []);
            }

            if ($response->successful()) {
                $data = null;
                try {
                    $data = $response->json();
                } catch (\Exception $e) {
                    // No es JSON, pero la respuesta fue exitosa
                }

                // Mensajes específicos según el endpoint
                if (str_contains($request->endpoint, '/shortcodes')) {
                    if (is_array($data)) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Endpoint funcional - ' . count($data) . ' shortcodes encontrados'
                        ]);
                    } else {
                        return response()->json([
                            'success' => true,
                            'message' => 'Endpoint responde correctamente'
                        ]);
                    }
                } elseif (str_contains($request->endpoint, '/media')) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Endpoint de medios accesible'
                    ]);
                } elseif (str_contains($request->endpoint, '/posts')) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Endpoint de posts accesible'
                    ]);
                } elseif (str_contains($request->endpoint, '/update-power-shortcode')) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Endpoint de actualización funcional'
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Endpoint responde correctamente (HTTP ' . $response->status() . ')'
                    ]);
                }
            } elseif ($response->status() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint no encontrado (404) - Verifica que el plugin/código esté instalado'
                ]);
            } elseif ($response->status() === 401) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas (401)'
                ]);
            } elseif ($response->status() === 403) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado (403) - Verifica permisos del usuario'
                ]);
            } elseif ($response->status() === 405) {
                // Método no permitido, pero el endpoint existe
                return response()->json([
                    'success' => true,
                    'message' => 'Endpoint existe (método de verificación no soportado)'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error HTTP ' . $response->status()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error testEndpoint: ' . $e->getMessage(), [
                'url' => $url,
                'method' => $method
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ]);
        }
    }
}

