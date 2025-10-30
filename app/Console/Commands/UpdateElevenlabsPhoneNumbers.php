<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsAgent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateElevenlabsPhoneNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elevenlabs:update-phone-numbers {--limit=50 : Número máximo de conversaciones a procesar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los números de teléfono de las conversaciones de Hera Saliente desde la API de ElevenLabs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = config('elevenlabs.api_key');

        if (!$apiKey) {
            $this->error('API Key de ElevenLabs no configurada');
            Log::error('UpdateElevenlabsPhoneNumbers: API Key no configurada');
            return 1;
        }

        // Buscar el agente "Hera Saliente"
        $heraSaliente = ElevenlabsAgent::where('name', 'LIKE', '%Hera%')
            ->where('name', 'LIKE', '%Saliente%')
            ->first();

        if (!$heraSaliente) {
            $this->warn('No se encontró el agente "Hera Saliente"');
            Log::warning('UpdateElevenlabsPhoneNumbers: Agente Hera Saliente no encontrado');
            return 1;
        }

        $this->info("Agente encontrado: {$heraSaliente->name} (ID: {$heraSaliente->agent_id})");

        // Obtener conversaciones de Hera Saliente sin número asignado
        $limit = $this->option('limit');
        $conversaciones = ElevenlabsConversation::where('agent_id', $heraSaliente->agent_id)
            ->whereNull('numero')
            ->whereNotNull('conversation_id')
            ->orderBy('conversation_date', 'desc')
            ->limit($limit)
            ->get();

        if ($conversaciones->isEmpty()) {
            $this->info('No hay conversaciones sin número para procesar');
            return 0;
        }

        $this->info("Procesando {$conversaciones->count()} conversaciones...");
        $bar = $this->output->createProgressBar($conversaciones->count());
        $bar->start();

        $procesadas = 0;
        $errores = 0;
        $sinNumero = 0;

        foreach ($conversaciones as $conversacion) {
            try {
                // Hacer petición GET a la API de ElevenLabs
                $response = Http::withHeaders([
                    'xi-api-key' => $apiKey,
                ])
                ->timeout(10)
                ->get("https://api.elevenlabs.io/v1/convai/conversations/{$conversacion->conversation_id}");

                if ($response->successful()) {
                    $data = $response->json();

                    // Extraer el número externo
                    $externalNumber = $data['metadata']['phone_call']['external_number'] ?? null;

                    if ($externalNumber) {
                        // Actualizar la conversación con el número
                        $conversacion->numero = $externalNumber;
                        $conversacion->save();

                        $procesadas++;
                    } else {
                        $sinNumero++;
                        Log::info("UpdateElevenlabsPhoneNumbers: Sin número en conversation_id {$conversacion->conversation_id}");
                    }
                } else {
                    $errores++;
                    Log::error("UpdateElevenlabsPhoneNumbers: Error API para {$conversacion->conversation_id}: " . $response->status());
                }
            } catch (\Exception $e) {
                $errores++;
                Log::error("UpdateElevenlabsPhoneNumbers: Excepción para {$conversacion->conversation_id}: " . $e->getMessage());
            }

            $bar->advance();

            // Pequeña pausa para no sobrecargar la API
            usleep(100000); // 100ms
        }

        $bar->finish();
        $this->newLine(2);

        // Resumen
        $this->info("✅ Procesadas exitosamente: {$procesadas}");
        if ($sinNumero > 0) {
            $this->warn("⚠️  Sin número en API: {$sinNumero}");
        }
        if ($errores > 0) {
            $this->error("❌ Errores: {$errores}");
        }

        Log::info("UpdateElevenlabsPhoneNumbers: Procesadas={$procesadas}, SinNumero={$sinNumero}, Errores={$errores}");

        return 0;
    }
}
