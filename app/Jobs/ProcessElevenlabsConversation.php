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

    /**
     * Número de veces que se reintentará el job si falla
     */
    public $tries = 3;

    /**
     * Tiempo máximo de ejecución del job (en segundos)
     */
    public $timeout = 120;

    /**
     * La conversación a procesar
     */
    protected $conversationId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $conversationId)
    {
        $this->conversationId = $conversationId;
    }

    /**
     * Execute the job.
     */
    public function handle(ElevenlabsAIService $aiService): void
    {
        try {
            $conversation = ElevenlabsConversation::find($this->conversationId);

            if (!$conversation) {
                Log::error('Conversación no encontrada para procesar', [
                    'conversation_id' => $this->conversationId,
                ]);
                return;
            }

            // Verificar si la conversación tiene transcripción
            if (empty($conversation->transcript)) {
                Log::warning('Conversación sin transcripción', [
                    'conversation_id' => $conversation->conversation_id,
                ]);
                $conversation->markAsFailed();
                return;
            }

            // Verificar si ya está procesada
            if ($conversation->isProcessed()) {
                Log::info('Conversación ya procesada, omitiendo', [
                    'conversation_id' => $conversation->conversation_id,
                ]);
                return;
            }

            Log::info('Iniciando procesamiento de conversación', [
                'conversation_id' => $conversation->conversation_id,
                'transcript_length' => strlen($conversation->transcript),
            ]);

            // Procesar la conversación (categorizar + resumir)
            $success = $aiService->processConversation($conversation);

            if ($success) {
                Log::info('Conversación procesada exitosamente', [
                    'conversation_id' => $conversation->conversation_id,
                    'category' => $conversation->category,
                    'confidence' => $conversation->confidence_score,
                ]);
            } else {
                Log::error('Error al procesar conversación', [
                    'conversation_id' => $conversation->conversation_id,
                ]);
                
                // El job fallará y se reintentará automáticamente
                throw new Exception('Error en el procesamiento de la conversación');
            }

        } catch (Exception $e) {
            Log::error('Excepción en ProcessElevenlabsConversation', [
                'conversation_id' => $this->conversationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Marcar conversación como fallida
            $conversation = ElevenlabsConversation::find($this->conversationId);
            if ($conversation) {
                $conversation->markAsFailed();
            }

            throw $e;
        }
    }

    /**
     * Manejar el fallo del job
     */
    public function failed(Exception $exception): void
    {
        Log::error('Job ProcessElevenlabsConversation falló definitivamente', [
            'conversation_id' => $this->conversationId,
            'error' => $exception->getMessage(),
        ]);

        // Marcar la conversación como fallida
        $conversation = ElevenlabsConversation::find($this->conversationId);
        if ($conversation) {
            $conversation->markAsFailed();
        }
    }
}

