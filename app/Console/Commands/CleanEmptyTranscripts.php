<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use Illuminate\Support\Facades\Log;

class CleanEmptyTranscripts extends Command
{
    protected $signature = 'elevenlabs:clean-empty-transcripts';
    protected $description = 'Eliminar conversaciones con transcript vacío de la base de datos';

    public function handle()
    {
        $this->info('🧹 Buscando conversaciones con transcript vacío...');
        
        // Buscar conversaciones con transcript vacío o null
        $emptyConversations = ElevenlabsConversation::where(function($query) {
            $query->whereNull('transcript')
                  ->orWhere('transcript', '')
                  ->orWhere('transcript', ' ')
                  ->orWhere('transcript', "\n")
                  ->orWhere('transcript', "\r\n");
        })->get();

        $count = $emptyConversations->count();
        
        if ($count === 0) {
            $this->info('✅ No se encontraron conversaciones con transcript vacío.');
            return 0;
        }

        $this->warn("⚠️  Se encontraron {$count} conversaciones con transcript vacío.");
        $this->newLine();
        
        if (!$this->confirm('¿Deseas eliminarlas?', true)) {
            $this->info('Operación cancelada.');
            return 0;
        }

        $this->info('🗑️  Eliminando conversaciones...');
        $progressBar = $this->output->createProgressBar($count);
        
        $deleted = 0;
        foreach ($emptyConversations as $conversation) {
            try {
                Log::info('CleanEmptyTranscripts: Eliminando conversación', [
                    'conversation_id' => $conversation->conversation_id,
                    'agent_id' => $conversation->agent_id,
                    'transcript_length' => strlen($conversation->transcript ?? '')
                ]);
                
                $conversation->delete();
                $deleted++;
                $progressBar->advance();
            } catch (\Exception $e) {
                Log::error('CleanEmptyTranscripts: Error eliminando conversación', [
                    'conversation_id' => $conversation->conversation_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("═══════════════════════════════════════════");
        $this->info("✅ Limpieza completada");
        $this->info("   🗑️  Eliminadas: {$deleted}");
        $this->info("═══════════════════════════════════════════");
        
        Log::info('CleanEmptyTranscripts: Limpieza completada', [
            'total_found' => $count,
            'deleted' => $deleted
        ]);

        return 0;
    }
}

