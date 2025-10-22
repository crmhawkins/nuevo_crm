<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ElevenlabsConversation;
use App\Services\ElevenlabsAIService;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessElevenlabsConversation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    protected $conversationId;

    public function __construct(int $conversationId)
    {
        $this->conversationId = $conversationId;
    }

    public function handle(ElevenlabsAIService $aiService): void
    {
        try {
            $conversation = ElevenlabsConversation::find($this->conversationId);

            if (!$conversation) {
                Log::error('❌ Conversación no encontrada', [
                    'conversation_id' => $this->conversationId,
                ]);
                return;
            }

            if (empty($conversation->transcript)) {
                Log::warning('⚠️ Sin transcripción', [
                    'conversation_id' => $conversation->conversation_id,
                ]);
                $conversation->markAsFailed();
                return;
            }

            if ($conversation->isProcessed()) {
                Log::info('ℹ️ Ya procesada, omitiendo', [
                    'conversation_id' => $conversation->conversation_id,
                ]);
                return;
            }

            Log::info('🚀 Procesando conversación', [
                'conversation_id' => $conversation->conversation_id,
            ]);

            $success = $aiService->processConversation($conversation);

            if (!$success) {
                throw new Exception('Error en el procesamiento');
            }

        } catch (Exception $e) {
            Log::error('❌ Excepción en ProcessElevenlabsConversation', [
                'conversation_id' => $this->conversationId,
                'error' => $e->getMessage(),
            ]);

            $conversation = ElevenlabsConversation::find($this->conversationId);
            if ($conversation) {
                $conversation->markAsFailed();
            }

            throw $e;
        }
    }

    public function failed(Exception $exception): void
    {
        Log::error('❌ Job falló definitivamente', [
            'conversation_id' => $this->conversationId,
            'error' => $exception->getMessage(),
        ]);

        $conversation = ElevenlabsConversation::find($this->conversationId);
        if ($conversation) {
            $conversation->markAsFailed();
        }
    }
}
