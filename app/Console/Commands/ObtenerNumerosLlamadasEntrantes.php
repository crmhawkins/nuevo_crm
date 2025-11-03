<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsAgent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ObtenerNumerosLlamadasEntrantes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elevenlabs:obtener-numeros-entrantes {--limit=50 : Número máximo de conversaciones a procesar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene números de teléfono de llamadas entrantes (no Hera Saliente/Dominios) desde la app Flask';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📱 Iniciando obtención de números de llamadas entrantes...');

        // URL de la app Flask (configurar en .env: FLASK_APP_URL=http://localhost:9000)
        $appUrl = env('FLASK_APP_URL', 'https://aiapi.hawkins.es/getnum');
        $this->info("🌐 URL Flask App: {$appUrl}");

        // IDs de agentes que NO procesar (Hera Saliente y Hera Dominios tienen su propio sistema)
        $agentesExcluir = [
            'agent_2101k6g86xpmf9vvcshs353mc7ft', // Hera Dominios
        ];

        // Buscar Hera Saliente por nombre y agregarlo a la exclusión
        $heraSaliente = ElevenlabsAgent::where('name', 'LIKE', '%Hera%')
            ->where('name', 'LIKE', '%Saliente%')
            ->first();

        if ($heraSaliente) {
            $agentesExcluir[] = $heraSaliente->agent_id;
            $this->info("🚫 Excluyendo Hera Saliente: {$heraSaliente->name} (ID: {$heraSaliente->agent_id})");
        }

        // Buscar Hera Dominios
        $heraDominios = ElevenlabsAgent::where('agent_id', 'agent_2101k6g86xpmf9vvcshs353mc7ft')->first();
        if ($heraDominios) {
            $this->info("🚫 Excluyendo Hera Dominios: {$heraDominios->name} (ID: {$heraDominios->agent_id})");
        }

        // Obtener conversaciones de OTROS agentes (llamadas entrantes) sin número
        $limit = $this->option('limit');
        $conversaciones = ElevenlabsConversation::whereNotIn('agent_id', $agentesExcluir)
            ->whereNull('numero')
            ->whereNotNull('conversation_id')
            ->orderBy('conversation_date', 'desc')
            ->limit($limit)
            ->get();

        if ($conversaciones->isEmpty()) {
            $this->info('✅ No hay llamadas entrantes sin número para procesar');
            return 0;
        }

        $this->info("📞 Procesando {$conversaciones->count()} llamadas entrantes...");
        $bar = $this->output->createProgressBar($conversaciones->count());
        $bar->start();

        $procesadas = 0;
        $errores = 0;
        $sinNumero = 0;
        $primerError = null;

        foreach ($conversaciones as $conversacion) {
            try {
                // Hacer petición GET a la app Flask
                $urlCompleta = $appUrl . '?conv_id=' . urlencode($conversacion->conversation_id);

                $response = Http::timeout(10)
                    ->withoutVerifying() // Desactivar verificación SSL si hay problemas
                    ->get($appUrl, [
                        'conv_id' => $conversacion->conversation_id
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $phone = $data['phone'] ?? false;

                    if ($phone && $phone !== false && $phone !== 'false' && !empty($phone)) {
                        // Actualizar la conversación con el número
                        $conversacion->numero = $phone;
                        $conversacion->save();

                        $procesadas++;
                        Log::info("Número obtenido para conversación entrante {$conversacion->conversation_id}: {$phone}");
                    } else {
                        $sinNumero++;
                        Log::info("Sin número disponible para conversación entrante {$conversacion->conversation_id}");
                    }
                } else {
                    $errores++;
                    $errorMsg = "HTTP {$response->status()} - " . $response->body();
                    if ($primerError === null) {
                        $primerError = "Conv {$conversacion->conversation_id}: {$errorMsg}";
                    }
                    Log::warning("Error al consultar Flask para {$conversacion->conversation_id}: {$errorMsg}");
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $errores++;
                $errorMsg = "Timeout/Connection: " . $e->getMessage();
                if ($primerError === null) {
                    $primerError = "Conv {$conversacion->conversation_id}: {$errorMsg}";
                }
                Log::error("Error de conexión para conversación entrante {$conversacion->conversation_id}: {$errorMsg}");
            } catch (\Exception $e) {
                $errores++;
                $errorMsg = $e->getMessage();
                if ($primerError === null) {
                    $primerError = "Conv {$conversacion->conversation_id}: {$errorMsg}";
                }
                Log::error("Error obteniendo número para conversación entrante {$conversacion->conversation_id}: {$errorMsg}");
            }

            $bar->advance();

            // Pequeña pausa para no sobrecargar
            usleep(100000); // 100ms
        }

        $bar->finish();
        $this->newLine(2);

        // Resumen
        $this->info("✅ Números obtenidos: {$procesadas}");
        if ($sinNumero > 0) {
            $this->warn("⚠️  Sin número en app Flask: {$sinNumero}");
        }
        if ($errores > 0) {
            $this->error("❌ Errores: {$errores}");
            if ($primerError) {
                $this->line("   🔍 Primer error de ejemplo:");
                $this->line("   {$primerError}");
            }
            $this->line("");
            $this->warn("💡 Revisa los logs para más detalles: storage/logs/laravel.log");
        }

        Log::info("ObtenerNumerosLlamadasEntrantes: Procesadas={$procesadas}, SinNumero={$sinNumero}, Errores={$errores}");

        return 0;
    }
}

