<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Models\Dominios\Dominio;
use App\Services\DominioFacturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    protected $facturaService;

    public function __construct(DominioFacturaService $facturaService)
    {
        $this->facturaService = $facturaService;
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Manejar webhooks de Stripe
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        if (!$endpointSecret) {
            Log::warning('STRIPE_WEBHOOK_SECRET no configurado en .env');
            return response('Webhook secret not configured', 500);
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload en webhook de Stripe', [
                'error' => $e->getMessage()
            ]);
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid signature en webhook de Stripe', [
                'error' => $e->getMessage()
            ]);
            return response('Invalid signature', 400);
        }

        // Procesar el evento
        try {
            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    $this->handlePaymentSucceeded($event);
                    break;

                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event);
                    break;

                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event);
                    break;

                default:
                    Log::info('Evento de Stripe no manejado', [
                        'type' => $event->type
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar evento de Stripe', [
                'event_type' => $event->type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Error processing event', 500);
        }

        return response('OK', 200);
    }

    /**
     * Manejar pago exitoso
     */
    protected function handlePaymentSucceeded($event)
    {
        $stripeInvoice = $event->data->object;

        // Verificar que es una factura de renovación de dominio
        if (!isset($stripeInvoice->metadata->dominio_id)) {
            Log::info('Invoice no es de dominio, ignorando', [
                'invoice_id' => $stripeInvoice->id
            ]);
            return;
        }

        $dominioId = $stripeInvoice->metadata->dominio_id;
        $dominio = Dominio::find($dominioId);

        if (!$dominio) {
            Log::error('Dominio no encontrado para invoice de Stripe', [
                'dominio_id' => $dominioId,
                'invoice_id' => $stripeInvoice->id
            ]);
            return;
        }

        // Verificar que la suscripción coincide
        if ($dominio->stripe_subscription_id !== $stripeInvoice->subscription) {
            Log::warning('Subscription ID no coincide', [
                'dominio_subscription' => $dominio->stripe_subscription_id,
                'invoice_subscription' => $stripeInvoice->subscription
            ]);
        }

        try {
            // Verificar que el pago fue exitoso
            if ($stripeInvoice->status !== 'paid') {
                Log::warning('Invoice no está pagada, ignorando', [
                    'invoice_id' => $stripeInvoice->id,
                    'status' => $stripeInvoice->status
                ]);
                return;
            }

            // Crear factura en el sistema
            $factura = $this->facturaService->crearFacturaDesdeStripe($dominio, $stripeInvoice);

            Log::info('Pago procesado exitosamente desde Stripe', [
                'dominio_id' => $dominio->id,
                'factura_id' => $factura->id,
                'stripe_invoice_id' => $stripeInvoice->id,
                'amount' => $stripeInvoice->amount_paid / 100,
                'subscription_id' => $stripeInvoice->subscription
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear factura desde webhook', [
                'dominio_id' => $dominio->id,
                'stripe_invoice_id' => $stripeInvoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // No lanzar excepción para que Stripe no reintente el webhook
            // El error ya está registrado para revisión manual
        }
    }

    /**
     * Manejar pago fallido
     */
    protected function handlePaymentFailed($event)
    {
        $stripeInvoice = $event->data->object;

        if (!isset($stripeInvoice->metadata->dominio_id)) {
            Log::info('Invoice sin metadata dominio_id, ignorando', [
                'invoice_id' => $stripeInvoice->id
            ]);
            return;
        }

        $dominioId = $stripeInvoice->metadata->dominio_id;
        $dominio = Dominio::with('cliente')->find($dominioId);

        if (!$dominio) {
            Log::error('Dominio no encontrado para invoice fallido', [
                'dominio_id' => $dominioId,
                'invoice_id' => $stripeInvoice->id
            ]);
            return;
        }

        $cliente = $dominio->cliente;
        if (!$cliente) {
            Log::error('Cliente no encontrado para dominio', [
                'dominio_id' => $dominio->id
            ]);
            return;
        }

        $attemptCount = $stripeInvoice->attempt_count ?? 0;
        $failureReason = 'Razón desconocida';
        
        if (isset($stripeInvoice->last_payment_error) && is_object($stripeInvoice->last_payment_error)) {
            $failureReason = $stripeInvoice->last_payment_error->message ?? 'Razón desconocida';
        } elseif (isset($stripeInvoice->last_payment_error) && is_string($stripeInvoice->last_payment_error)) {
            $failureReason = $stripeInvoice->last_payment_error;
        }

        Log::warning('Pago fallido en Stripe para dominio', [
            'dominio_id' => $dominio->id,
            'cliente_id' => $cliente->id,
            'stripe_invoice_id' => $stripeInvoice->id,
            'attempt_count' => $attemptCount,
            'failure_reason' => $failureReason,
            'amount' => ($stripeInvoice->amount_due ?? 0) / 100
        ]);

        // Notificar al cliente si es el último intento (Stripe intenta 3 veces por defecto)
        if ($attemptCount >= 3) {
            try {
                // Generar token si no existe para el enlace de actualización
                if (!$cliente->token_verificacion_dominios || 
                    ($cliente->token_verificacion_expires_at && $cliente->token_verificacion_expires_at->isPast())) {
                    $cliente->generarTokenVerificacion($dominio->id);
                }

                // Enviar notificación por email
                if ($cliente->email) {
                    try {
                        Mail::to($cliente->email)->send(
                            new \App\Mail\MailDominioPagoFallido($dominio, $cliente, $failureReason)
                        );
                        Log::info('Email de pago fallido enviado', [
                            'cliente_id' => $cliente->id,
                            'email' => $cliente->email
                        ]);
                    } catch (\Exception $emailError) {
                        Log::error('Error al enviar email de pago fallido', [
                            'error' => $emailError->getMessage(),
                            'cliente_id' => $cliente->id
                        ]);
                    }
                }

                // Enviar notificación por WhatsApp si está disponible
                if ($cliente->phone) {
                    try {
                        $whatsappController = new \App\Http\Controllers\Whatsapp\WhatsappController();
                        $urlPago = $cliente->token_verificacion_dominios 
                            ? route('dominio.pago.formulario', $cliente->token_verificacion_dominios)
                            : 'Contacte con soporte';
                        
                        $mensaje = "Hola {$cliente->name},\n\n";
                        $mensaje .= "⚠️ Le informamos que el pago automático de la renovación de su dominio *{$dominio->dominio}* ha fallado.\n\n";
                        $mensaje .= "Razón: {$failureReason}\n\n";
                        $mensaje .= "Por favor, actualice su método de pago para evitar la pérdida de su dominio:\n";
                        $mensaje .= "{$urlPago}\n\n";
                        $mensaje .= "Gracias.\nLos Creativos de Hawkins";

                        $phone = preg_replace('/[^0-9+]/', '', $cliente->phone);
                        if (!str_starts_with($phone, '+') && !str_starts_with($phone, '34')) {
                            $phone = '34' . ltrim($phone, '0');
                        }
                        if (!str_starts_with($phone, '+')) {
                            $phone = '+' . $phone;
                        }

                        $whatsappController->contestarWhatsapp($phone, $mensaje);
                        Log::info('WhatsApp de pago fallido enviado', [
                            'cliente_id' => $cliente->id,
                            'phone' => $phone
                        ]);
                    } catch (\Exception $whatsappError) {
                        Log::error('Error al enviar WhatsApp de pago fallido', [
                            'error' => $whatsappError->getMessage(),
                            'cliente_id' => $cliente->id
                        ]);
                    }
                }
            } catch (\Exception $notificationError) {
                Log::error('Error al enviar notificación de pago fallido', [
                    'error' => $notificationError->getMessage()
                ]);
            }
        }
    }

    /**
     * Manejar actualización de suscripción
     */
    protected function handleSubscriptionUpdated($event)
    {
        $subscription = $event->data->object;

        if (!isset($subscription->metadata->dominio_id)) {
            return;
        }

        $dominioId = $subscription->metadata->dominio_id;
        $dominio = Dominio::find($dominioId);

        if (!$dominio) {
            return;
        }

        Log::info('Suscripción actualizada en Stripe', [
            'dominio_id' => $dominio->id,
            'subscription_id' => $subscription->id,
            'status' => $subscription->status
        ]);
    }

    /**
     * Manejar cancelación de suscripción
     */
    protected function handleSubscriptionDeleted($event)
    {
        $subscription = $event->data->object;

        if (!isset($subscription->metadata->dominio_id)) {
            return;
        }

        $dominioId = $subscription->metadata->dominio_id;
        $dominio = Dominio::find($dominioId);

        if (!$dominio) {
            return;
        }

        // Limpiar suscripción del dominio
        $dominio->update([
            'stripe_subscription_id' => null,
            'stripe_plan_id' => null
        ]);

        Log::info('Suscripción cancelada en Stripe', [
            'dominio_id' => $dominio->id,
            'subscription_id' => $subscription->id
        ]);
    }
}
