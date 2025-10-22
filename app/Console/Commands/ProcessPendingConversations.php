<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use App\Jobs\ProcessElevenlabsConversation;

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

        $bar = $this->output->createProgressBar($pending->count());
        $bar->start();

        foreach ($pending as $conversation) {
            ProcessElevenlabsConversation::dispatch($conversation->id);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('✅ Jobs despachados a la cola');
        $this->info('💡 Ejecuta en otra terminal: php artisan queue:work');
        $this->newLine();
        $this->info('📊 Puedes ver el progreso en /elevenlabs/dashboard');

        return Command::SUCCESS;
    }
}
