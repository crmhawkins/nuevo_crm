<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\Log;

class VincularConversacionesConClientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elevenlabs:vincular-clientes {--limit=100 : Número máximo de conversaciones a procesar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vincula automáticamente las conversaciones de ElevenLabs con clientes basándose en números de teléfono parseados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔗 Iniciando vinculación de conversaciones con clientes...');

        // Obtener conversaciones sin client_id asignado pero con número
        $limit = $this->option('limit');
        $conversaciones = ElevenlabsConversation::whereNull('client_id')
            ->whereNotNull('numero')
            ->orderBy('conversation_date', 'desc')
            ->limit($limit)
            ->get();

        if ($conversaciones->isEmpty()) {
            $this->info('✅ No hay conversaciones sin vincular');
            return 0;
        }

        $this->info("📞 Procesando {$conversaciones->count()} conversaciones...");

        // Obtener todos los clientes con teléfono para comparación
        $clientes = Client::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($clientes->isEmpty()) {
            $this->warn('⚠️  No hay clientes con teléfono en la base de datos');
            return 1;
        }

        $this->info("👥 Clientes con teléfono: {$clientes->count()}");

        // Crear un mapa de números parseados a clientes
        $mapaNumeros = [];
        foreach ($clientes as $cliente) {
            $numeroParsed = $this->parsearNumero($cliente->phone);
            if ($numeroParsed) {
                $mapaNumeros[$numeroParsed] = $cliente->id;
            }
        }

        $this->info("🗺️  Mapa de números creado con " . count($mapaNumeros) . " números únicos");

        $bar = $this->output->createProgressBar($conversaciones->count());
        $bar->start();

        $vinculadas = 0;
        $noEncontradas = 0;

        foreach ($conversaciones as $conversacion) {
            try {
                // Parsear el número de la conversación
                $numeroConversacion = $this->parsearNumero($conversacion->numero);

                if ($numeroConversacion && isset($mapaNumeros[$numeroConversacion])) {
                    // Vincular con el cliente
                    $conversacion->client_id = $mapaNumeros[$numeroConversacion];
                    $conversacion->save();

                    $vinculadas++;
                    Log::info("Conversación {$conversacion->conversation_id} vinculada con cliente {$mapaNumeros[$numeroConversacion]}");
                } else {
                    $noEncontradas++;
                }
            } catch (\Exception $e) {
                Log::error("Error vinculando conversación {$conversacion->conversation_id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Resumen
        $this->info("✅ Conversaciones vinculadas: {$vinculadas}");
        if ($noEncontradas > 0) {
            $this->warn("⚠️  Sin coincidencias: {$noEncontradas}");
        }

        Log::info("VincularConversacionesConClientes: Vinculadas={$vinculadas}, NoEncontradas={$noEncontradas}");

        return 0;
    }

    /**
     * Parsear número de teléfono a formato estándar
     * Convierte cualquier formato a: +34XXXXXXXXX
     */
    private function parsearNumero($telefono)
    {
        if (empty($telefono)) {
            return null;
        }

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

        return null;
    }
}

