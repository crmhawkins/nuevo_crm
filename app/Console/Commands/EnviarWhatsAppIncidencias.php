<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsAgent;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\Log;

class EnviarWhatsAppIncidencias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:enviar-incidencias';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica y envía WhatsApp de incidencias pendientes de Maria Apartamentos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando incidencias pendientes...');

        try {
            // Categorías de incidencias
            $incidenciaCategories = [
                'incidencia_general',
                'incidencia_limpieza',
                'incidencia_mantenimiento',
                'incidencia_de_limpieza',
                'incidencia_de_mantenimiento',
            ];

            // Buscar conversaciones de incidencias sin WhatsApp enviado
            $incidenciasPendientes = ElevenlabsConversation::whereIn('specific_category', $incidenciaCategories)
                ->where('whatsapp_incidencia_enviado', false)
                ->whereNotNull('summary_es')
                ->orderBy('conversation_date', 'desc')
                ->get();

            if ($incidenciasPendientes->isEmpty()) {
                $this->info('✅ No hay incidencias pendientes.');
                return 0;
            }

            $this->info("📋 Encontradas {$incidenciasPendientes->count()} incidencias pendientes");

            $enviados = 0;
            $errores = 0;

            foreach ($incidenciasPendientes as $conversation) {
                // Verificar que el agente sea Maria Apartamentos
                $agent = ElevenlabsAgent::findByAgentId($conversation->agent_id);

                if (!$agent || stripos($agent->name, 'Maria Apartamentos') === false) {
                    $this->warn("⏭️  Conversación {$conversation->conversation_id} no es de Maria Apartamentos");
                    continue;
                }

                // Intentar enviar WhatsApp
                if ($this->enviarWhatsAppIncidencia($conversation, $agent)) {
                    $enviados++;
                    $this->info("✅ WhatsApp enviado para conversación {$conversation->conversation_id}");
                } else {
                    $errores++;
                    $this->error("❌ Error enviando WhatsApp para conversación {$conversation->conversation_id}");
                }
            }

            $this->info("📊 Resumen: {$enviados} enviados, {$errores} errores");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error general: {$e->getMessage()}");
            Log::error('Error en comando EnviarWhatsAppIncidencias', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Enviar WhatsApp de incidencia usando el template
     */
    protected function enviarWhatsAppIncidencia(ElevenlabsConversation $conversation, ElevenlabsAgent $agent): bool
    {
        try {
            // Mapeo de categorías a texto legible
            $categoryLabels = [
                'incidencia_general' => 'Incidencia General',
                'incidencia_limpieza' => 'Incidencia de Limpieza',
                'incidencia_mantenimiento' => 'Incidencia de Mantenimiento',
                'incidencia_de_limpieza' => 'Incidencia de Limpieza',
                'incidencia_de_mantenimiento' => 'Incidencia de Mantenimiento',
            ];

            $tipoIncidencia = $categoryLabels[$conversation->specific_category] ?? $conversation->specific_category;

            // Normalizar texto para cumplir con las restricciones de WhatsApp
            $tipoIncidencia = $this->sanitizeWhatsappText($tipoIncidencia, 'Incidencia General');

            // Obtener resumen (limitar a 500 caracteres para el template)
            $resumen = $conversation->summary_es ?? 'Sin resumen disponible';
            $resumen = $this->sanitizeWhatsappText($resumen, 'Sin resumen disponible');
            if (strlen($resumen) > 500) {
                $resumen = substr($resumen, 0, 497) . '...';
            }

            // Obtener número del cliente
            $numeroCliente = $conversation->numero ?? 'No disponible';

            // Si no hay número, intentar obtenerlo del cliente asociado
            if ($numeroCliente === 'No disponible' && $conversation->client_id) {
                $cliente = Client::find($conversation->client_id);
                if ($cliente && $cliente->phone) {
                    $numeroCliente = $cliente->phone;
                }
            }

            $numeroCliente = $this->sanitizeWhatsappText($numeroCliente, 'No disponible');

            $destinatarios = [
                ['nombre' => 'Nico', 'telefono' => '+34634261382'],
                ['nombre' => 'Elena', 'telefono' => '+34664368232'],
                ['nombre' => 'Limpiadora', 'telefono' => '++34633065237'],
            ];

            $token = env('TOKEN_WHATSAPP');
            $phoneNumberId = '102360642838173';
            $urlMensajes = 'https://graph.facebook.com/v18.0/' . $phoneNumberId . '/messages';

            $enviosExitosos = 0;

            foreach ($destinatarios as $destinatario) {
                $phoneDestino = $this->sanitizePhoneNumber($destinatario['telefono']);

                if ($phoneDestino === null) {
                    Log::warning('⚠️ Número inválido para destinatario de incidencias', [
                        'conversation_id' => $conversation->conversation_id,
                        'destinatario' => $destinatario['nombre'],
                        'telefono_original' => $destinatario['telefono']
                    ]);
                    continue;
                }

                $payload = [
                    "messaging_product" => "whatsapp",
                    "recipient_type" => "individual",
                    "to" => $phoneDestino,
                    "type" => "template",
                    "template" => [
                        "name" => "incidencia_apartamentos",
                        "language" => [
                            "code" => "es"
                        ],
                        "components" => [
                            [
                                "type" => "body",
                                "parameters" => [
                                    ["type" => "text", "text" => $tipoIncidencia],
                                    ["type" => "text", "text" => $resumen],
                                    ["type" => "text", "text" => $numeroCliente],
                                ]
                            ]
                        ]
                    ]
                ];

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $urlMensajes,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token
                    ],
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]);

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                $resultado = json_decode($response, true);

                if ($httpCode === 200 && isset($resultado['messages'])) {
                    $enviosExitosos++;

                    Log::info('✅ WhatsApp de incidencia enviado', [
                        'conversation_id' => $conversation->conversation_id,
                        'destinatario' => $destinatario['nombre'],
                        'destino' => $phoneDestino,
                        'tipo_incidencia' => $tipoIncidencia,
                        'numero_cliente' => $numeroCliente,
                        'message_id' => $resultado['messages'][0]['id'] ?? 'N/A'
                    ]);
                } else {
                    Log::error('❌ Error al enviar WhatsApp de incidencia', [
                        'conversation_id' => $conversation->conversation_id,
                        'destinatario' => $destinatario['nombre'],
                        'destino' => $phoneDestino,
                        'http_code' => $httpCode,
                        'response' => $resultado
                    ]);
                }
            }

            if ($enviosExitosos > 0) {
                $conversation->update([
                    'whatsapp_incidencia_enviado' => true,
                    'whatsapp_incidencia_enviado_at' => now()
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Excepción al enviar WhatsApp de incidencia', [
                'conversation_id' => $conversation->conversation_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Normaliza texto para cumplir con restricciones de WhatsApp (sin saltos de línea ni espacios consecutivos)
     */
    protected function sanitizeWhatsappText(?string $text, string $fallback = ''): string
    {
        if ($text === null) {
            $text = '';
        }

        // Reemplazar saltos de línea, tabs y espacios múltiples por un único espacio
        $text = preg_replace('/[\t\r\n]+/', ' ', $text);
        $text = preg_replace('/\s{2,}/', ' ', $text);

        // Eliminar espacios al inicio y final
        $text = trim($text);

        if ($text === '') {
            return $fallback;
        }

        return $text;
    }

    /**
     * Normaliza números de teléfono a formato internacional con un único prefijo '+'
     */
    protected function sanitizePhoneNumber(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $phone = trim($phone);
        $phone = preg_replace('/\s+/', '', $phone);
        $phone = preg_replace('/^\++/', '+', $phone); // Asegura un solo + al principio

        if ($phone === '+') {
            return null;
        }

        if (!str_starts_with($phone, '+')) {
            if (str_starts_with($phone, '34')) {
                $phone = '+' . $phone;
            } elseif (preg_match('/^\d{9}$/', $phone)) {
                $phone = '+34' . $phone;
            }
        }

        if (!preg_match('/^\+\d{6,15}$/', $phone)) {
            return null;
        }

        return $phone;
    }
}
