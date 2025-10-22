<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use App\Services\ElevenlabsAIService;
use Illuminate\Support\Facades\Log;

class ReprocessScheduledCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elevenlabs:reprocess-scheduled-calls 
                            {--all : Reprocesar todas las llamadas agendadas}
                            {--from-date= : Reprocesar desde una fecha específica (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocesa las conversaciones con llamadas agendadas para corregir las horas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Reprocesando llamadas agendadas...');
        $this->newLine();

        $all = $this->option('all');
        $fromDate = $this->option('from-date');

        // Construir query
        $query = ElevenlabsConversation::where('sentiment_category', 'llamada_agendada')
            ->whereNotNull('scheduled_call_datetime');

        if ($fromDate && !$all) {
            $query->where('conversation_date', '>=', $fromDate);
            $this->info("📅 Filtrando desde: {$fromDate}");
        } elseif (!$all) {
            // Por defecto, últimos 7 días
            $query->where('conversation_date', '>=', now()->subDays(7));
            $this->info("📅 Filtrando: Últimos 7 días");
        } else {
            $this->info("📅 Procesando: TODAS las llamadas agendadas");
        }

        $conversations = $query->get();
        $total = $conversations->count();

        if ($total === 0) {
            $this->info('✅ No hay llamadas agendadas que reprocesar');
            return 0;
        }

        $this->info("📊 Total de llamadas agendadas a reprocesar: {$total}");
        $this->newLine();

        if (!$this->confirm('¿Deseas continuar con el reprocesamiento?', true)) {
            $this->warn('❌ Operación cancelada');
            return 0;
        }

        $this->newLine();
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $aiService = app(ElevenlabsAIService::class);
        $updated = 0;
        $unchanged = 0;
        $failed = 0;

        foreach ($conversations as $conversation) {
            try {
                $oldDateTime = $conversation->scheduled_call_datetime ? 
                    $conversation->scheduled_call_datetime->format('Y-m-d H:i:s') : null;

                // Extraer información de cita agendada de nuevo
                $scheduledData = $this->extractScheduledCallInfo(
                    $aiService, 
                    $conversation->transcript,
                    $conversation->conversation_date
                );

                if ($scheduledData && $scheduledData['datetime']) {
                    $newDateTime = $scheduledData['datetime'];
                    
                    // Solo actualizar si cambió
                    if ($oldDateTime !== $newDateTime) {
                        $conversation->scheduled_call_datetime = $newDateTime;
                        $conversation->scheduled_call_notes = $scheduledData['notes'] ?? $conversation->scheduled_call_notes;
                        $conversation->save();
                        
                        $updated++;
                        
                        Log::info('✅ Cita reprocesada', [
                            'conversation_id' => $conversation->conversation_id,
                            'old_datetime' => $oldDateTime,
                            'new_datetime' => $newDateTime,
                        ]);
                    } else {
                        $unchanged++;
                    }
                } else {
                    $unchanged++;
                }

                $progressBar->advance();
            } catch (\Exception $e) {
                $failed++;
                Log::error('❌ Error reprocesando cita', [
                    'conversation_id' => $conversation->conversation_id,
                    'error' => $e->getMessage(),
                ]);
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Resumen
        $this->info('✅ Reprocesamiento completado');
        $this->newLine();
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total procesadas', $total],
                ['Actualizadas (hora corregida)', $updated],
                ['Sin cambios', $unchanged],
                ['Fallos', $failed],
            ]
        );

        if ($updated > 0) {
            $this->newLine();
            $this->warn("⚠️  Se actualizaron {$updated} llamadas agendadas con nuevas horas.");
            $this->info("💡 Revisa el log para ver los cambios específicos.");
        }

        return 0;
    }

    /**
     * Extraer información de llamada agendada usando el servicio de IA
     */
    protected function extractScheduledCallInfo($aiService, string $transcript, $conversationDate): ?array
    {
        // Usar reflexión para acceder al método protegido
        $reflection = new \ReflectionClass($aiService);
        $method = $reflection->getMethod('extractScheduledCallInfo');
        $method->setAccessible(true);
        
        return $method->invoke($aiService, $transcript, $conversationDate);
    }
}

