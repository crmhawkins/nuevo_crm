<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use App\Services\ElevenlabsAIService;
use Illuminate\Support\Facades\Log;

class ProcessPendingConversations extends Command
{
    protected $signature = 'elevenlabs:process
                            {--limit=50 : Número máximo de conversaciones a procesar}';

    protected $description = 'Procesar conversaciones pendientes con IA local';

    public function handle()
    {
        $this->info('🤖 Procesando conversaciones pendientes con IA...');
        $this->newLine();

        $limit = (int) $this->option('limit');

        $pending = ElevenlabsConversation::pending()
            ->whereNotNull('transcript')
            ->limit($limit)
            ->get();

        if ($pending->isEmpty()) {
            $this->info('ℹ️ No hay conversaciones pendientes para procesar');
            return Command::SUCCESS;
        }

        $this->info("📝 Se procesarán {$pending->count()} conversaciones con IA");
        $this->newLine();

        $aiService = app(ElevenlabsAIService::class);
        $processed = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($pending->count());
        $bar->start();

        foreach ($pending as $conversation) {
            try {
                $result = $aiService->processConversation($conversation);
                if ($result) {
                    $processed++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $failed++;
                Log::error('❌ Error procesando conversación en comando elevenlabs:process', [
                    'conversation_id' => $conversation->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('✅ Procesamiento finalizado');
        $this->table([
            'Total',
            'Procesadas',
            'Fallidas',
        ], [[
            $pending->count(),
            $processed,
            $failed,
        ]]);

        if ($failed > 0) {
            $this->warn('⚠️ Algunas conversaciones no pudieron procesarse. Revisa el log.');
        }

        return Command::SUCCESS;
    }
}
