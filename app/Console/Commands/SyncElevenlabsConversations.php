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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elevenlabs:sync 
                            {--from= : Fecha desde la cual sincronizar (YYYY-MM-DD)}
                            {--force : Forzar sincronización completa}
                            {--no-process : No procesar automáticamente las conversaciones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar conversaciones desde Eleven Labs API';

    /**
     * Execute the console command.
     */
    public function handle(ElevenlabsService $elevenlabsService)
    {
        $this->info('🔄 Iniciando sincronización de conversaciones de Eleven Labs...');
        $this->newLine();

        // Crear registro de sincronización
        $syncLog = ElevenlabsSyncLog::create([
            'sync_started_at' => now(),
            'status' => 'running',
        ]);

        try {
            // Determinar fecha de inicio
            $fromDate = $this->getFromDate();
            
            if ($fromDate) {
                $this->info("📅 Sincronizando desde: {$fromDate->format('Y-m-d H:i:s')}");
            } else {
                $this->info("📅 Sincronizando todas las conversaciones");
            }
            
            $this->newLine();

            // Probar conexión
            $this->info('🔍 Verificando conexión con Eleven Labs API...');
            if (!$elevenlabsService->testConnection()) {
                throw new Exception('No se pudo conectar con la API de Eleven Labs');
            }
            $this->info('✅ Conexión exitosa');
            $this->newLine();

            // Sincronizar conversaciones
            $this->info('⬇️  Descargando conversaciones...');
            $progressBar = $this->output->createProgressBar();
            $progressBar->start();

            $stats = $elevenlabsService->syncConversations($fromDate);

            $progressBar->finish();
            $this->newLine(2);

            // Actualizar log de sincronización
            $syncLog->update([
                'conversations_synced' => $stats['total'],
                'conversations_new' => $stats['new'],
                'conversations_updated' => $stats['updated'],
            ]);

            // Mostrar estadísticas
            $this->info('📊 Estadísticas de sincronización:');
            $this->table(
                ['Métrica', 'Cantidad'],
                [
                    ['Total sincronizadas', $stats['total']],
                    ['Nuevas', $stats['new']],
                    ['Actualizadas', $stats['updated']],
                ]
            );
            $this->newLine();

            // Procesar conversaciones si está habilitado
            if (!$this->option('no-process') && config('elevenlabs.auto_process', true)) {
                $this->processConversations();
            }

            // Marcar sincronización como completada
            $syncLog->markAsCompleted();

            $this->info('✅ Sincronización completada exitosamente!');
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Error durante la sincronización: ' . $e->getMessage());
            
            // Marcar sincronización como fallida
            $syncLog->markAsFailed($e->getMessage());
            
            return Command::FAILURE;
        }
    }

    /**
     * Obtener fecha desde la cual sincronizar
     */
    protected function getFromDate(): ?Carbon
    {
        if ($this->option('force')) {
            return null; // Sincronizar todo
        }

        if ($this->option('from')) {
            try {
                return Carbon::parse($this->option('from'));
            } catch (Exception $e) {
                $this->warn('⚠️  Fecha inválida, usando fecha por defecto');
            }
        }

        // Por defecto, sincronizar desde la última sincronización exitosa
        $lastSync = ElevenlabsSyncLog::completed()
            ->orderBy('sync_finished_at', 'desc')
            ->first();

        if ($lastSync) {
            return $lastSync->sync_finished_at;
        }

        // Si no hay sincronización previa, sincronizar último mes
        return now()->subMonth();
    }

    /**
     * Procesar conversaciones pendientes
     */
    protected function processConversations(): void
    {
        $this->newLine();
        $this->info('🤖 Procesando conversaciones con IA...');

        $pendingConversations = ElevenlabsConversation::pending()
            ->whereNotNull('transcript')
            ->get();

        if ($pendingConversations->isEmpty()) {
            $this->info('ℹ️  No hay conversaciones pendientes de procesar');
            return;
        }

        $this->info("📝 Se procesarán {$pendingConversations->count()} conversaciones");
        
        $progressBar = $this->output->createProgressBar($pendingConversations->count());
        $progressBar->start();

        foreach ($pendingConversations as $conversation) {
            // Despachar job para procesar en background
            ProcessElevenlabsConversation::dispatch($conversation->id);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        
        $this->info('✅ Jobs de procesamiento despachados a la cola');
        $this->info('💡 Tip: Ejecuta "php artisan queue:work" para procesar los jobs');
    }
}

