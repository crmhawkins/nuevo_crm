<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Models\Dominios\DominioNotificacion;
use App\Models\Clients\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ListarDominiosPorVencer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dominios:por-vencer 
                            {--dias=30 : Número de días a considerar}
                            {--todos : Mostrar todos los dominios (con y sin método de pago)}
                            {--con-pago : Solo mostrar dominios con método de pago válido}
                            {--cliente= : Filtrar por ID de cliente}
                            {--dominio= : Filtrar por ID de dominio específico}
                            {--enviar-whatsapp : Enviar mensajes de WhatsApp a los clientes con teléfono válido}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista los dominios que van a vencer en los próximos días y NO tienen método de pago válido (IBAN válido o Stripe)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dias = (int) $this->option('dias');
        $todos = $this->option('todos');
        $conPago = $this->option('con-pago');
        $clienteId = $this->option('cliente');
        $dominioId = $this->option('dominio');

        // Si se especifica un dominio, mostrar información específica
        if ($dominioId) {
            $this->info("Buscando dominio ID: {$dominioId}");
        } else {
            $this->info("Buscando dominios que vencen en los próximos {$dias} días...");
        }
        
        if (!$todos && !$conPago) {
            $this->comment("(Solo dominios sin método de pago válido - IBAN válido o Stripe)");
        }
        $this->newLine();

        // Fecha límite
        $fechaLimite = Carbon::now()->addDays($dias);

        // Si se especifica un dominio ID, filtrar directamente por ese ID
        if ($dominioId) {
            $query = Dominio::with(['cliente', 'cliente.phones'])
                ->where('id', $dominioId);
        } else {
            // Construir query base (cargar cliente con teléfonos)
            $query = Dominio::with(['cliente', 'cliente.phones'])
                ->where(function($q) use ($fechaLimite) {
                    // Dominios con fecha_renovacion_ionos
                    $q->whereNotNull('fecha_renovacion_ionos')
                      ->where('fecha_renovacion_ionos', '<=', $fechaLimite)
                      ->where('fecha_renovacion_ionos', '>', Carbon::now());
                })
                ->orWhere(function($q) use ($fechaLimite) {
                    // Dominios con date_end (si no tienen fecha_renovacion_ionos)
                    $q->whereNull('fecha_renovacion_ionos')
                      ->whereNotNull('date_end')
                      ->where('date_end', '<=', $fechaLimite)
                      ->where('date_end', '>', Carbon::now());
                });

            // Filtrar por cliente si se especifica
            if ($clienteId) {
                $query->where('client_id', $clienteId);
            }
        }

        // Por defecto, solo dominios sin método de pago válido
        if (!$todos && !$conPago) {
            $query->where(function($q) {
                // No tiene IBAN válido
                $q->where(function($subQ) {
                    $subQ->whereNull('iban')
                         ->orWhere('iban', '')
                         ->orWhere('iban_validado', false);
                })
                // Y no tiene método de pago Stripe
                ->where(function($subQ) {
                    $subQ->whereNull('stripe_payment_method_id')
                         ->orWhere('stripe_payment_method_id', '');
                });
            });
        } elseif ($conPago) {
            // Solo dominios con método de pago válido
            $query->where(function($q) {
                // Tiene IBAN válido
                $q->where(function($subQ) {
                    $subQ->whereNotNull('iban')
                         ->where('iban', '!=', '')
                         ->where('iban_validado', true);
                })
                // O tiene método de pago Stripe
                ->orWhere(function($subQ) {
                    $subQ->whereNotNull('stripe_payment_method_id')
                         ->where('stripe_payment_method_id', '!=', '');
                });
            });
        }

        $dominios = $query->get();

        // Filtrar usando el método getFechaCaducidad() para asegurar que usamos la fecha correcta
        // Si se especificó un dominio ID, no filtrar por fecha (mostrar siempre)
        if ($dominioId) {
            $dominiosFiltrados = $dominios->filter(function($dominio) {
                // Solo verificar que tenga fecha de caducidad
                return $dominio->getFechaCaducidad() !== null;
            });
        } else {
            $dominiosFiltrados = $dominios->filter(function($dominio) use ($fechaLimite) {
                $fechaCaducidad = $dominio->getFechaCaducidad();
                if (!$fechaCaducidad) {
                    return false;
                }
                return $fechaCaducidad->lte($fechaLimite) && $fechaCaducidad->isFuture();
            });
        }

        // Verificación adicional: asegurar que no tienen método de pago válido (si no es --todos o --con-pago)
        if (!$todos && !$conPago) {
            $dominiosFiltrados = $dominiosFiltrados->filter(function($dominio) {
                return !$dominio->tieneMetodoPagoValido();
            });
        } elseif ($conPago) {
            $dominiosFiltrados = $dominiosFiltrados->filter(function($dominio) {
                return $dominio->tieneMetodoPagoValido();
            });
        }

        if ($dominiosFiltrados->isEmpty()) {
            $this->warn("No se encontraron dominios que vencen en los próximos {$dias} días.");
            return 0;
        }

        // Preparar datos para la tabla
        $datos = $dominiosFiltrados->map(function($dominio) {
            $fechaCaducidad = $dominio->getFechaCaducidad();
            $cliente = $dominio->cliente;
            
            // Calcular días restantes
            $diasRestantes = $fechaCaducidad ? Carbon::now()->diffInDays($fechaCaducidad, false) : null;
            
            // Método de pago
            $metodoPago = 'Sin método';
            if ($dominio->iban_validado && !empty($dominio->iban)) {
                $metodoPago = 'IBAN';
            } elseif (!empty($dominio->stripe_payment_method_id)) {
                $metodoPago = 'Stripe';
            }

            // Obtener teléfono del cliente
            $telefono = 'Sin teléfono';
            if ($cliente) {
                $telefonoRaw = null;
                if (!empty($cliente->phone)) {
                    $telefonoRaw = $cliente->phone;
                } elseif ($cliente->phones && $cliente->phones->isNotEmpty()) {
                    // Si no hay phone directo, intentar obtener el primer teléfono de la relación
                    $telefonoRaw = $cliente->phones->first()->phone ?? null;
                }
                
                // Formatear teléfono: 34 + número todo seguido
                if ($telefonoRaw) {
                    $telefono = $this->formatearTelefono($telefonoRaw);
                }
            }

            return [
                'ID' => $dominio->id,
                'Dominio' => $dominio->dominio,
                'Cliente' => $cliente ? ($cliente->name ?? 'Sin nombre') : 'Sin cliente',
                'Email Cliente' => $cliente ? ($cliente->email ?? 'Sin email') : 'Sin email',
                'Teléfono Cliente' => $telefono,
                'Fecha Caducidad' => $fechaCaducidad ? $fechaCaducidad->format('d/m/Y') : 'N/A',
                'Días Restantes' => $diasRestantes !== null ? ($diasRestantes > 0 ? $diasRestantes : 'Vencido') : 'N/A',
                'Método Pago' => $metodoPago,
                'Precio Venta' => $dominio->precio_venta ? '€' . number_format($dominio->precio_venta, 2) : 'N/A',
            ];
        })->toArray();

        // Mostrar tabla
        $this->table([
            'ID',
            'Dominio',
            'Cliente',
            'Email Cliente',
            'Teléfono Cliente',
            'Fecha Caducidad',
            'Días Restantes',
            'Método Pago',
            'Precio Venta'
        ], $datos);

        $this->newLine();
        $this->info("Total: {$dominiosFiltrados->count()} dominio(s)");
        
        // Estadísticas
        $sinPago = $dominiosFiltrados->filter(function($d) { return !$d->tieneMetodoPagoValido(); })->count();
        $conPago = $dominiosFiltrados->filter(function($d) { return $d->tieneMetodoPagoValido(); })->count();
        
        $this->line("  - Sin método de pago: {$sinPago}");
        $this->line("  - Con método de pago: {$conPago}");

        // Enviar WhatsApp si se solicita
        if ($this->option('enviar-whatsapp')) {
            $this->newLine();
            $this->info("📱 Iniciando envío de mensajes de WhatsApp...");
            $this->enviarMensajesWhatsapp($dominiosFiltrados);
        }

        return 0;
    }

    /**
     * Formatea el teléfono para que tenga el formato 34XXXXXXXXX (todo seguido)
     * 
     * @param string $telefono
     * @return string
     */
    private function formatearTelefono($telefono)
    {
        if (empty($telefono)) {
            return 'Sin teléfono';
        }

        // Eliminar todos los caracteres que no sean números
        $telefono = preg_replace('/[^0-9]/', '', $telefono);

        // Si está vacío después de limpiar, retornar sin teléfono
        if (empty($telefono)) {
            return 'Sin teléfono';
        }

        // Detectar números inválidos (todos ceros, todos iguales, etc.)
        if (preg_match('/^0+$/', $telefono) || preg_match('/^(\d)\1+$/', $telefono)) {
            return 'Sin teléfono';
        }

        // Si empieza con 0034, reemplazar por 34
        if (strpos($telefono, '0034') === 0) {
            $telefono = '34' . substr($telefono, 4);
        }
        // Si empieza con 34 y tiene al menos 11 dígitos (34 + 9), mantenerlo
        elseif (strpos($telefono, '34') === 0 && strlen($telefono) >= 11) {
            // Ya tiene el 34, mantenerlo
        }
        // Si empieza con 6, 7, 8 o 9 (móviles españoles), agregar 34
        elseif (preg_match('/^[6789]/', $telefono) && strlen($telefono) == 9) {
            $telefono = '34' . $telefono;
        }
        // Si empieza con 9 (fijos españoles), agregar 34
        elseif (preg_match('/^9/', $telefono) && strlen($telefono) == 9) {
            $telefono = '34' . $telefono;
        }
        // Si tiene 9 dígitos y no empieza con 34, agregar 34
        elseif (strlen($telefono) == 9 && strpos($telefono, '34') !== 0) {
            $telefono = '34' . $telefono;
        }
        // Si tiene menos de 9 dígitos o más de 13, probablemente no es un número válido
        elseif (strlen($telefono) < 9 || strlen($telefono) > 13) {
            return 'Sin teléfono';
        }

        return $telefono;
    }

    /**
     * Envía mensajes de WhatsApp usando el template "mensaje_dominios"
     * 
     * @param \Illuminate\Support\Collection $dominios
     * @return void
     */
    private function enviarMensajesWhatsapp($dominios)
    {
        $enviados = 0;
        $fallidos = 0;
        $sinTelefono = 0;

        $progressBar = $this->output->createProgressBar($dominios->count());
        $progressBar->start();

        foreach ($dominios as $dominio) {
            try {
                $cliente = $dominio->cliente;
                
                if (!$cliente) {
                    $sinTelefono++;
                    $progressBar->advance();
                    continue;
                }

                // Obtener teléfono formateado
                $telefonoRaw = null;
                if (!empty($cliente->phone)) {
                    $telefonoRaw = $cliente->phone;
                } elseif ($cliente->phones && $cliente->phones->isNotEmpty()) {
                    $telefonoRaw = $cliente->phones->first()->phone ?? null;
                }

                $telefono = $this->formatearTelefono($telefonoRaw);
                
                if ($telefono === 'Sin teléfono') {
                    $sinTelefono++;
                    $progressBar->advance();
                    continue;
                }

                $fechaCaducidad = $dominio->getFechaCaducidad();
                
                if (!$fechaCaducidad) {
                    $this->warn("\n⚠️  Dominio {$dominio->dominio} sin fecha de caducidad.");
                    $fallidos++;
                    $progressBar->advance();
                    continue;
                }

                // Generar token único para este dominio y cliente
                // Buscar si ya existe una notificación reciente con token válido
                $notificacionExistente = DominioNotificacion::where('dominio_id', $dominio->id)
                    ->where('client_id', $cliente->id)
                    ->whereNotNull('token_enlace')
                    ->where('fecha_envio', '>=', \Carbon\Carbon::now()->subDays(30))
                    ->orderBy('fecha_envio', 'desc')
                    ->first();

                if ($notificacionExistente && $notificacionExistente->token_enlace) {
                    // Reutilizar token existente si es válido
                    $token = $notificacionExistente->token_enlace;
                } else {
                    // Generar nuevo token único para este dominio y cliente
                    $data = [
                        'client_id' => $cliente->id,
                        'dominio_id' => $dominio->id,
                        'timestamp' => now()->timestamp,
                        'random' => bin2hex(random_bytes(16))
                    ];
                    $token = hash('sha256', json_encode($data) . config('app.key'));
                }

                // Generar URL de pago
                $urlPago = route('dominio.pago.formulario', ['token' => $token]);

                // Preparar variables para el template
                $nombreCliente = $cliente->name ?? 'Cliente';
                $nombreDominio = $dominio->dominio;
                $fechaFormateada = $fechaCaducidad->format('d/m/Y');

                // Enviar template de WhatsApp
                $resultado = $this->enviarTemplateWhatsapp(
                    $telefono,
                    $nombreCliente,
                    $nombreDominio,
                    $fechaFormateada,
                    $urlPago
                );

                if (isset($resultado['error'])) {
                    // Registrar error - usar updateOrCreate para evitar duplicados
                    DominioNotificacion::updateOrCreate(
                        [
                            'dominio_id' => $dominio->id,
                            'client_id' => $cliente->id,
                            'tipo_notificacion' => 'whatsapp',
                            'fecha_envio' => now()->startOfDay() // Agrupar por día
                        ],
                        [
                            'estado' => 'fallido',
                            'token_enlace' => $token, // Token único por dominio y cliente
                            'metodo_pago_solicitado' => 'ambos',
                            'fecha_caducidad' => $fechaCaducidad->format('Y-m-d'),
                            'error_mensaje' => $resultado['error']['message'] ?? 'Error desconocido'
                        ]
                    );

                    $this->warn("\n❌ Error al enviar WhatsApp a {$telefono} (dominio: {$nombreDominio}): " . ($resultado['error']['message'] ?? 'Error desconocido'));
                    $fallidos++;
                } else {
                    // Registrar éxito - usar updateOrCreate para evitar duplicados
                    DominioNotificacion::updateOrCreate(
                        [
                            'dominio_id' => $dominio->id,
                            'client_id' => $cliente->id,
                            'tipo_notificacion' => 'whatsapp',
                            'fecha_envio' => now()->startOfDay() // Agrupar por día
                        ],
                        [
                            'estado' => 'enviado',
                            'token_enlace' => $token, // Token único por dominio y cliente
                            'metodo_pago_solicitado' => 'ambos',
                            'fecha_caducidad' => $fechaCaducidad->format('Y-m-d'),
                            'error_mensaje' => null
                        ]
                    );

                    $this->line("\n✅ WhatsApp enviado a {$telefono} para dominio {$nombreDominio}");
                    $enviados++;
                }

            } catch (\Exception $e) {
                Log::error('Error al enviar WhatsApp de dominio por vencer', [
                    'dominio_id' => $dominio->id ?? null,
                    'cliente_id' => $cliente->id ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $this->warn("\n❌ Excepción al procesar dominio {$dominio->dominio}: " . $e->getMessage());
                $fallidos++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("📊 Resumen de envío de WhatsApp:");
        $this->line("  ✅ Enviados: {$enviados}");
        $this->line("  ❌ Fallidos: {$fallidos}");
        $this->line("  ⚠️  Sin teléfono: {$sinTelefono}");
    }

    /**
     * Envía un template de WhatsApp usando la API de Meta
     * 
     * @param string $phone Número de teléfono (formato 34XXXXXXXXX)
     * @param string $nombreCliente Nombre del cliente
     * @param string $nombreDominio Nombre del dominio
     * @param string $fechaCaducidad Fecha de caducidad (formato d/m/Y)
     * @param string $urlPago URL para establecer método de pago
     * @return array
     */
    private function enviarTemplateWhatsapp($phone, $nombreCliente, $nombreDominio, $fechaCaducidad, $urlPago)
    {
        try {
            // Limpiar y formatear número de teléfono para WhatsApp API
            $phoneClean = preg_replace('/[^0-9]/', '', $phone);
            
            // Asegurar que tenga el formato correcto (sin + para la API)
            if (strpos($phoneClean, '34') !== 0 && strlen($phoneClean) == 9) {
                $phoneClean = '34' . $phoneClean;
            }

            $token = env('TOKEN_WHATSAPP');
            $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID', '102360642838173');
            $urlMensajes = 'https://graph.facebook.com/v18.0/' . $phoneNumberId . '/messages';

            // Construir el payload del template
            $payload = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $phoneClean,
                "type" => "template",
                "template" => [
                    "name" => "mensaje_dominios",
                    "language" => [
                        "code" => "es"
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                ["type" => "text", "text" => $nombreCliente],
                                ["type" => "text", "text" => $nombreDominio],
                                ["type" => "text", "text" => $fechaCaducidad],
                                ["type" => "text", "text" => $urlPago],
                            ]
                        ]
                    ]
                ]
            ];

            Log::info('Enviando template WhatsApp para dominio', [
                'phone' => $phoneClean,
                'dominio' => $nombreDominio,
                'payload' => $payload
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $urlMensajes,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
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

            Log::info('Respuesta de WhatsApp API', [
                'http_code' => $httpCode,
                'response' => $resultado
            ]);

            if ($httpCode !== 200 || isset($resultado['error'])) {
                return [
                    'error' => $resultado['error'] ?? ['message' => 'Error desconocido'],
                    'http_code' => $httpCode
                ];
            }

            return [
                'success' => true,
                'data' => $resultado
            ];

        } catch (\Exception $e) {
            Log::error('Excepción al enviar template WhatsApp', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => ['message' => $e->getMessage()]
            ];
        }
    }
}
