<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\Logs\LogActions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class LogActionsController extends Controller
{
    public function index()
    {
        $logActions = LogActions::all();

        return view('logs.index', compact('logActions'));
    }

    public function iaClasificacion(Request $request){

        $fecha = $request->fecha ?? Carbon::today();

        $logActions = LogActions::where('tipo',1)->whereDate('created_at',$fecha)->get();
        //mando logs para que el modelo de openai pueda procesarlos y regresar un resultado clasificado por categoria de cambio y usuario
        $logActions = $logActions->toArray();
        $logActions = json_encode($logActions);

        $token = env('OPENAI_API_KEY');
        $url = 'https://api.openai.com/v1/chat/completions';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        );

        $data = array(
            "model" => "gpt-4o",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => 'Analiza el siguiente JSON que contiene registros de acciones de actualización y creacion. Clasifica y agrupa las actualizaciones de acuerdo con el usuario que realizó la acción. Para cada actualización, identifica los valores antiguos y nuevos involucrados, extraídos del campo de descripción, que sigue el formato DE (valor antiguo) a (valor nuevo).
                                        Estructura la respuesta de manera que para cada usuario se liste cada campo que fue actualizado y sus respectivos valores antiguos y nuevos.Dame solo la clasificacion sin mas texto.
                                        Entrega el resumen en formato JSON agrupado por usuario, y lista los campos actualizados, junto con sus valores antiguos y nuevos para cada actualización.'
                        ],
                        [
                            "type" => "text",
                            "text" =>  $logActions
                        ],
                    ]
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

        $response = curl_exec($curl);
        curl_close($curl);

        // Decodificar la respuesta JSON
        $response_data = json_decode($response, true);

        $content  = $response_data['choices'][0]['message']['content'];
        $content = str_replace(['```json', '```'], '', $content);
        $jsonContent = json_decode($content, true);

        return $jsonContent;

    }


    public function chatgpt($texto){

        $token = env('OPENAI_API_KEY');

        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        );

        // Construir el contenido del mensaje que incluye la imagen en base64, paises y tipos de documento como texto
        $data = array(
            "model" => "gpt-4",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "Analiza esta transcripción de una reunión del equipo Hawkins, que puede involucrar tanto a miembros internos como a clientes. Elabora un resumen conciso que destaque únicamente los temas discutidos y los puntos clave mencionados durante la reunión. Asegúrate de excluir cualquier información confidencial, sensible o relacionada con temas ilegales. El resumen debe ser claro, preciso y enfocado únicamente en los aspectos relevantes de la conversación."
                        ],
                        [
                            "type" => "text",
                            "text" => "Texto de la reunion: " . $texto
                        ],
                    ]
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
        curl_close($curl);

        // Decodificar la respuesta JSON
        $response_data = json_decode($response, true);

        return $response_data;
    }

}
