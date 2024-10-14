<?php

namespace App\Http\Controllers\Whatsapp;

use App\Models\Clients\Client;
use App\Models\Whatsapp\Mensaje;
use App\Models\Whatsapp\RespuestasMensajes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{

    public function hookWhatsapp(Request $request) {
        $responseJson = env('WHATSAPP_KEY', 'valorPorDefecto');

            $query = $request->all();
            $mode = $query['hub_mode'];
            $token = $query['hub_verify_token'];
            $challenge = $query['hub_challenge'];

            // Formatear la fecha y hora actual
            $dateTime = Carbon::now()->format('Y-m-d_H-i-s'); // Ejemplo de formato: 2023-11-13_15-30-25

            // Crear un nombre de archivo con la fecha y hora actual
            $filename = "hookWhatsapp_{$dateTime}.txt";

            Storage::disk('local')->put($filename, json_encode($request->all()));

            return response($challenge, 200)->header('Content-Type', 'text/plain');
    }

    public function processHookWhatsapp(Request $request) {

        $data = json_decode($request->getContent(), true);

        $tipo = $data['entry'][0]['changes'][0]['value']['messages'][0]['type'];

        if ($tipo == 'audio') {
            $this->audioMensaje($data);
        }elseif($tipo == 'image') {
            $this->imageMensaje($data);
        }else {
            $this->textMensaje($data);
        }
        return response(200)->header('Content-Type', 'text/plain');

    }

    public function textMensaje( $data )
    {
        $fecha = Carbon::now()->format('Y-m-d_H-i-s');

        Storage::disk('local')->put('Mensaje_Texto_Reicibido-'.$fecha.'.txt', json_encode($data) );

        // Whatsapp::create(['mensaje' => json_encode($data)]);
        $id = $data['entry'][0]['changes'][0]['value']['messages'][0]['id'];
        $phone = $data['entry'][0]['changes'][0]['value']['messages'][0]['from'];
        $mensaje = $data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];

        $mensajeExiste = Mensaje::where( 'id_mensaje', $id )->get();
        if (count($mensajeExiste) > 0) {
            return response(400)->header('Content-Type', 'text/plain');

        }else {

            $isAutomatico = Mensaje::where('remitente', $phone)
            ->where('is_automatic', true)
            ->where('mensaje', null)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->orderBy('created_at', 'desc')
            ->first();

            if ($isAutomatico != null) {
                // $dataRegistrar = [
                //     'id_mensaje' => $id,
                //     'id_three' => null,
                //     'remitente' => $phone,
                //     'mensaje' => $mensaje,
                //     'respuesta' => null,
                //     'status' => 1,
                //     'status_mensaje' => 0,
                //     'type' => 'text',
                //     'date' => Carbon::now()
                // ];
                // $mensajeCreado = Mensaje::create($dataRegistrar);
                $reponseChatGPT1 = $this->chatGptModelo($mensaje,$id);

                if($reponseChatGPT1 == 1 || $reponseChatGPT1 == 0 || $reponseChatGPT1 == 2 || $reponseChatGPT1 == 3 ){
                    $isAutomatico ->mensaje =$mensaje;
                    $isAutomatico ->save();

                    $mensajeCreado1 = RespuestasMensajes::create([
                        'remitente' => $phone,
                        'mensaje' => $mensaje,
                        'respuesta' =>$reponseChatGPT1
                    ]);

                }
                $dataSend = [
                    'ayuda_id' => $isAutomatico->ayuda_id,
                    'mensaje' => $mensaje,
                    'mensaje_interpretado' => $reponseChatGPT1
                ];
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://crmhawkins.com/updateAyudas',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($dataSend),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json'
                    ],
                ]);

                $response = curl_exec($curl);
                Storage::disk('local')->put('Respuesta_Peticion_ChatGPT-Model.txt', $response );

                curl_close($curl);
                return response(200)->header('Content-Type', 'text/plain');

            }else {
                $dataRegistrar = [
                    'id_mensaje' => $id,
                    'id_three' => null,
                    'remitente' => $phone,
                    'mensaje' => $mensaje,
                    'respuesta' => null,
                    'status' => 1,
                    'status_mensaje' => 0,
                    'type' => 'text',
                    'date' => Carbon::now()
                ];
                $mensajeCreado = Mensaje::create($dataRegistrar);
                // $mensajeExiste = Mensaje::where('id_mensaje', $id)->first();
				// $mensajeExiste->id_three = null;
				// $mensajeExiste->save();

			 	$reponseChatGPT = $this->chatGpt($mensaje,$id);

                $respuestaWhatsapp = $this->contestarWhatsapp($phone, $reponseChatGPT);
                if(isset($respuestaWhatsapp['error'])){
                    dd($respuestaWhatsapp);
                }
                $mensajeCreado->update([
                    'respuesta'=> $reponseChatGPT
                ]);
                //return response($reponseChatGPT)->header('Content-Type', 'text/plain');
                return response(201)->header('Content-Type', 'text/plain');

            }

        }
    }
    public function chatGptModelo($respuestaCliente) {
        $token = env('TOKEN_OPENAI', 'valorPorDefecto');

        // Configurar los parámetros de la solicitud
        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        );

        $data = array(
            "model" => "gpt-4o",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => 'Analiza la respuesta de un cliente a este mensaje:
                            Buenas tardes!
                            Me llamo Hera y te escribo de Hawkins, tu agente digitalizador para las subvenciones del kit digital.
                            Te escribo principalmente para continuar con tu subvención. Quieres que te llamemos mañana y avancemos con tu proyecto? Quedo a la espera, Gracias!;
                            Necesito que me respondas con lo que quiere decir el cliente al responder a ese texto ( "Si", "No", "No se") esta es la respuesta del cliente:'. $respuestaCliente .'
                            Respondeme solo con la opcion nada mas, si es SI enviame un 1, si es No 0, si es NO SE enviame un 2, si es algo contrario a todo esto enviame un 3.'
                        ]
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
        $response_data = json_decode($response, true);
        Storage::disk('local')->put('Respuesta_Peticion_ChatGPT-Model.txt', $response_data['choices'][0]['message']['content'] );

        return $response_data['choices'][0]['message']['content'];
        // Procesar la respuesta
        if ($response === false) {
            $error = [
                'status' => 'error',
                'messages' => 'Error al realizar la solicitud'
            ];

            return response()->json($error);
        } else {
            $response_data = json_decode($response, true);
            $responseReturn = [
                'status' => 'ok',
                'messages' => $response_data['choices'][0]['text']
            ];

            return response()->json($response_data);
        }


    }
    public function chatGptPruebas(Request $request) {

    }
    public function chatGpt1($mensaje, $id)
    {
        $mensajeExiste = Mensaje::where('id_mensaje', $id)->first();

        if ($mensajeExiste->id_three === null) {
            // Crear un nuevo hilo si no existe
            $three_id = $this->crearHilo();
            $mensajeExiste->id_three = $three_id['id'];
            $mensajeExiste->save();
        }

        // Independientemente de si el hilo es nuevo o existente, inicia la ejecución
        $hilo = $this->mensajeHilo($mensajeExiste->id_three, $mensaje);
        $ejecuccion = $this->ejecutarHilo1($three_id['id']);
        $ejecuccionStatus = $this->ejecutarHiloStatus($three_id['id'], $ejecuccion['id']);
        //dd($ejecuccionStatus);
        // Inicia un bucle para esperar hasta que el hilo se complete
        while (true) {
            //$ejecuccion = $this->ejecutarHilo($three_id['id']);

            if ($ejecuccionStatus['status'] === 'in_progress') {
                // Espera activa antes de verificar el estado nuevamente
                sleep(5); // Ajusta este valor según sea necesario

                // Verifica el estado del paso actual del hilo
                $pasosHilo = $this->ejecutarHiloISteeps($three_id['id'], $ejecuccion['id']);
                if ($pasosHilo['data'][0]['status'] === 'completed') {
                    // Si el paso se completó, verifica el estado general del hilo
                    $ejecuccionStatus = $this->ejecutarHiloStatus($three_id['id'],$ejecuccion['id']);
                }
            } elseif ($ejecuccionStatus['status'] === 'completed') {
                // El hilo ha completado su ejecución, obtiene la respuesta final
                $mensajes = $this->listarMensajes($three_id['id']);
				//dd($mensajes);
                if(count($mensajes['data']) > 0){
                    return $mensajes['data'][0]['content'][0]['text']['value'];
                }
            } else {
                // Maneja otros estados, por ejemplo, errores
				dd($ejecuccionStatus);
                break; // Sale del bucle si se encuentra un estado inesperado
            }
        }
    }
    public function chatGpt($mensaje, $id)
    {
        $mensajeExiste = Mensaje::where('id_mensaje', $id)->first();

        if ($mensajeExiste->id_three === null) {
            // Crear un nuevo hilo si no existe
            $three_id = $this->crearHilo();
            $mensajeExiste->id_three = $three_id['id'];
            $mensajeExiste->save();
        }

        // Independientemente de si el hilo es nuevo o existente, inicia la ejecución
        $hilo = $this->mensajeHilo($mensajeExiste->id_three, $mensaje);
        $ejecuccion = $this->ejecutarHilo($three_id['id']);
        $ejecuccionStatus = $this->ejecutarHiloStatus($three_id['id'], $ejecuccion['id']);
        //dd($ejecuccionStatus);
        // Inicia un bucle para esperar hasta que el hilo se complete
        while (true) {
            //$ejecuccion = $this->ejecutarHilo($three_id['id']);

            if ($ejecuccionStatus['status'] === 'in_progress') {
                // Espera activa antes de verificar el estado nuevamente
                sleep(5); // Ajusta este valor según sea necesario

                // Verifica el estado del paso actual del hilo
                $pasosHilo = $this->ejecutarHiloISteeps($three_id['id'], $ejecuccion['id']);
                if ($pasosHilo['data'][0]['status'] === 'completed') {
                    // Si el paso se completó, verifica el estado general del hilo
                    $ejecuccionStatus = $this->ejecutarHiloStatus($three_id['id'],$ejecuccion['id']);
                }
            } elseif ($ejecuccionStatus['status'] === 'completed') {
                // El hilo ha completado su ejecución, obtiene la respuesta final
                $mensajes = $this->listarMensajes($three_id['id']);
				//dd($mensajes);
                if(count($mensajes['data']) > 0){
                    return $mensajes['data'][0]['content'][0]['text']['value'];
                }
            } else {
                // Maneja otros estados, por ejemplo, errores
				dd($ejecuccionStatus);
                break; // Sale del bucle si se encuentra un estado inesperado
            }
        }
    }

    public function crearHilo(){
        $token = env('TOKEN_OPENAI', 'valorPorDefecto');
        $url = 'https://api.openai.com/v1/threads';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '. $token,
            "OpenAI-Beta: assistants=v1"
        );

        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        curl_close($curl);

        // Procesar la respuesta
        if ($response === false) {
            $response_data = json_decode($response, true);
            $error = [
            'status' => 'error',
            'messages' => 'Error al realizar la solicitud: '.$response_data
            ];
            return $error;

        } else {
            $response_data = json_decode($response, true);
            //Storage::disk('local')->put('Respuesta_Peticion_ChatGPT-'.$id.'.txt', $response );
            return $response_data;
        }
    }
    public function recuperarHilo($id_thread){
        $token = env('TOKEN_OPENAI', 'valorPorDefecto');
        $url = 'https://api.openai.com/v1/threads/'.$id_thread;

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '. $token,
            "OpenAI-Beta: assistants=v1"
        );

        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        curl_close($curl);

        // Procesar la respuesta
        if ($response === false) {
            $error = [
            'status' => 'error',
            'messages' => 'Error al realizar la solicitud'
            ];

        } else {
            $response_data = json_decode($response, true);
            // Storage::disk('local')->put('Respuesta_Peticion_ChatGPT-'.$id.'.txt', $response );
            return $response_data;
        }
    }
    public function ejecutarHilo1($id_thread){
        $token = env('TOKEN_OPENAI', 'valorPorDefecto');
        $url = 'https://api.openai.com/v1/threads/'.$id_thread.'/runs';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '. $token,
            "OpenAI-Beta: assistants=v1"
        );

        $body = [
            "assistant_id" => 'asst_g5C8HrIw2NSQ5Tgcz750MDiC'
        ];
        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($body));

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        curl_close($curl);

        // Procesar la respuesta
        if ($response === false) {
            $error = [
            'status' => 'error',
            'messages' => 'Error al realizar la solicitud'
            ];

        } else {
            $response_data = json_decode($response, true);
            return $response_data;
        }
    }
    public function ejecutarHilo($id_thread){
        $token = env('TOKEN_OPENAI', 'valorPorDefecto');
        $url = 'https://api.openai.com/v1/threads/'.$id_thread.'/runs';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '. $token,
            "OpenAI-Beta: assistants=v1"
        );

        $body = [
            "assistant_id" => 'asst_J1rG3DRZ1X2mV7t81kHZ9vfj'
        ];
        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($body));

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        curl_close($curl);

        // Procesar la respuesta
        if ($response === false) {
            $error = [
            'status' => 'error',
            'messages' => 'Error al realizar la solicitud'
            ];

        } else {
            $response_data = json_decode($response, true);
            return $response_data;
        }
    }
    public function mensajeHilo($id_thread, $pregunta){
        $token = env('TOKEN_OPENAI', 'valorPorDefecto');
        $url = 'https://api.openai.com/v1/threads/'.$id_thread.'/messages';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '. $token,
            "OpenAI-Beta: assistants=v1"
        );
        $body = [
            "role" => "user",
            "content" => $pregunta
        ];

        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($body));


        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        curl_close($curl);

        // Procesar la respuesta
        if ($response === false) {
            $response_data = json_decode($response, true);
            $error = [
            'status' => 'error',
            'messages' => 'Error al realizar la solicitud: '.$response_data
            ];
            return $error;

        } else {
            $response_data = json_decode($response, true);
            //Storage::disk('local')->put('Respuesta_Peticion_ChatGPT-'.$id.'.txt', $response );
            return $response_data;
        }
    }
    public function ejecutarHiloStatus($id_thread, $id_runs){
        $token = env('TOKEN_OPENAI', 'valorPorDefecto');
        $url = 'https://api.openai.com/v1/threads/'. $id_thread .'/runs/'.$id_runs;

        $headers = array(
            'Authorization: Bearer '. $token,
            "OpenAI-Beta: assistants=v1"
        );

        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        curl_close($curl);

        // Procesar la respuesta
        if ($response === false) {
            $error = [
            'status' => 'error',
            'messages' => 'Error al realizar la solicitud'
            ];

        } else {
            $response_data = json_decode($response, true);
            return $response_data;
        }
    }

    public function ejecutarHiloISteeps($id_thread, $id_runs){
        $token = env('TOKEN_OPENAI', 'valorPorDefecto');
        $url = 'https://api.openai.com/v1/threads/'.$id_thread. '/runs/' .$id_runs. '/steps';

        $headers = array(
            'Authorization: Bearer '. $token,
            "OpenAI-Beta: assistants=v1"
        );

        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        curl_close($curl);

        // Procesar la respuesta
        if ($response === false) {
            $error = [
            'status' => 'error',
            'messages' => 'Error al realizar la solicitud'
            ];

        } else {
            $response_data = json_decode($response, true);
            return $response_data;
        }
    }

    public function listarMensajes($id_thread){
        $token = env('TOKEN_OPENAI', 'valorPorDefecto');
        $url = 'https://api.openai.com/v1/threads/'. $id_thread .'/messages';

        $headers = array(
            'Authorization: Bearer '. $token,
            "OpenAI-Beta: assistants=v1"
        );

        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        curl_close($curl);

        // Procesar la respuesta
        if ($response === false) {
            $error = [
            'status' => 'error',
            'messages' => 'Error al realizar la solicitud'
            ];

        } else {
            $response_data = json_decode($response, true);
            return $response_data;
        }
    }

    public function contestarWhatsapp($phone, $texto)
    {
        $token = env('TOKEN_WHATSAPP', 'valorPorDefecto');
        $urlMensajes = 'https://graph.facebook.com/v18.0/254315494430032/messages';

        $mensajePersonalizado = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phone,
            "type" => "text",
            "text" => [
                "preview_url" => false,
                "body" => $texto
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $urlMensajes,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($mensajePersonalizado),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
        ]);

        $response = curl_exec($curl);
		if ($response === false) {
			$error = curl_error($curl);
			curl_close($curl);
			//Log::error("Error en cURL al enviar mensaje de WhatsApp: " . $error);
			return ['error' => $error];
		}
		curl_close($curl);

		try {
			$responseJson = json_decode($response, true);
			Storage::disk('local')->put("Respuesta_Envio_Whatsapp-{$phone}.txt", $response);
			return $responseJson;
		} catch (\Exception $e) {
			//Log::error("Error al guardar la respuesta de WhatsApp: " . $e->getMessage());
			return ['error' => $e->getMessage()];
		}
    }

    public function autoMensajeWhatsappTemplate($phone, $client, $template)
    {
        $token = env('TOKEN_WHATSAPP', 'valorPorDefecto');
        $urlMensajes = 'https://graph.facebook.com/v18.0/254315494430032/messages';

        $mensajePersonalizado = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phone,
            "type" => "template",
            "template" => [
                "name" => $template,
                // "name" => 'cliente-vip',
                "language" => [
                    "code" => 'es_ES'
                ],
                "components" => [
                    [
                        "type" => 'body',
                        "parameters" => [
                            ["type" => "text", "text" => $client],
                        ],
                    ]
                ]
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $urlMensajes,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($mensajePersonalizado),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $responseJson = json_decode($response, true);

        // Storage::disk('local')->put('Respuesta_Envio_Whatsapp-'.$phone.'.txt', json_encode($response));
        return $responseJson;
    }



    // Vista de los mensajes
    public function whatsapp()
    {
        // $mensajes = Mensaje::all();
        $mensajes = Mensaje::orderBy('created_at', 'desc')->get();
        $resultado = [];
        foreach ($mensajes as $elemento) {

            $remitenteSinPrefijo = (substr($elemento['remitente'], 0, 2) == "34") ? substr($elemento['remitente'], 2) : $elemento['remitente'];

            // Busca el cliente cuyo teléfono coincide con el remitente del mensaje.
            $cliente = Client::where('phone', $remitenteSinPrefijo)->first();

            // Si se encontró un cliente, añade su nombre al elemento del mensaje.
            if ($cliente) {
                $elemento['nombre_remitente'] = $cliente->name;
            } else {
                // Si no se encuentra el cliente, puedes optar por dejar el campo vacío o asignar un valor predeterminado.
                $elemento['nombre_remitente'] = 'Desconocido';
            }

            $resultado[$elemento['remitente']][] = $elemento;


        }

        return view('whatsapp.whatsapp', compact('resultado'));
    }

}
