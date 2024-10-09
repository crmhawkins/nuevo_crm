<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\Logs\LogActions;
use App\Models\Users\User;
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
                                        Entrega el resumen en formato JSON agrupado por usuario, y lista los campos actualizados, junto con sus valores antiguos y nuevos para cada actualización. para la respuesta usa el sigiente formato:
                                        {
                                            "101": {
                                                "estado": [
                                                {
                                                    "antiguo": "TRAMITADA",
                                                    "nuevo": "APORTAR DOCUMENTACION"
                                                },
                                                {
                                                    "antiguo": "TRAMITADA",
                                                    "nuevo": "APORTAR DOCUMENTACION"
                                                },
                                                {
                                                    "antiguo": "JUSTIFICADO",
                                                    "nuevo": "PENDIENTE SUBSANAR 1"
                                                },
                                                {
                                                    "antiguo": "PENDIENTE SUBSANAR 1",
                                                    "nuevo": "APORTAR DOCUMENTACION"
                                                },

                                                ],
                                                "comentario": [
                                                {
                                                    "antiguo": "TRAMITADA POR PITI",
                                                    "nuevo": ""
                                                },
                                                {
                                                    "antiguo": "",
                                                    "nuevo": "Listado de clientes Emma.\nhan creado una nueva Sociedad y no tienen la antigüedad de 6 meses hasta el día 1 de Julio. Llamar."
                                                },
                                                {
                                                    "antiguo": "",
                                                    "nuevo": "Certificado de situación en el censo de actividades económicas"
                                                },
                                                {
                                                    "antiguo": "Resolución concesión: 16/09 - SIN IVA\nBono de horas (quiere más adelante Factura electrónica, Ángel nos avisa)",
                                                    "nuevo": "Resolución concesión: 16/09 - SIN IVA \nPara justificar"
                                                }
                                                ],
                                                "nuevo_comentario": [
                                                {
                                                    "antiguo": "",
                                                    "nuevo": "sonia@asesoriaasela.es\n667 46 09 42"
                                                },
                                                {
                                                    "antiguo": "sonia@asesoriaasela.es\n667 46 09 42",
                                                    "nuevo": "sonia@asesoriaasela.es\n667 46 09 42\nModelos 200 tres últimos ejercicios"
                                                },
                                                {
                                                    "antiguo": "ANTONIACRUZZY@HOTMAIL.COM\nTarea justificación Alejandro \nOrdenador 1.500€",
                                                    "nuevo": "ANTONIACRUZZY@HOTMAIL.COM\nPte Factura Electrónica\nOrdenador 1.500€"
                                                }
                                                ],
                                            },
                                            "8": {
                                                "estado": [
                                                {
                                                    "antiguo": "VALIDADAS 2ª JUSTIFICACION",
                                                    "nuevo": "PAGADA 2º JUSTIFICACIÓN"
                                                },
                                                {
                                                    "antiguo": "VALIDADAS 2ª JUSTIFICACION",
                                                    "nuevo": "PAGADA 2º JUSTIFICACIÓN"
                                                }
                                                ],
                                                "fecha_actualizacion": [
                                                {
                                                    "antiguo": "2024-09-23",
                                                    "nuevo": "2024-10-08"
                                                },
                                                {
                                                    "antiguo": "2024-09-18",
                                                    "nuevo": "2024-10-09"
                                                }
                                                ]
                                            },
                                            "10": {
                                                "nuevo_comentario": [
                                                {
                                                    "antiguo": "Post-venta: No llamar",
                                                    "nuevo": ""
                                                },
                                                {
                                                    "antiguo": "",
                                                    "nuevo": "consulting ? llamar"
                                                },
                                                {
                                                    "antiguo": "alba gallego",
                                                    "nuevo": ""
                                                }
                                                ]
                                            },
                                            "2": {
                                                "nuevo_comentario": [
                                                {
                                                    "antiguo": "Post-venta: No llamar",
                                                    "nuevo": "Manolo llama a Carmen"
                                                }
                                                ]
                                            },
                                        }'
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
        $clasificacion = json_decode($content, true);
        $usuarios = User::get()->keyBy('id');


        return view('logs.clasificacion', compact('clasificacion','usuarios'));

    }



}
