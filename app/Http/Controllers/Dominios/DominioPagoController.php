<?php

namespace App\Http\Controllers\Dominios;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Dominios\Dominio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\SetupIntent;
use Stripe\PaymentMethod;
use Stripe\Plan;
use Stripe\Subscription;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class DominioPagoController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Mostrar formulario de pago con token
     */
    public function showFormularioPago($token)
    {
        $validacion = $this->validarToken($token);
        
        if (!$validacion['valido']) {
            return view('dominio-pago.error', [
                'mensaje' => $validacion['mensaje']
            ]);
        }

        $dominio = $validacion['dominio'];
        $cliente = $validacion['cliente'];

        return view('dominio-pago.formulario', compact('dominio', 'cliente', 'token'));
    }

    /**
     * Validar token y devolver datos
     * El token debe ser único por cliente y dominio
     */
    public function validarToken($token)
    {
        // Buscar el token en dominio_notificaciones (token único por dominio y cliente)
        $notificacion = \App\Models\Dominios\DominioNotificacion::where('token_enlace', $token)
            ->with(['dominio', 'cliente'])
            ->orderBy('fecha_envio', 'desc')
            ->first();

        if (!$notificacion) {
            // Fallback: buscar en el cliente (compatibilidad con tokens antiguos)
            $cliente = Client::where('token_verificacion_dominios', $token)->first();
            
            if (!$cliente) {
                return [
                    'valido' => false,
                    'mensaje' => 'Token inválido o expirado.'
                ];
            }

            if (!$cliente->validarToken($token)) {
                return [
                    'valido' => false,
                    'mensaje' => 'El token ha expirado. Por favor, solicite un nuevo enlace.'
                ];
            }

            // Obtener el dominio más próximo a caducar del cliente (comportamiento antiguo)
            $dominio = $cliente->dominios()
                ->where(function($query) {
                    $query->whereNotNull('fecha_renovacion_ionos')
                          ->orWhereNotNull('date_end');
                })
                ->orderByRaw('COALESCE(fecha_renovacion_ionos, date_end) ASC')
                ->first();

            if (!$dominio) {
                return [
                    'valido' => false,
                    'mensaje' => 'No se encontró ningún dominio asociado.'
                ];
            }

            return [
                'valido' => true,
                'cliente' => $cliente,
                'dominio' => $dominio
            ];
        }

        // Validar que el token no haya expirado (30 días desde la fecha de envío)
        $fechaExpiracion = \Carbon\Carbon::parse($notificacion->fecha_envio)->addDays(30);
        if ($fechaExpiracion->isPast()) {
            return [
                'valido' => false,
                'mensaje' => 'El token ha expirado. Por favor, solicite un nuevo enlace.'
            ];
        }

        // Validar que el dominio y cliente existan
        if (!$notificacion->dominio || !$notificacion->cliente) {
            return [
                'valido' => false,
                'mensaje' => 'No se encontró el dominio o cliente asociado al token.'
            ];
        }

        return [
            'valido' => true,
            'cliente' => $notificacion->cliente,
            'dominio' => $notificacion->dominio
        ];
    }

    /**
     * Guardar IBAN
     */
    public function guardarIban(Request $request, $token)
    {
        $validacion = $this->validarToken($token);
        
        if (!$validacion['valido']) {
            return redirect()->back()->with('error', $validacion['mensaje']);
        }

        // Limpiar el IBAN antes de validar (eliminar espacios y convertir a mayúsculas)
        $ibanLimpio = strtoupper(str_replace(' ', '', $request->iban ?? ''));

        $validator = Validator::make([
            'iban' => $ibanLimpio
        ], [
            'iban' => [
                'required',
                'string',
                'min:15',
                'max:34',
                'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/',
            ],
        ], [
            'iban.required' => 'El IBAN es obligatorio.',
            'iban.min' => 'El IBAN debe tener al menos 15 caracteres.',
            'iban.max' => 'El IBAN no puede tener más de 34 caracteres.',
            'iban.regex' => 'El formato del IBAN no es válido. Debe comenzar con 2 letras, seguido de 2 dígitos y luego caracteres alfanuméricos.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dominio = $validacion['dominio'];
        $iban = $ibanLimpio;

        // Validar formato IBAN básico (doble verificación)
        if (!$this->validarFormatoIban($iban)) {
            return redirect()->back()
                ->with('error', 'El formato del IBAN no es válido.')
                ->withInput();
        }

        $dominio->update([
            'iban' => $iban,
            'iban_validado' => true,
            'metodo_pago_preferido' => 'iban'
        ]);

        Log::info('IBAN guardado para dominio', [
            'dominio_id' => $dominio->id,
            'cliente_id' => $validacion['cliente']->id
        ]);

        return redirect()->route('dominio.pago.confirmacion', $token)
            ->with('success', 'IBAN guardado correctamente. Su método de pago ha sido configurado.');
    }

    /**
     * Procesar pago con Stripe
     */
    public function procesarStripe(Request $request, $token)
    {
        $validacion = $this->validarToken($token);
        
        if (!$validacion['valido']) {
            return redirect()->back()->with('error', $validacion['mensaje']);
        }

        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|string',
        ], [
            'payment_method_id.required' => 'Debe seleccionar un método de pago.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cliente = $validacion['cliente'];
        $dominio = $validacion['dominio'];

        $plan = null;
        $subscription = null;

        try {
            // Validaciones previas
            $precioVenta = $dominio->precio_venta ?? 0;
            if ($precioVenta <= 0) {
                return redirect()->back()
                    ->with('error', 'El dominio no tiene un precio de venta configurado. Por favor, contacte con soporte.');
            }

            $fechaCaducidad = $dominio->getFechaCaducidad();
            if (!$fechaCaducidad) {
                return redirect()->back()
                    ->with('error', 'El dominio no tiene fecha de caducidad configurada. Por favor, contacte con soporte.');
            }

            // Crear o verificar cliente de Stripe
            $stripeCustomerId = $cliente->stripe_customer_id;
            
            if (!$stripeCustomerId) {
                // Crear nuevo cliente
                try {
                    // Intentar obtener el Test Clock más reciente para asociarlo al cliente
                    $testClockId = null;
                    try {
                        $testClocks = \Stripe\TestHelpers\TestClock::all(['limit' => 1]);
                        if (count($testClocks->data) > 0) {
                            $testClockId = $testClocks->data[0]->id;
                            Log::info('Test Clock detectado, asociando al cliente', [
                                'test_clock_id' => $testClockId,
                                'cliente_id' => $cliente->id
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::info('No se pudo obtener Test Clock, continuando sin él', [
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    $customerData = [
                        'email' => $cliente->email ?? 'cliente@example.com',
                        'name' => $cliente->name ?? 'Cliente',
                        'metadata' => [
                            'client_id' => $cliente->id,
                        ]
                    ];
                    
                    // Si hay Test Clock, asociarlo al cliente
                    if ($testClockId) {
                        $customerData['test_clock'] = $testClockId;
                    }
                    
                    $stripeCustomer = \Stripe\Customer::create($customerData);

                    $cliente->update([
                        'stripe_customer_id' => $stripeCustomer->id
                    ]);
                    
                    $stripeCustomerId = $stripeCustomer->id;
                } catch (ApiErrorException $e) {
                    Log::error('Error al crear cliente en Stripe', [
                        'error' => $e->getMessage(),
                        'cliente_id' => $cliente->id
                    ]);
                    return redirect()->back()
                        ->with('error', 'Error al crear el cliente en el sistema de pagos. Por favor, intente de nuevo.');
                }
            } else {
                // Verificar que el cliente existe en Stripe
                try {
                    \Stripe\Customer::retrieve($stripeCustomerId);
                } catch (\Exception $e) {
                    // Si el cliente no existe, crear uno nuevo
                    Log::warning('Cliente Stripe no encontrado, creando nuevo', [
                        'cliente_id' => $cliente->id,
                        'stripe_customer_id' => $stripeCustomerId,
                        'error' => $e->getMessage()
                    ]);
                    
                    try {
                        // Intentar obtener el Test Clock más reciente para asociarlo al cliente
                        $testClockId = null;
                        try {
                            $testClocks = \Stripe\TestHelpers\TestClock::all(['limit' => 1]);
                            if (count($testClocks->data) > 0) {
                                $testClockId = $testClocks->data[0]->id;
                                Log::info('Test Clock detectado al recrear cliente, asociando', [
                                    'test_clock_id' => $testClockId,
                                    'cliente_id' => $cliente->id
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::info('No se pudo obtener Test Clock al recrear cliente', [
                                'error' => $e->getMessage()
                            ]);
                        }
                        
                        $customerData = [
                            'email' => $cliente->email ?? 'cliente@example.com',
                            'name' => $cliente->name ?? 'Cliente',
                            'metadata' => [
                                'client_id' => $cliente->id,
                            ]
                        ];
                        
                        // Si hay Test Clock, asociarlo al cliente
                        if ($testClockId) {
                            $customerData['test_clock'] = $testClockId;
                        }
                        
                        $stripeCustomer = \Stripe\Customer::create($customerData);

                        $cliente->update([
                            'stripe_customer_id' => $stripeCustomer->id
                        ]);
                        
                        $stripeCustomerId = $stripeCustomer->id;
                    } catch (ApiErrorException $createError) {
                        Log::error('Error al crear nuevo cliente en Stripe', [
                            'error' => $createError->getMessage(),
                            'cliente_id' => $cliente->id
                        ]);
                        return redirect()->back()
                            ->with('error', 'Error al crear el cliente en el sistema de pagos. Por favor, intente de nuevo.');
                    }
                }
            }

            // Adjuntar método de pago al cliente
            try {
                $paymentMethod = PaymentMethod::retrieve($request->payment_method_id);
                $paymentMethod->attach([
                    'customer' => $stripeCustomerId,
                ]);
            } catch (ApiErrorException $e) {
                Log::error('Error al adjuntar método de pago', [
                    'error' => $e->getMessage(),
                    'payment_method_id' => $request->payment_method_id
                ]);
                return redirect()->back()
                    ->with('error', 'Error al guardar el método de pago: ' . $e->getMessage());
            }

            // Crear Plan en Stripe para este dominio
            try {
                $amountInCents = (int)round($precioVenta * 100); // Convertir a céntimos y redondear
                
                Log::info('Creando Plan en Stripe', [
                    'dominio_id' => $dominio->id,
                    'precio_venta' => $precioVenta,
                    'amount_in_cents' => $amountInCents,
                    'amount_in_euros' => $amountInCents / 100
                ]);
                
                $plan = Plan::create([
                    'amount' => $amountInCents,
                    'currency' => 'eur',
                    'interval' => 'year', // Renovación anual
                    'product' => [
                        'name' => "Renovación dominio {$dominio->dominio}",
                    ],
                    'metadata' => [
                        'dominio_id' => $dominio->id,
                        'cliente_id' => $cliente->id,
                        'tipo' => 'renovacion_dominio',
                        'precio_venta' => (string)$precioVenta
                    ]
                ]);
                
                Log::info('Plan creado en Stripe', [
                    'plan_id' => $plan->id,
                    'amount' => $plan->amount,
                    'currency' => $plan->currency,
                    'interval' => $plan->interval
                ]);
            } catch (ApiErrorException $e) {
                Log::error('Error al crear Plan en Stripe', [
                    'error' => $e->getMessage(),
                    'dominio_id' => $dominio->id,
                    'precio' => $precioVenta
                ]);
                return redirect()->back()
                    ->with('error', 'Error al configurar el plan de pago: ' . $e->getMessage());
            }

            // Calcular fecha de cobro (fecha de caducidad del dominio)
            // Usar la fecha de caducidad directamente - es la fecha real cuando debe cobrarse
            $fechaCaducidadInicioDia = $fechaCaducidad->copy()->startOfDay();
            $billingCycleAnchor = $fechaCaducidadInicioDia->timestamp;
            
            $fechaActual = now();
            $timestampActual = time();
            
            // Usar la fecha de caducidad directamente - NO ajustar por Test Clock
            // Solo ajustar si está realmente en el pasado según el tiempo real
            if ($billingCycleAnchor < ($timestampActual - 86400)) {
                // La fecha está en el pasado, usar el mismo día/mes del año siguiente
                $billingCycleAnchor = $fechaActual->copy()
                    ->addYear() // Año siguiente
                    ->month($fechaCaducidadInicioDia->month)
                    ->day($fechaCaducidadInicioDia->day)
                    ->startOfDay()
                    ->timestamp;
                
                Log::info('Fecha de caducidad ajustada para billing_cycle_anchor (estaba en el pasado)', [
                    'dominio_id' => $dominio->id,
                    'fecha_caducidad_original' => $fechaCaducidad->format('Y-m-d'),
                    'billing_cycle_anchor_nuevo' => $billingCycleAnchor,
                    'billing_cycle_anchor_fecha' => date('Y-m-d H:i:s', $billingCycleAnchor),
                    'fecha_actual' => $fechaActual->format('Y-m-d H:i:s')
                ]);
            } else {
                // La fecha es futura, usar directamente (2026-06-09)
                Log::info('Fecha de caducidad es futura, usando directamente', [
                    'dominio_id' => $dominio->id,
                    'fecha_caducidad' => $fechaCaducidad->format('Y-m-d'),
                    'billing_cycle_anchor' => $billingCycleAnchor,
                    'billing_cycle_anchor_fecha' => date('Y-m-d H:i:s', $billingCycleAnchor),
                    'fecha_actual' => $fechaActual->format('Y-m-d H:i:s')
                ]);
            }

            // NO usar cancel_at - dejar que la suscripción se renueve automáticamente
            // Si necesitas cancelarla después de X años, hazlo manualmente o con webhook
            $cancelAtTimestamp = null;

            // Log para verificar el cálculo
            Log::info('Configurando billing_cycle_anchor para suscripción', [
                'dominio_id' => $dominio->id,
                'dominio' => $dominio->dominio,
                'fecha_caducidad_original' => $fechaCaducidad->format('Y-m-d H:i:s'),
                'fecha_caducidad_inicio_dia' => $fechaCaducidadInicioDia->format('Y-m-d H:i:s'),
                'billing_cycle_anchor_timestamp' => $billingCycleAnchor,
                'billing_cycle_anchor_fecha' => date('Y-m-d H:i:s', $billingCycleAnchor),
                'duracion_suscripcion' => 'Renovación automática anual',
                'fecha_actual' => now()->format('Y-m-d H:i:s')
            ]);

            // Crear Subscription en Stripe
            try {
                $subscriptionData = [
                    'customer' => $stripeCustomerId,
                    'items' => [['plan' => $plan->id]],
                    'default_payment_method' => $request->payment_method_id,
                    'billing_cycle_anchor' => $billingCycleAnchor,
                    // NO usar cancel_at aquí para evitar prorrateo incorrecto
                    // La suscripción se renovará automáticamente cada año en la fecha de caducidad
                    'proration_behavior' => 'none', // No prorratear
                    'metadata' => [
                        'dominio_id' => $dominio->id,
                        'cliente_id' => $cliente->id,
                        'dominio' => $dominio->dominio,
                        'tipo' => 'renovacion_dominio',
                        'fecha_caducidad' => $fechaCaducidad->format('Y-m-d'),
                        'billing_cycle_anchor_fecha' => date('Y-m-d', $billingCycleAnchor),
                        'precio' => (string)$precioVenta
                    ]
                ];

                Log::info('Creando suscripción en Stripe con datos', [
                    'dominio_id' => $dominio->id,
                    'billing_cycle_anchor' => $billingCycleAnchor,
                    'billing_cycle_anchor_fecha' => date('Y-m-d H:i:s', $billingCycleAnchor),
                    'fecha_caducidad' => $fechaCaducidad->format('Y-m-d'),
                    'precio_plan' => $precioVenta,
                    'duracion' => 'Renovación automática anual en fecha de caducidad'
                ]);

                // Crear suscripción directamente con la fecha de caducidad
                // NO intentar ajustar el Test Clock - el usuario debe hacerlo manualmente si es necesario
                try {
                    $subscription = Subscription::create($subscriptionData);
                } catch (ApiErrorException $e) {
                    // Si falla por Test Clock, mostrar mensaje claro al usuario
                    if (strpos($e->getMessage(), 'billing_cycle_anchor') !== false && 
                        strpos($e->getMessage(), 'Test Clock') !== false) {
                        
                        Log::error('Error por Test Clock al crear suscripción', [
                            'error' => $e->getMessage(),
                            'billing_cycle_anchor' => date('Y-m-d H:i:s', $billingCycleAnchor),
                            'fecha_caducidad' => $fechaCaducidad->format('Y-m-d')
                        ]);
                        
                        return redirect()->back()
                            ->with('error', 'Error: El Test Clock de Stripe está en el futuro. Por favor, avanza el Test Clock a una fecha anterior a ' . $fechaCaducidad->format('d/m/Y') . ' desde el Dashboard de Stripe (Desarrolladores → Test Clocks) o elimínalo si no lo necesitas.');
                    } else {
                        throw $e;
                    }
                }
            } catch (ApiErrorException $e) {
                // Si falla la suscripción, intentar eliminar el plan creado
                if ($plan) {
                    try {
                        $plan->delete();
                    } catch (\Exception $deleteError) {
                        Log::warning('No se pudo eliminar el plan después de error', [
                            'plan_id' => $plan->id,
                            'error' => $deleteError->getMessage()
                        ]);
                    }
                }

                Log::error('Error al crear Subscription en Stripe', [
                    'error' => $e->getMessage(),
                    'dominio_id' => $dominio->id,
                    'plan_id' => $plan->id ?? null
                ]);

                return redirect()->back()
                    ->with('error', 'Error al crear la suscripción: ' . $e->getMessage());
            }

            // Guardar método de pago y suscripción en el dominio
            try {
                $dominio->update([
                    'stripe_payment_method_id' => $request->payment_method_id,
                    'stripe_subscription_id' => $subscription->id,
                    'stripe_plan_id' => $plan->id,
                    'metodo_pago_preferido' => 'stripe'
                ]);
            } catch (\Exception $e) {
                // Si falla al guardar, cancelar la suscripción
                if ($subscription) {
                    try {
                        $subscription->cancel();
                    } catch (\Exception $cancelError) {
                        Log::error('Error al cancelar suscripción después de fallo', [
                            'subscription_id' => $subscription->id,
                            'error' => $cancelError->getMessage()
                        ]);
                    }
                }

                Log::error('Error al guardar suscripción en base de datos', [
                    'error' => $e->getMessage(),
                    'dominio_id' => $dominio->id
                ]);

                return redirect()->back()
                    ->with('error', 'Error al guardar la configuración. La suscripción ha sido cancelada.');
            }

            Log::info('Suscripción Stripe creada exitosamente para dominio', [
                'dominio_id' => $dominio->id,
                'cliente_id' => $cliente->id,
                'payment_method_id' => $request->payment_method_id,
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'billing_cycle_anchor' => date('Y-m-d H:i:s', $billingCycleAnchor),
                'precio' => $precioVenta
            ]);

            return redirect()->route('dominio.pago.confirmacion', $token)
                ->with('success', 'Método de pago y suscripción configurados correctamente. Su dominio se renovará automáticamente el ' . date('d/m/Y', $billingCycleAnchor) . '.');

        } catch (\Exception $e) {
            // Limpiar recursos en caso de error general
            if ($subscription) {
                try {
                    $subscription->cancel();
                } catch (\Exception $cancelError) {
                    Log::warning('Error al cancelar suscripción en cleanup', [
                        'error' => $cancelError->getMessage()
                    ]);
                }
            }

            if ($plan) {
                try {
                    $plan->delete();
                } catch (\Exception $deleteError) {
                    Log::warning('Error al eliminar plan en cleanup', [
                        'error' => $deleteError->getMessage()
                    ]);
                }
            }

            Log::error('Error general al procesar suscripción Stripe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'cliente_id' => $cliente->id ?? null,
                'dominio_id' => $dominio->id ?? null
            ]);

            return redirect()->back()
                ->with('error', 'Ha ocurrido un error inesperado. Por favor, intente de nuevo o contacte con soporte.');
        }
    }

    /**
     * Crear SetupIntent para Stripe
     */
    /**
     * Crear SetupIntent para guardar método de pago
     */
    public function crearSetupIntent($token)
    {
        $validacion = $this->validarToken($token);
        
        if (!$validacion['valido']) {
            return response()->json([
                'error' => $validacion['mensaje']
            ], 400);
        }

        $cliente = $validacion['cliente'];

        try {
            // Crear o verificar cliente de Stripe
            $stripeCustomerId = $cliente->stripe_customer_id;
            
            if (!$stripeCustomerId) {
                // Crear nuevo cliente
                // Intentar obtener el Test Clock más reciente para asociarlo al cliente
                $testClockId = null;
                try {
                    $testClocks = \Stripe\TestHelpers\TestClock::all(['limit' => 1]);
                    if (count($testClocks->data) > 0) {
                        $testClockId = $testClocks->data[0]->id;
                        Log::info('Test Clock detectado en crearSetupIntent, asociando al cliente', [
                            'test_clock_id' => $testClockId,
                            'cliente_id' => $cliente->id
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::info('No se pudo obtener Test Clock en crearSetupIntent', [
                        'error' => $e->getMessage()
                    ]);
                }
                
                $customerData = [
                    'email' => $cliente->email ?? 'cliente@example.com',
                    'name' => $cliente->name ?? 'Cliente',
                    'metadata' => [
                        'client_id' => $cliente->id,
                    ]
                ];
                
                // Si hay Test Clock, asociarlo al cliente
                if ($testClockId) {
                    $customerData['test_clock'] = $testClockId;
                }
                
                $stripeCustomer = \Stripe\Customer::create($customerData);

                $cliente->update([
                    'stripe_customer_id' => $stripeCustomer->id
                ]);
                
                $stripeCustomerId = $stripeCustomer->id;
            } else {
                // Verificar que el cliente existe en Stripe
                try {
                    Log::info('Verificando existencia de cliente en Stripe', [
                        'cliente_id' => $cliente->id,
                        'stripe_customer_id' => $stripeCustomerId
                    ]);
                    
                    \Stripe\Customer::retrieve($stripeCustomerId);
                    
                    Log::info('Cliente Stripe verificado correctamente', [
                        'cliente_id' => $cliente->id,
                        'stripe_customer_id' => $stripeCustomerId
                    ]);
                } catch (\Exception $e) {
                    // Si el cliente no existe (cualquier tipo de error), crear uno nuevo
                    Log::warning('Cliente Stripe no encontrado o error al verificar, creando nuevo', [
                        'cliente_id' => $cliente->id,
                        'stripe_customer_id' => $stripeCustomerId,
                        'error' => $e->getMessage(),
                        'error_type' => get_class($e)
                    ]);
                    
                    try {
                        // Intentar obtener el Test Clock más reciente para asociarlo al cliente
                        $testClockId = null;
                        try {
                            $testClocks = \Stripe\TestHelpers\TestClock::all(['limit' => 1]);
                            if (count($testClocks->data) > 0) {
                                $testClockId = $testClocks->data[0]->id;
                                Log::info('Test Clock detectado en crearSetupIntent (catch), asociando al cliente', [
                                    'test_clock_id' => $testClockId,
                                    'cliente_id' => $cliente->id
                                ]);
                            }
                        } catch (\Exception $testClockError) {
                            Log::info('No se pudo obtener Test Clock en crearSetupIntent (catch)', [
                                'error' => $testClockError->getMessage()
                            ]);
                        }
                        
                        $customerData = [
                            'email' => $cliente->email ?? 'cliente@example.com',
                            'name' => $cliente->name ?? 'Cliente',
                            'metadata' => [
                                'client_id' => $cliente->id,
                            ]
                        ];
                        
                        // Si hay Test Clock, asociarlo al cliente
                        if ($testClockId) {
                            $customerData['test_clock'] = $testClockId;
                        }
                        
                        $stripeCustomer = \Stripe\Customer::create($customerData);

                        $cliente->update([
                            'stripe_customer_id' => $stripeCustomer->id
                        ]);
                        
                        $stripeCustomerId = $stripeCustomer->id;
                        
                        Log::info('Nuevo cliente Stripe creado exitosamente', [
                            'cliente_id' => $cliente->id,
                            'nuevo_stripe_customer_id' => $stripeCustomerId
                        ]);
                    } catch (\Exception $createError) {
                        Log::error('Error al crear nuevo cliente en Stripe', [
                            'cliente_id' => $cliente->id,
                            'error' => $createError->getMessage()
                        ]);
                        throw $createError;
                    }
                }
            }

            $setupIntent = SetupIntent::create([
                'customer' => $stripeCustomerId,
                'payment_method_types' => ['card'],
            ]);

            return response()->json([
                'client_secret' => $setupIntent->client_secret,
                'customer_id' => $cliente->stripe_customer_id
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Error al crear SetupIntent', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error al inicializar el proceso de pago.'
            ], 500);
        }
    }

    /**
     * Crear Checkout Session de Stripe y redirigir
     */
    public function crearCheckoutSession($token)
    {
        $validacion = $this->validarToken($token);
        
        if (!$validacion['valido']) {
            return redirect()->back()->with('error', $validacion['mensaje']);
        }

        $cliente = $validacion['cliente'];
        $dominio = $validacion['dominio'];

        try {
            // Validaciones previas
            $precioVenta = $dominio->precio_venta ?? 0;
            if ($precioVenta <= 0) {
                return redirect()->back()
                    ->with('error', 'El dominio no tiene un precio de venta configurado. Por favor, contacte con soporte.');
            }

            $fechaCaducidad = $dominio->getFechaCaducidad();
            if (!$fechaCaducidad) {
                return redirect()->back()
                    ->with('error', 'El dominio no tiene fecha de caducidad configurada. Por favor, contacte con soporte.');
            }

            // Crear o verificar cliente de Stripe
            $stripeCustomerId = $cliente->stripe_customer_id;
            
            if (!$stripeCustomerId) {
                // Crear nuevo cliente
                try {
                    $testClockId = null;
                    try {
                        $testClocks = \Stripe\TestHelpers\TestClock::all(['limit' => 1]);
                        if (count($testClocks->data) > 0) {
                            $testClockId = $testClocks->data[0]->id;
                        }
                    } catch (\Exception $e) {
                        // Ignorar error de Test Clock
                    }
                    
                    $customerData = [
                        'email' => $cliente->email ?? 'cliente@example.com',
                        'name' => $cliente->name ?? 'Cliente',
                        'metadata' => [
                            'client_id' => $cliente->id,
                        ]
                    ];
                    
                    if ($testClockId) {
                        $customerData['test_clock'] = $testClockId;
                    }
                    
                    $stripeCustomer = \Stripe\Customer::create($customerData);
                    $stripeCustomerId = $stripeCustomer->id;
                    
                    $cliente->update(['stripe_customer_id' => $stripeCustomerId]);
                } catch (ApiErrorException $e) {
                    Log::error('Error al crear cliente de Stripe', [
                        'error' => $e->getMessage(),
                        'cliente_id' => $cliente->id
                    ]);
                    return redirect()->back()
                        ->with('error', 'Error al crear el cliente en Stripe. Por favor, intente de nuevo.');
                }
            }

            // Calcular precio con IVA (en céntimos)
            // El precio_venta ya debería incluir IVA o no, dependiendo de tu configuración
            // Asumimos que precio_venta es sin IVA, así que agregamos 21%
            $precioConIva = round($precioVenta * 1.21 * 100);

            // Calcular trial_end (fecha de caducidad) - esto hace que NO cobre hasta esa fecha
            $trialEnd = $fechaCaducidad->timestamp;
            
            // Si la fecha está en el pasado, usar el próximo año
            if ($trialEnd <= time()) {
                $trialEnd = $fechaCaducidad->copy()->addYear()->timestamp;
            }

            // billing_cycle_anchor será igual a trial_end para que la primera facturación sea en la fecha de caducidad
            $billingCycleAnchor = $trialEnd;

            Log::info('Creando Checkout Session', [
                'precio_venta' => $precioVenta,
                'precio_con_iva' => $precioConIva,
                'precio_con_iva_euros' => $precioConIva / 100,
                'trial_end' => date('Y-m-d H:i:s', $trialEnd),
                'billing_cycle_anchor' => date('Y-m-d H:i:s', $billingCycleAnchor),
                'fecha_caducidad' => $fechaCaducidad->format('Y-m-d'),
            ]);

            // Crear Checkout Session
            $checkoutSession = Session::create([
                'customer' => $stripeCustomerId,
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Renovación de dominio ' . $dominio->dominio,
                            'description' => 'Renovación anual automática del dominio ' . $dominio->dominio,
                        ],
                        'unit_amount' => $precioConIva,
                        'recurring' => [
                            'interval' => 'year',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'subscription_data' => [
                    'trial_end' => $trialEnd, // Período de prueba hasta la fecha de caducidad (no cobra nada hasta entonces)
                    'metadata' => [
                        'dominio_id' => $dominio->id,
                        'cliente_id' => $cliente->id,
                        'dominio' => $dominio->dominio,
                        'tipo' => 'renovacion_dominio',
                        'fecha_caducidad' => $fechaCaducidad->format('Y-m-d'),
                        'precio' => (string)$precioVenta,
                    ],
                ],
                'payment_method_collection' => 'always', // Siempre pedir método de pago
                'success_url' => route('dominio.pago.confirmacion', ['token' => $token]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('dominio.pago.formulario', ['token' => $token]),
                'metadata' => [
                    'dominio_id' => $dominio->id,
                    'cliente_id' => $cliente->id,
                    'token' => $token,
                ],
            ]);

            Log::info('Checkout Session creada', [
                'session_id' => $checkoutSession->id,
                'dominio_id' => $dominio->id,
                'cliente_id' => $cliente->id,
            ]);

            // Redirigir a Stripe Checkout
            return redirect($checkoutSession->url);

        } catch (ApiErrorException $e) {
            Log::error('Error al crear Checkout Session', [
                'error' => $e->getMessage(),
                'dominio_id' => $dominio->id,
                'cliente_id' => $cliente->id,
            ]);

            return redirect()->back()
                ->with('error', 'Error al crear la sesión de pago. Por favor, intente de nuevo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado al crear Checkout Session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Error inesperado. Por favor, contacte con soporte.');
        }
    }

    /**
     * Página de confirmación
     */
    public function confirmacion($token)
    {
        $validacion = $this->validarToken($token);
        
        if (!$validacion['valido']) {
            return view('dominio-pago.error', [
                'mensaje' => $validacion['mensaje']
            ]);
        }

        $dominio = $validacion['dominio'];
        $cliente = $validacion['cliente'];

        // Si hay session_id, verificar el estado del pago
        $sessionId = request()->query('session_id');
        $pagoExitoso = false;
        
        if ($sessionId) {
            try {
                $session = Session::retrieve($sessionId);
                if ($session->payment_status === 'paid' && $session->subscription) {
                    $pagoExitoso = true;
                    
                    // Actualizar dominio con la suscripción
                    $subscription = Subscription::retrieve($session->subscription);
                    $planId = $subscription->items->data[0]->price->id;
                    
                    $dominio->update([
                        'stripe_subscription_id' => $subscription->id,
                        'stripe_plan_id' => $planId,
                        'metodo_pago_preferido' => 'stripe',
                    ]);
                    
                    Log::info('Suscripción confirmada desde Checkout', [
                        'session_id' => $sessionId,
                        'subscription_id' => $subscription->id,
                        'dominio_id' => $dominio->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error al verificar Checkout Session', [
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('dominio-pago.confirmacion', compact('dominio', 'cliente', 'token', 'pagoExitoso'));
    }

    /**
     * Validar formato IBAN
     */
    private function validarFormatoIban($iban)
    {
        // Eliminar espacios y convertir a mayúsculas
        $iban = strtoupper(str_replace(' ', '', $iban));
        
        // Verificar longitud mínima (15 caracteres) y máxima (34 caracteres)
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }
        
        // Verificar formato: 2 letras + 2 dígitos + hasta 30 caracteres alfanuméricos
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/', $iban)) {
            return false;
        }
        
        return true;
    }
}
