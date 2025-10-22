<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElevenlabsService;
use App\Models\ElevenlabsSyncLog;
use App\Models\ElevenlabsConversation;
use App\Jobs\ProcessElevenlabsConversation;
use Carbon\Carbon;
use Exception;

class SyncElevenlabsConversations extends Command
{
    protected $signature = 'elevenlabs:sync 
                            {--from= : Fecha desde (YYYY-MM-DD)}
                            {--limit=10 : Máximo de páginas a sincronizar}
                            {--no-process : No procesar con IA automáticamente}';

    protected $description = 'Sincronizar conversaciones de Eleven Labs (MANUAL)';

    public function handle(ElevenlabsService $elevenlabsService)
    {
        $this->info('🔄 Sincronización MANUAL de Eleven Labs');
        $this->newLine();

        // Verificar configuración
        if (!config('elevenlabs.api_key')) {
            $this->error('❌ API Key no configurada en .env');
            return Command::FAILURE;
        }

        $this->info('✅ API Key: ' . substr(config('elevenlabs.api_key'), 0, 10) . '...');
        $this->newLine();

        $syncLog = ElevenlabsSyncLog::create([
            'sync_started_at' => now(),
            'status' => 'running',
        ]);

        try {
            $maxPages = (int) $this->option('limit');
            
            // Convertir fecha a timestamp Unix si se proporciona
            $fromTimestamp = null;
            $fromDate = null;
            
            if ($this->option('from')) {
                $fromDate = Carbon::parse($this->option('from'));
                $fromTimestamp = $fromDate->timestamp;
                $this->info("📅 Sincronizando desde: {$fromDate->format('Y-m-d H:i:s')}");
                $this->info("🔢 Timestamp Unix: {$fromTimestamp}");
            } else {
                $this->info("📅 Sincronizando TODAS las conversaciones disponibles");
            }
            
            $this->info("📄 Máximo de páginas: {$maxPages}");
            $this->newLine();

            // Probar conexión
            $this->info('🔍 Verificando conexión...');
            if (!$elevenlabsService->testConnection()) {
                throw new Exception('No se pudo conectar con Eleven Labs API');
            }
            $this->info('✅ Conexión exitosa');
            $this->newLine();

            // Sincronizar
            $this->info('⬇️ Descargando conversaciones...');
            $stats = $elevenlabsService->syncConversations($fromTimestamp, $maxPages);

            $this->newLine();
            $this->info('📊 Estadísticas:');
            $this->table(
                ['Métrica', 'Cantidad'],
                [
                    ['Total sincronizadas', $stats['total']],
                    ['Nuevas', $stats['new']],
                    ['Actualizadas', $stats['updated']],
                ]
            );

            $syncLog->update([
                'conversations_synced' => $stats['total'],
                'conversations_new' => $stats['new'],
                'conversations_updated' => $stats['updated'],
            ]);

            // Procesar si no se especifica --no-process
            if (!$this->option('no-process')) {
                $this->processConversations();
            }

            $syncLog->markAsCompleted();
            $this->info('✅ Sincronización completada');
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $syncLog->markAsFailed($e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function processConversations(): void
    {
        $this->newLine();
        $this->info('🤖 Procesando con IA...');

        $pending = ElevenlabsConversation::pending()
            ->whereNotNull('transcript')
            ->get();

        if ($pending->isEmpty()) {
            $this->info('ℹ️ No hay conversaciones para procesar');
            return;
        }

        $this->info("📝 Procesando {$pending->count()} conversaciones");
        $bar = $this->output->createProgressBar($pending->count());
        $bar->start();

        foreach ($pending as $conversation) {
            ProcessElevenlabsConversation::dispatch($conversation->id);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('✅ Jobs despachados a la cola');
        $this->info('💡 Ejecuta: php artisan queue:work');
    }
}
