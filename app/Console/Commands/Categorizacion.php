<?php

namespace App\Console\Commands;

use App\Models\Email\Email;

use App\Models\Email\CategoryEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Categorizacion extends Command
{
    protected $signature = 'correos:categorizacion';
    protected $description = 'Categoriza los correos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $categorias = CategoryEmail::where('id', '!=' ,6)->get(); // Obtener categorías excepto la categoría 6
        $emails = Email::whereNull('category_id')->get(); // Obtener correos sin categoría

        foreach($emails as $email){
            // Convertir las categorías en una lista legible para OpenAI
            $categoria_list = $categorias->map(function ($categoria) {
                return $categoria->id . ': ' . $categoria->name;
            })->implode(', ');
            // Enviar el correo y las categorías a OpenAI para obtener la categoría
            $respuesta = $this->chatGptModelo($email->body, $categoria_list);

            // Asignar la categoría obtenida al correo
            if ($respuesta != 'NULL') {
                $email->category_id = $respuesta; // Asegúrate de que 'respuesta' sea un ID válido
                $email->save();
            }
        }

        $this->info('Comando completado: Correos categorizados.');
    }

    public function chatGptModelo($correo, $categorias) {
        $token = env('OPENAI_API_KEY', 'valorPorDefecto');

        // Configurar los parámetros de la solicitud
        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        );

        // Formato del mensaje enviado a OpenAI
        $data = array(
            "model" => "gpt-4",
            "messages" => [
                [
                    "role" => "user",
                    "content" => 'Analiza el contenido de un correo y categorizalo dentro de estas categorías: ' . $categorias . '. Dame solo el id de la categoría unicamente el numero nunca me des el nombre de la categoria solo el id , a la que pertenece este correo: "' . $correo . '" si el correo no tiene contenido o no sabes en que categoría debe estar, responde con categoria otros'
                ]
            ]
        );

        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Verificar si la respuesta fue exitosa (código 200)
        if ($httpCode === 200) {
            $response_data = json_decode($response, true);

            if (isset($response_data['choices'][0]['message']['content'])) {
                // Guardar la respuesta en un archivo para depuración
                Storage::disk('local')->put('Respuesta_Peticion_ChatGPT-Model.txt', $response_data['choices'][0]['message']['content']);

                // Devolver el contenido (ID de la categoría)
                return trim($response_data['choices'][0]['message']['content']);
            }
        }

        // Si la solicitud falló o no hay respuesta, registrar un error
        Storage::disk('local')->put('Error_Peticion_ChatGPT.txt', json_encode($response));

        return null; // Devolver null en caso de fallo
    }
}

