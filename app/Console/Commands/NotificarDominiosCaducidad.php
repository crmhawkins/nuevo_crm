<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Models\Dominios\DominioNotificacion;
use App\Models\Clients\Client;
use App\Mail\MailDominioCaducidad;
use App\Http\Controllers\Whatsapp\WhatsappController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificarDominiosCaducidad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dominios:notificar-caducidad 
                            {--dias=30 : Días antes de caducar para notificar}
                            {--solo-email : Solo enviar notificaciones por email}
                            {--solo-whatsapp : Solo enviar notificaciones por WhatsApp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones automáticas a clientes cuyos dominios están próximos a caducar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dias = (int) $this->option('dias');
        $soloEmail = $this->option('solo-email');
        $soloWhatsapp = $this->option('solo-whatsapp');

        $this->info("🔔 Iniciando notificaciones de dominios próximos a caducar...");
        $this->line("  - Días antes de caducar: {$dias}");
        $this->line("  - Modo: " . ($soloEmail ? 'Solo Email' : ($soloWhatsapp ? 'Solo WhatsApp' : 'Email y WhatsApp')));

        // Obtener dominios que necesitan notificación
        $dominios = Dominio::with('cliente')
            ->whereHas('cliente', function($query) {
                $query->whereNotNull('email')
                      ->orWhereNotNull('phone');
            })
            ->get()
            ->filter(function($dominio) use ($dias) {
                return $dominio->necesitaNotificacion($dias);
            });

        if ($dominios->isEmpty()) {
            $this->info("✅ No hay dominios que requieran notificación en este momento.");
            return 0;
        }

        $this->line("📊 Dominios a notificar: " . $dominios->count());

        $enviados = 0;
        $fallidos = 0;
        $progressBar = $this->output->createProgressBar($dominios->count());
        $progressBar->start();

        foreach ($dominios as $dominio) {
            try {
                $cliente = $dominio->cliente;
                
                if (!$cliente) {
                    $this->warn("⚠️  Dominio {$dominio->dominio} sin cliente asociado.");
                    $fallidos++;
                    $progressBar->advance();
                    continue;
                }

                // Generar token para el cliente si no existe o ha expirado
                if (!$cliente->token_verificacion_dominios || 
                    ($cliente->token_verificacion_expires_at && $cliente->token_verificacion_expires_at->isPast())) {
                    $token = $cliente->generarTokenVerificacion($dominio->id);
                } else {
                    $token = $cliente->token_verificacion_dominios;
                }

                $fechaCaducidad = $dominio->getFechaCaducidad();
                
                if (!$fechaCaducidad) {
                    $this->warn("⚠️  Dominio {$dominio->dominio} sin fecha de caducidad.");
                    $fallidos++;
                    $progressBar->advance();
                    continue;
                }
                
                $urlPago = route('dominio.pago.formulario', ['token' => $token]);

                // Enviar email
                if (!$soloWhatsapp && $cliente->email) {
                    try {
                        Mail::to($cliente->email)->send(new MailDominioCaducidad($dominio, $cliente, $fechaCaducidad, $urlPago));
                        
                        DominioNotificacion::create([
                            'dominio_id' => $dominio->id,
                            'client_id' => $cliente->id,
                            'tipo_notificacion' => 'email',
                            'fecha_envio' => now(),
                            'estado' => 'enviado',
                            'token_enlace' => $token,
                            'metodo_pago_solicitado' => 'ambos',
                            'fecha_caducidad' => $fechaCaducidad->format('Y-m-d')
                        ]);

                        $this->line("\n✅ Email enviado a {$cliente->email} para dominio {$dominio->dominio}");
                    } catch (\Exception $e) {
                        Log::error('Error al enviar email de caducidad de dominio', [
                            'dominio_id' => $dominio->id,
                            'cliente_id' => $cliente->id,
                            'error' => $e->getMessage()
                        ]);

                        DominioNotificacion::create([
                            'dominio_id' => $dominio->id,
                            'client_id' => $cliente->id,
                            'tipo_notificacion' => 'email',
                            'fecha_envio' => now(),
                            'estado' => 'fallido',
                            'token_enlace' => $token,
                            'metodo_pago_solicitado' => 'ambos',
                            'fecha_caducidad' => $fechaCaducidad->format('Y-m-d'),
                            'error_mensaje' => $e->getMessage()
                        ]);

                        $this->warn("\n❌ Error al enviar email a {$cliente->email}: " . $e->getMessage());
                        $fallidos++;
                    }
                }

                // Enviar WhatsApp
                if (!$soloEmail && $cliente->phone) {
                    try {
                        $whatsappController = new WhatsappController();
                        $mensaje = $this->generarMensajeWhatsapp($dominio, $cliente, $fechaCaducidad, $urlPago);
                        
                        $phone = $this->formatearTelefono($cliente->phone);
                        $whatsappController->contestarWhatsapp($phone, $mensaje);

                        DominioNotificacion::create([
                            'dominio_id' => $dominio->id,
                            'client_id' => $cliente->id,
                            'tipo_notificacion' => 'whatsapp',
                            'fecha_envio' => now(),
                            'estado' => 'enviado',
                            'token_enlace' => $token,
                            'metodo_pago_solicitado' => 'ambos',
                            'fecha_caducidad' => $fechaCaducidad->format('Y-m-d')
                        ]);

                        $this->line("\n✅ WhatsApp enviado a {$phone} para dominio {$dominio->dominio}");
                    } catch (\Exception $e) {
                        Log::error('Error al enviar WhatsApp de caducidad de dominio', [
                            'dominio_id' => $dominio->id,
                            'cliente_id' => $cliente->id,
                            'error' => $e->getMessage()
                        ]);

                        DominioNotificacion::create([
                            'dominio_id' => $dominio->id,
                            'client_id' => $cliente->id,
                            'tipo_notificacion' => 'whatsapp',
                            'fecha_envio' => now(),
                            'estado' => 'fallido',
                            'token_enlace' => $token,
                            'metodo_pago_solicitado' => 'ambos',
                            'fecha_caducidad' => $fechaCaducidad->format('Y-m-d'),
                            'error_mensaje' => $e->getMessage()
                        ]);

                        $this->warn("\n❌ Error al enviar WhatsApp a {$cliente->phone}: " . $e->getMessage());
                        $fallidos++;
                    }
                }

                // Marcar dominio como notificado
                $dominio->marcarNotificacionEnviada();
                $enviados++;

            } catch (\Exception $e) {
                Log::error('Error general al procesar notificación de dominio', [
                    'dominio_id' => $dominio->id ?? null,
                    'error' => $e->getMessage()
                ]);
                $fallidos++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("✅ Proceso completado:");
        $this->line("  - Notificaciones enviadas: {$enviados}");
        $this->line("  - Notificaciones fallidas: {$fallidos}");

        return 0;
    }

    /**
     * Generar mensaje de WhatsApp
     */
    private function generarMensajeWhatsapp($dominio, $cliente, $fechaCaducidad, $urlPago)
    {
        $fecha = Carbon::parse($fechaCaducidad)->format('d/m/Y');
        
        $mensaje = "Hola {$cliente->name},\n\n";
        $mensaje .= "Le informamos que su dominio *{$dominio->dominio}* caducará el *{$fecha}*.\n\n";
        $mensaje .= "Para asegurar la continuidad de su servicio, necesitamos que configure un método de pago:\n";
        $mensaje .= "• IBAN para domiciliación SEPA\n";
        $mensaje .= "• Tarjeta de crédito mediante Stripe\n\n";
        $mensaje .= "Puede configurar su método de pago aquí:\n";
        $mensaje .= "{$urlPago}\n\n";
        $mensaje .= "Gracias por confiar en nosotros.\n";
        $mensaje .= "Los Creativos de Hawkins";

        return $mensaje;
    }

    /**
     * Formatear número de teléfono para WhatsApp
     */
    private function formatearTelefono($phone)
    {
        // Eliminar espacios y caracteres especiales
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Si no empieza con código de país, añadir 34 (España)
        if (!str_starts_with($phone, '+') && !str_starts_with($phone, '34')) {
            $phone = '34' . ltrim($phone, '0');
        }
        
        // Asegurar que empiece con +
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }
}
