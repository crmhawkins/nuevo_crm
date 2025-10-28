<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ElevenLabsBatchCallController extends Controller
{
    private $apiKey;
    private $apiUrl;
    private $aiApiUrl;
    private $aiApiKey;

    public function __construct()
    {
        $this->apiKey = config('elevenlabs.api_key');
        $this->apiUrl = config('elevenlabs.api_url', 'https://api.elevenlabs.io');
        $this->aiApiUrl = config('elevenlabs.ai_service_url', 'https://aiapi.hawkins.es/chat');
        $this->aiApiKey = config('elevenlabs.ai_api_key');
    }

    /**
     * Enviar batch calls a la API de ElevenLabs
     */
    public function submitBatchCall(Request $request)
    {
        try {
            Log::info('=== INICIO submitBatchCall ===');
            Log::info('Datos recibidos:', $request->all());

            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'call_name' => 'required|string|max:255',
                'agent_id' => 'required|string',
                'agent_phone_number_id' => 'required|string',
                'recipients' => 'required|array|min:1',
                'recipients.*.phone_number' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::error('Validación fallida:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Verificar que la API key esté configurada
            if (!$this->apiKey) {
                Log::error('API key de ElevenLabs no configurada');
                return response()->json([
                    'success' => false,
                    'message' => 'API key de ElevenLabs no configurada. Por favor, configure ELEVENLABS_API_KEY en el archivo .env'
                ], 500);
            }

            // Preparar los datos para enviar
            $payload = [
                'call_name' => $request->call_name,
                'agent_id' => $request->agent_id,
                'agent_phone_number_id' => $request->agent_phone_number_id,
                'recipients' => $request->recipients
            ];

            Log::info('Payload preparado:', $payload);

            // Hacer la petición POST a la API de ElevenLabs
            $response = Http::withHeaders([
                'xi-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->timeout(30)->post($this->apiUrl . '/v1/convai/batch-calling/submit', $payload);

            Log::info('Respuesta de ElevenLabs:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Verificar si la petición fue exitosa
            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('Batch call enviado exitosamente:', $responseData);

                return response()->json([
                    'success' => true,
                    'message' => 'Batch call enviado exitosamente a ElevenLabs',
                    'data' => $responseData
                ]);
            } else {
                Log::error('Error en la respuesta de ElevenLabs:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar batch call a ElevenLabs',
                    'error' => $response->body(),
                    'status_code' => $response->status()
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Error en submitBatchCall:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar batch call con múltiples destinatarios desde la vista
     */
    public function submitBatchCallFromView(Request $request)
    {
        try {
            Log::info('=== INICIO submitBatchCallFromView ===');
            Log::info('Datos recibidos desde la vista:', $request->all());

            // Validar datos de entrada (más flexible para la vista)
            $validator = Validator::make($request->all(), [
                'call_name' => 'required|string|max:255',
                'agent_id' => 'required|string',
                'agent_phone_number_id' => 'required|string',
                'phone_numbers' => 'required|array|min:1',
                'phone_numbers.*' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::error('Validación fallida:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Transformar el array de teléfonos al formato requerido por la API
            $recipients = array_map(function($phone) {
                return ['phone_number' => $phone];
            }, $request->phone_numbers);

            // Crear un nuevo request con el formato correcto
            $apiRequest = new Request([
                'call_name' => $request->call_name,
                'agent_id' => $request->agent_id,
                'agent_phone_number_id' => $request->agent_phone_number_id,
                'recipients' => $recipients
            ]);

            // Llamar al método principal de envío
            return $this->submitBatchCall($apiRequest);

        } catch (\Exception $e) {
            Log::error('Error en submitBatchCallFromView:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parsear teléfonos con IA local
     * Maneja casos complejos como:
     * - "956 661 515/ 956 632 764" (múltiples números)
     * - "+34 689 27 42 67" (con espacios)
     * - "956 76 37 86" (sin prefijo)
     * - "647821293" (sin espacios)
     */
    private function parsearTelefonoConIA($telefono)
    {
        try {
            Log::info('Parseando teléfono con IA:', ['telefono' => $telefono]);

            $prompt = "Eres un experto en parseo de números de teléfono españoles. Tu tarea es convertir números de teléfono al formato internacional correcto.

## REGLAS IMPORTANTES:
1. Si hay MÚLTIPLES números separados por /, coma, o cualquier delimitador: devuelve SOLO el PRIMER número válido
2. Formato de salida: +34XXXXXXXXX (sin espacios, sin guiones)
3. Si el número ya tiene +34, úsalo tal cual (quitando espacios)
4. Si NO tiene +34 pero es un número español válido de 9 dígitos, añade +34
5. Los números españoles válidos empiezan con: 6, 7, 8, o 9

## EJEMPLOS:
- '956 661 515/ 956 632 764' → '+34956661515' (solo el primero)
- '+34 689 27 42 67' → '+34689274267'
- '956 76 37 86' → '+34956763786'
- '647821293' → '+34647821293'
- '34 956 661 515' → '+34956661515'

## NÚMERO A PARSEAR:
{$telefono}

## RESPUESTA:
Devuelve ÚNICAMENTE el número en formato +34XXXXXXXXX, sin texto adicional, sin explicaciones.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->aiApiKey,
                'Content-Type' => 'application/json'
            ])->timeout(10)->post($this->aiApiUrl, [
                'message' => $prompt,
                'stream' => false
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $telefonoParsed = trim($data['response'] ?? '');
                
                // Limpiar cualquier texto adicional y extraer solo el número
                $telefonoParsed = preg_replace('/[^0-9+]/', '', $telefonoParsed);
                
                // Validación adicional: debe empezar con +34 y tener 12 caracteres totales
                if (preg_match('/^\+34[6-9]\d{8}$/', $telefonoParsed)) {
                    Log::info('Teléfono parseado correctamente:', [
                        'original' => $telefono,
                        'parseado' => $telefonoParsed
                    ]);
                    return $telefonoParsed;
                }
                
                // Si la IA no devolvió formato válido, intentar parseo manual
                Log::warning('IA devolvió formato inválido, intentando parseo manual:', [
                    'telefono' => $telefono,
                    'respuesta_ia' => $telefonoParsed
                ]);
                
                return $this->parseoManualFallback($telefono);
            }

            Log::warning('Error en respuesta de IA, usando parseo manual:', [
                'telefono' => $telefono,
                'status' => $response->status()
            ]);

            return $this->parseoManualFallback($telefono);

        } catch (\Exception $e) {
            Log::error('Error al parsear teléfono con IA, usando parseo manual:', [
                'telefono' => $telefono,
                'error' => $e->getMessage()
            ]);
            return $this->parseoManualFallback($telefono);
        }
    }

    /**
     * Parseo manual de fallback cuando la IA falla
     */
    private function parseoManualFallback($telefono)
    {
        try {
            // Si hay múltiples números separados, tomar solo el primero
            if (strpos($telefono, '/') !== false) {
                $telefono = explode('/', $telefono)[0];
            } elseif (strpos($telefono, ',') !== false) {
                $telefono = explode(',', $telefono)[0];
            }
            
            // Limpiar espacios, guiones, paréntesis
            $telefonoLimpio = preg_replace('/[^0-9+]/', '', trim($telefono));
            
            // Si ya tiene +34, validar y devolver
            if (strpos($telefonoLimpio, '+34') === 0) {
                if (preg_match('/^\+34[6-9]\d{8}$/', $telefonoLimpio)) {
                    return $telefonoLimpio;
                }
                // Quitar el +34 para procesarlo
                $telefonoLimpio = substr($telefonoLimpio, 3);
            }
            
            // Si empieza con 34 (sin +), quitarlo
            if (strpos($telefonoLimpio, '34') === 0 && strlen($telefonoLimpio) === 11) {
                $telefonoLimpio = substr($telefonoLimpio, 2);
            }
            
            // Validar que sea un número español válido (9 dígitos, empieza con 6,7,8,9)
            if (preg_match('/^[6-9]\d{8}$/', $telefonoLimpio)) {
                return '+34' . $telefonoLimpio;
            }
            
            Log::warning('No se pudo parsear el teléfono:', ['telefono' => $telefono]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error en parseo manual fallback:', [
                'telefono' => $telefono,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Actualizar first_message de un agente
     * Documentación: https://elevenlabs.io/docs/api-reference/agents/update
     */
    private function actualizarFirstMessage($agentId, $firstMessage)
    {
        try {
            Log::info('Actualizando first_message del agente:', [
                'agent_id' => $agentId,
                'first_message' => substr($firstMessage, 0, 100) . '...'
            ]);

            $response = Http::withHeaders([
                'xi-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->timeout(30)->patch($this->apiUrl . '/v1/convai/agents/' . $agentId, [
                'conversation_config' => [
                    'agent' => [
                        'first_message' => $firstMessage
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info('First message actualizado correctamente');
                return true;
            } else {
                Log::error('Error al actualizar first_message:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Excepción al actualizar first_message:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar batch call con clientes filtrados (procesa teléfonos con IA)
     */
    public function submitBatchCallConClientesFiltrados(Request $request)
    {
        try {
            Log::info('=== INICIO submitBatchCallConClientesFiltrados ===');
            Log::info('Datos recibidos:', $request->all());

            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'call_name' => 'required|string|max:255',
                'agent_id' => 'required|string',
                'agent_phone_number_id' => 'required|string',
                'first_message' => 'nullable|string|max:1000',
                'clientes' => 'required|array|min:1',
                'clientes.*.id' => 'required|integer',
                'clientes.*.telefono' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::error('Validación fallida:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Si se proporciona first_message, actualizar el agente primero
            if ($request->filled('first_message')) {
                Log::info('Actualizando first_message del agente antes de enviar batch call');
                $actualizado = $this->actualizarFirstMessage($request->agent_id, $request->first_message);
                
                if (!$actualizado) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al actualizar el mensaje inicial del agente. No se enviará el batch call.',
                        'error' => 'No se pudo actualizar first_message'
                    ], 500);
                }
                
                Log::info('First message actualizado correctamente, procediendo con batch call');
            }

            // Parsear teléfonos con IA
            $telefonosParseados = [];
            $errores = [];

            foreach ($request->clientes as $cliente) {
                try {
                    $telefonoParsed = $this->parsearTelefonoConIA($cliente['telefono']);
                    
                    // Validar que el teléfono parseado tenga el formato correcto
                    if (preg_match('/^\+34[0-9]{9}$/', $telefonoParsed)) {
                        $telefonosParseados[] = [
                            'phone_number' => $telefonoParsed,
                            'cliente_id' => $cliente['id'],
                            'nombre' => $cliente['nombre'] ?? 'N/A'
                        ];
                    } else {
                        $errores[] = [
                            'cliente_id' => $cliente['id'],
                            'telefono_original' => $cliente['telefono'],
                            'motivo' => 'Formato inválido después de parsear'
                        ];
                    }
                } catch (\Exception $e) {
                    $errores[] = [
                        'cliente_id' => $cliente['id'],
                        'telefono_original' => $cliente['telefono'],
                        'motivo' => $e->getMessage()
                    ];
                }
            }

            Log::info('Teléfonos procesados:', [
                'total_originales' => count($request->clientes),
                'total_validos' => count($telefonosParseados),
                'total_errores' => count($errores)
            ]);

            if (empty($telefonosParseados)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo parsear ningún teléfono válido',
                    'errores' => $errores
                ], 400);
            }

            // Preparar recipients solo con phone_number para la API
            $recipients = array_map(function($item) {
                return ['phone_number' => $item['phone_number']];
            }, $telefonosParseados);

            // Preparar los datos para enviar a ElevenLabs
            $payload = [
                'call_name' => $request->call_name,
                'agent_id' => $request->agent_id,
                'agent_phone_number_id' => $request->agent_phone_number_id,
                'recipients' => $recipients
            ];

            Log::info('Payload preparado para ElevenLabs:', [
                'call_name' => $payload['call_name'],
                'total_recipients' => count($recipients)
            ]);

            // Hacer la petición POST a la API de ElevenLabs
            $response = Http::withHeaders([
                'xi-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->timeout(30)->post($this->apiUrl . '/v1/convai/batch-calling/submit', $payload);

            Log::info('Respuesta de ElevenLabs:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'success' => true,
                    'message' => 'Batch call enviado exitosamente a ElevenLabs',
                    'data' => $responseData,
                    'estadisticas' => [
                        'total_clientes' => count($request->clientes),
                        'llamadas_programadas' => count($telefonosParseados),
                        'errores' => count($errores)
                    ],
                    'telefonos_procesados' => $telefonosParseados,
                    'errores' => $errores
                ]);
            } else {
                Log::error('Error en la respuesta de ElevenLabs:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar batch call a ElevenLabs',
                    'error' => $response->body(),
                    'status_code' => $response->status(),
                    'telefonos_procesados' => $telefonosParseados,
                    'errores' => $errores
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Error en submitBatchCallConClientesFiltrados:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista de agentes de ElevenLabs (desde base de datos local)
     */
    public function obtenerAgentes(Request $request)
    {
        try {
            Log::info('=== INICIO obtenerAgentes ===');

            // Agentes excluidos (no mostrar en el select)
            $agentesExcluidos = [
            ];

            // Obtener agentes activos desde la base de datos local
            $agentes = \App\Models\ElevenlabsAgent::active()
                ->whereNotIn('agent_id', $agentesExcluidos)
                ->select('agent_id', 'name')
                ->orderBy('name')
                ->get();

            Log::info('Agentes obtenidos (excluyendo agentes específicos):', [
                'total' => $agentes->count(),
                'excluidos' => count($agentesExcluidos)
            ]);

            return response()->json([
                'success' => true,
                'data' => $agentes
            ]);

        } catch (\Exception $e) {
            Log::error('Error en obtenerAgentes:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los agentes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener phone numbers de un agente desde la API de ElevenLabs
     * Documentación: https://elevenlabs.io/docs/api-reference/phone-numbers/list
     */
    public function obtenerPhoneNumbers(Request $request, $agentId)
    {
        try {
            Log::info('=== INICIO obtenerPhoneNumbers ===', ['agent_id' => $agentId]);

            // Verificar que la API key esté configurada
            if (!$this->apiKey) {
                Log::error('API key de ElevenLabs no configurada');
                return response()->json([
                    'success' => false,
                    'message' => 'API key de ElevenLabs no configurada'
                ], 500);
            }

            // Llamar a la API de ElevenLabs para obtener TODOS los phone numbers
            // Endpoint correcto según documentación: GET /v1/convai/phone-numbers
            $response = Http::withHeaders([
                'xi-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->timeout(30)->get($this->apiUrl . '/v1/convai/phone-numbers');

            Log::info('Respuesta de ElevenLabs al obtener phone numbers:', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500)
            ]);

            if ($response->successful()) {
                $allPhoneNumbers = $response->json();
                
                // Filtrar solo los números asignados al agente solicitado
                $phoneNumbersFiltrados = [];
                
                if (is_array($allPhoneNumbers)) {
                    foreach ($allPhoneNumbers as $phoneData) {
                        // Verificar si está asignado al agente
                        $assignedAgentId = $phoneData['assigned_agent']['agent_id'] ?? null;
                        
                        if ($assignedAgentId === $agentId) {
                            // Formatear para el frontend
                            $phoneNumbersFiltrados[] = [
                                'phone_number_id' => $phoneData['phone_number_id'],
                                'phone_number' => $phoneData['phone_number'],
                                'label' => $phoneData['label'] ?? $phoneData['phone_number'],
                                'provider' => $phoneData['provider'] ?? 'unknown',
                                'supports_inbound' => $phoneData['supports_inbound'] ?? false,
                                'supports_outbound' => $phoneData['supports_outbound'] ?? false
                            ];
                        }
                    }
                }

                Log::info('Phone numbers filtrados para el agente:', [
                    'agent_id' => $agentId,
                    'total' => count($phoneNumbersFiltrados),
                    'phone_numbers' => $phoneNumbersFiltrados
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $phoneNumbersFiltrados,
                    'total' => count($phoneNumbersFiltrados)
                ]);
            } else {
                Log::error('Error en la respuesta de ElevenLabs al obtener phone numbers:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener phone numbers de ElevenLabs',
                    'error' => $response->body(),
                    'status_code' => $response->status()
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Error en obtenerPhoneNumbers:', [
                'agent_id' => $agentId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener phone numbers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

