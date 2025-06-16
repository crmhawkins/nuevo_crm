<?php

namespace App\Console\Commands;

use App\Models\Clients\Client;
use App\Models\Plataforma\MensajesPendientes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Plataforma\CampaniasWhatsapp;
use App\Models\Plataforma\ModeloMensajes;

class GenerarMensajeCampania extends Command
{
    protected $signature = 'Whatsapp:GenerarMensajeCampania';
    protected $description = 'Genera 1 mensaje de WhatsApp para cada campaña que tenga menos de 10 modelos generados';

    private function replaceTemplateVariables($message, $client = null)
    {
        $variables = [
            '{cliente}' => $client ? $client->name : '',
            '{fecha}' => now()->format('d/m/Y'),
            '{telefono}' => $client ? $client->phone : '',
            '{email}' => $client ? $client->email : '',
            '{direccion}' => $client ? $client->address : '',
        ];

        return str_replace(array_keys($variables), array_values($variables), $message);
    }

    public function handle()
    {
        // Buscar campañas con menos de 10 mensajes
        $campanias = CampaniasWhatsapp::all()->filter(function ($campania) {
            return ModeloMensajes::where('campania_id', $campania->id)->count() < 10;
        });

        if ($campanias->isEmpty()) {
            $this->info("No hay campañas pendientes de generar mensajes.");
            return 0;
        }

        foreach ($campanias as $campania) {
            $generados = ModeloMensajes::where('campania_id', $campania->id)->count();
            if ($generados >= 10) {
                continue;
            }

            $prompt = "Cambia este mensaje de whatsapp, sigue estas pautas:
            - El mensaje debe ser en el mismo idioma que te llegó el mensaje original
            - El mensaje debe ser formato Whatsapp, como el que te llega.
            - El mensaje no debe cambiar su estructura ni significado. Debe decir lo mismo de otra forma.
            - No divagues, no inventes y no añadas nada.
            - Si encuentras cosas como {cliente} {fecha} no lo elimines, puedes moverlo pero son variables que se rellenaran mas adelante";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt . "\n\n" . $campania->mensaje,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 5000,
            ]);

            if ($response->successful()) {
                $mensajeGenerado = $response->json()['choices'][0]['message']['content'];

                ModeloMensajes::create([
                    'mensaje' => $mensajeGenerado,
                    'campania_id' => $campania->id,
                ]);

                $this->info("Mensaje generado para campaña ID {$campania->id}.");

                // Verificamos si ya hay 10 generados
                $totalGenerados = ModeloMensajes::where('campania_id', $campania->id)->count();
                if ($totalGenerados == 10) {
                    $this->info("Se han generado los 10 mensajes. Creando mensajes pendientes...");

                    $mensajes = ModeloMensajes::where('campania_id', $campania->id)->pluck('mensaje', 'id')->toArray();

                    $clientes = $campania->clientes; // array de IDs

                    foreach ($clientes as $index => $clienteId) {
                        $this->info("Cliente ID: {$clienteId}");
                        $cliente = Client::find($clienteId);
                        $this->info("Creando mensajes pendientes para el cliente {$cliente->id}...");
                        if (!$cliente) continue;
                        $this->info("Cliente encontrado: {$cliente->id}");
                        // Formatear teléfono
                        $telefono = str_replace([' ', '+'], '', $cliente->phone ?? '');
                        if (empty($telefono) || $telefono[0] === '9' || $telefono[0] === '8') {
                            continue;
                        }
                        $telefono = '34' . $telefono;
                        $this->info("Teléfono formateado: {$telefono}");
                        // Obtener modelo cíclico
                        $modeloIds = array_keys($mensajes);
                        $modeloId = $modeloIds[$index % 10];
                        $mensajeTexto = $mensajes[$modeloId];
                        $this->info("Mensaje cíclico: {$mensajeTexto}");
                        $message = $this->replaceTemplateVariables($mensajeTexto, $cliente);
                        MensajesPendientes::create([
                            'tlf' => $telefono,
                            'message' => $message,
                            'modelo_id' => $modeloId,
                            'client_id' => $clienteId,
                            'status' => 0,
                        ]);
                    }
                    $campania->estado = 3;
                    $campania->save();
                    $this->info("Mensajes pendientes creados para campaña ID {$campania->id}.");
                }

                return 0;
            }

        }
    }
}