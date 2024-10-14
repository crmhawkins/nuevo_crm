<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Whatsapp\WhatsappController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Whatsapp\Mensaje;
use App\Models\Whatsapp\RespuestasMensajes;
use Carbon\Carbon;



class AccionesController extends Controller
{
    public function index(){
        $estados = [
            ['id' => 1, 'name' => 'CANCELADO PARA SIEMPRE'],
            ['id' => 2, 'name' => 'TRAMITADA'],
            ['id' => 3, 'name' => 'APORTAR DOCUMENTACION'],
            ['id' => 4, 'name' => 'ACEPTADA'],
            ['id' => 5, 'name' => 'ACEPTADA - ACUERDO VALIDADO'],
            ['id' => 6, 'name' => 'CANCELADA'],
            ['id' => 7, 'name' => 'PRODUCCION'],
            ['id' => 8, 'name' => 'JUSTIFICADO'],
            ['id' => 9, 'name' => 'JUSTIFICADO PARCIAL'],
            ['id' => 10, 'name' => 'VALIDADA'],
            ['id' => 11, 'name' => 'PAGADA'],
            ['id' => 12, 'name' => 'PENDIENTE SUBSANAR 1'],
            ['id' => 13, 'name' => 'PENDIENTE SUBSANAR 2'],
            ['id' => 14, 'name' => 'SUBSANADO 1'],
            ['id' => 15, 'name' => 'SUBSANADO 2'],
            ['id' => 16, 'name' => 'CANCELADA POR VENCIMIENTO'],
            ['id' => 17, 'name' => 'RECUPERACION'],
            ['id' => 18, 'name' => 'LEADS'],
            ['id' => 19, 'name' => 'NO PAGADA POR PROBLEMAS DE HACIENDA'],
            ['id' => 20, 'name' => 'PENDIENTE SEGUNDA JUSTIFICACIÓN'],
            ['id' => 21, 'name' => 'SEGUNDA JUSTIFICACIÓN (REALIZADA)'],
            ['id' => 22, 'name' => 'PAGADA 2º JUSTIFICACIÓN'],
            ['id' => 23, 'name' => 'PENDIENTE DE TRAMITAR'],
            ['id' => 24, 'name' => 'INTERESADOS'],
            ['id' => 25, 'name' => 'VALIDADAS 2ª JUSTIFICACION'],
        ];
        return view('acciones.index', compact('estados'));
    }
    public function enviar(){
        $estados = [
            ['id' => 1, 'name' => 'CANCELADO PARA SIEMPRE'],
            ['id' => 2, 'name' => 'TRAMITADA'],
            ['id' => 3, 'name' => 'APORTAR DOCUMENTACION'],
            ['id' => 4, 'name' => 'ACEPTADA'],
            ['id' => 5, 'name' => 'ACEPTADA - ACUERDO VALIDADO'],
            ['id' => 6, 'name' => 'CANCELADA'],
            ['id' => 7, 'name' => 'PRODUCCION'],
            ['id' => 8, 'name' => 'JUSTIFICADO'],
            ['id' => 9, 'name' => 'JUSTIFICADO PARCIAL'],
            ['id' => 10, 'name' => 'VALIDADA'],
            ['id' => 11, 'name' => 'PAGADA'],
            ['id' => 12, 'name' => 'PENDIENTE SUBSANAR 1'],
            ['id' => 13, 'name' => 'PENDIENTE SUBSANAR 2'],
            ['id' => 14, 'name' => 'SUBSANADO 1'],
            ['id' => 15, 'name' => 'SUBSANADO 2'],
            ['id' => 16, 'name' => 'CANCELADA POR VENCIMIENTO'],
            ['id' => 17, 'name' => 'RECUPERACION'],
            ['id' => 18, 'name' => 'LEADS'],
            ['id' => 19, 'name' => 'NO PAGADA POR PROBLEMAS DE HACIENDA'],
            ['id' => 20, 'name' => 'PENDIENTE SEGUNDA JUSTIFICACIÓN'],
            ['id' => 21, 'name' => 'SEGUNDA JUSTIFICACIÓN (REALIZADA)'],
            ['id' => 22, 'name' => 'PAGADA 2º JUSTIFICACIÓN'],
            ['id' => 23, 'name' => 'PENDIENTE DE TRAMITAR'],
            ['id' => 24, 'name' => 'INTERESADOS'],
            ['id' => 25, 'name' => 'VALIDADAS 2ª JUSTIFICACION'],
        ];
        return view('acciones.enviar', compact('estados'));
    }

    public function enviarMensajes(Request $request){
        		$count = 0;

        $categoria = $request->categoria;
        // Realiza la solicitud HTTP para obtener los teléfonos usando cURL
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://crm.hawkins.es/api/getAyudas',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['estado' => $categoria]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);

		$phones = [];
        foreach ($data as $item) {
            if (isset($item['telefono'])) {
                // Eliminar espacios del número de teléfono
                $cleanedPhone = preg_replace('/\s+/', '', $item['telefono']);
                if (preg_match('/^\d{9}$/', $cleanedPhone)) {
                    $phones[] = [
                        'id' =>  $item['id'],
                        'phone' => $cleanedPhone
                    ];
                }
            }
        }
        $phones = array_unique($phones, SORT_REGULAR);
        // $phones = [
        //     [
        //         'id' =>  7405,
        //         'phone' => '605621704'
        //     ],
        //     [
        //         'id' =>  7405,
        //         'phone' => '622440984'
        //     ],
        //     [
        //         'id' =>  7405,
        //         'phone' => '626264646'
        //     ],
        // ];
        $template='';
        $mensajeEnvio='';

        switch ($categoria) {
            case 24:
                $template = 'kit_digital_10';
                $mensajeEnvio = 'Buenas tardes!
                        Me llamo Hera y te escribo de Hawkins, tu agente digitalizador para las subvenciones del kit digital.
                        Te escribo principalmente para continuar con tu subvención. Quieres que te llamemos y avancemos con tu proyecto? Si no te viene bien hoy dime cuando te vendria bien. Quedo a la espera, Gracias!';
            break;
            case 18:
                $template = 'kit_digital_leads2';
                $mensajeEnvio = 'Buenas tardes!
Me llamo Hera y te escribo de Hawkins, tu agente digitalizador para las subvenciones del kit digital.
Te escribo principalmente para preguntarte si estás interesado en que te consigamos las subvención y el portátil gratis o por el contrario te demos de baja de la lista de interesados.
Te ruego que nos indiques si quieres que te contactemos o en caso contrario que me indiques si quieres que te demos de baja del directorio para no molestarte más.
Muchas gracias!';
            break;
            case 4:
                $template = 'kit_digital_aceptado';
                $mensajeEnvio = 'Buenas tardes!
                    Me llamo Hera y te escribo de Hawkins, tu agente digitalizador para las subvenciones del kit digital.
                    Te escribo principalmente para recordarte que tienes tu kit digital aceptado pero que aun no nos has dicho a que servicio lo quieres destinar. Como los plazos de esa subvención están muy justos , te gustaría que te llamásemos y te ayudemos a elegir a que destinar el kit? Quedo a la espera muchas gracias!';
            break;

            default:
                # code...
                break;
        }
        foreach($phones as $entry){
            $mensajeCreado = Mensaje::where('ayuda_id',$entry['id'] )->first();
            if (!$mensajeCreado) {
                $phone = $entry['phone'];
                $envioMensaje = $this->autoMensajeWhatsappTemplate('34'.$phone, $template);
				$count += 1;
				$curl = curl_init();

				curl_setopt_array($curl, [
					CURLOPT_URL => 'https://crm.hawkins.es/api/updateAyudas/'.$entry['id'],
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS => json_encode(['estado' => $categoria]),
					CURLOPT_HTTPHEADER => [
						'Content-Type: application/json'
					],
				]);

				$response = curl_exec($curl);
                $id = $envioMensaje['messages'][0]['id'];

                $dataRegistrar = [
                    'id_mensaje' => $id,
                    'id_three' => null,
                    'remitente' => '34'.$phone,
                    'respuesta' => $mensajeEnvio,
                    'mensaje' => null,
                    'status' => 1,
                    'status_mensaje' => 1,
                    'type' => 'text',
                    'is_automatic' => true,
                    'ayuda_id' => $entry['id'],
                    'date' => Carbon::now()
                ];
                $mensajeCreado = Mensaje::create($dataRegistrar);
                # code...
            }
        }dd($count);
        return redirect()->route('acciones.enviar');
    }
    public function enviarMensajesSegmentos(Request $request){

        $categoria = $request->categoria;
        // Realiza la solicitud HTTP para obtener los teléfonos usando cURL
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://crmhawkins.com/getAyudasSegmento3',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['estado' => $categoria]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);
        $phones = [];
        $seenPhones = [];  // Array para llevar registro de los teléfonos ya procesados

        foreach ($data['ayudas'] as $item) {
            if (isset($item['telefono'])) {
                $cleanedPhone = preg_replace('/\s+/', '', $item['telefono']);
                if (preg_match('/^\d{9}$/', $cleanedPhone) && !in_array($cleanedPhone, $seenPhones)) {
                    $phones[] = [
                        'id' =>  $item['id'],
                        'phone' => $cleanedPhone
                    ];
                    $seenPhones[] = $cleanedPhone;  // Agregar el teléfono al registro de vistos
                }
            }
        }
        // dd($phones);

            // $phones = array_unique($phones, SORT_REGULAR);
        $mensajeEnvio = "Buenas!
Me llamo Hera y te escribo de Hawkins, tu agente digitalizador para las subvenciones del kit digital.
Te escribo principalmente para recordarte que ahora con tu kit digital te podemos solicitar un ordenador portátil o sobre mesa totalmente gratis. Quieres que te llamemos y te ampliemos la información? Quedo a la espera muchas gracias!";
        foreach($phones as $entry){
            $mensajeCreado = Mensaje::where('ayuda_id',$entry['id'] )->first();
            if (!$mensajeCreado) {
                $phone = $entry['phone'];
                $envioMensaje = $this->autoMensajeWhatsappTemplate('34'.$phone, 'kit_digital_ordenadores');
                //dd($envioMensaje);
                $id = $envioMensaje['messages'][0]['id'];

                $dataRegistrar = [
                    'id_mensaje' => $id,
                    'id_three' => null,
                    'remitente' => '34'.$phone,
                    'respuesta' => $mensajeEnvio,
                    'mensaje' => null,
                    'status' => 1,
                    'status_mensaje' => 1,
                    'type' => 'text',
                    'is_automatic' => true,
                    'ayuda_id' => $entry['id'],
                    'date' => Carbon::now()
                ];
                $mensajeCreado = Mensaje::create($dataRegistrar);
                # code...
            }
        }
        return redirect()->route('acciones.enviar');
    }

    public function autoMensajeWhatsappTemplate($phone, $template)
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
                /*"components" => [
                    [
                        "type" => 'body',
                        "parameters" => [
                            ["type" => "text", "text" => $client],
                        ],
                    ]
                ]*/
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

        Storage::disk('local')->put('Respuesta_Envio_Whatsapp-'.$phone.'.txt', json_encode($response));
        return $responseJson;
    }

    public function listarMensajes($id){
        $primerMensaje = Mensaje::where('ayuda_id', $id)->first();

        if ($primerMensaje) {
            $mensajes = Mensaje::where('remitente', $primerMensaje->remitente)->get();
            return response()->json($mensajes);
        }

        return response(404)->header('Content-Type', 'text/plain');
    }

    public function actualizar_old(){
				$count = 0;
        $whatsappController = new WhatsappController();
       $isAutomatico = Mensaje::where('is_automatic', true)
		   ->where('mensaje', null)
           ->where('created_at', '>=', '2024-10-09')
           ->get();

       if (count($isAutomatico) > 0) {
           foreach($isAutomatico as $item){

                $nextMensaje = Mensaje::where('remitente', $item->remitente)
               ->where('created_at', '>', $item->created_at)
               ->where('is_automatic',null)
               ->orderBy('created_at', 'asc')
               ->first();

           if ($nextMensaje) {
                $reponseChatGPT1 = $whatsappController->chatGptModelo($nextMensaje->mensaje);

               if($reponseChatGPT1 == 1 || $reponseChatGPT1 == 0 || $reponseChatGPT1 == 2 || $reponseChatGPT1 == 3 ){
                      //$item ->respuesta =$mensaje;
                   //$item ->save();
                   $mensajeCreado1 = RespuestasMensajes::create([
                       'remitente' => $item->remitente,
                       'mensaje' => $nextMensaje->mensaje,
                       'respuesta' =>$reponseChatGPT1
                   ]);

               }
               $dataSend = [
                   'ayuda_id' => $item->ayuda_id,
                   'mensaje' => $mensajeCreado1->mensaje,
                   'mensaje_interpretado' => $mensajeCreado1->respuesta
               ];
               $curl = curl_init();

               curl_setopt_array($curl, [
                   CURLOPT_URL => 'https://crm.hawkins.es/api/updateMensajes',
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

               curl_close($curl);
               /*if($item->id == 1587){
                               dd($response);
               }*/
			   $item->update(['mensaje' => $mensajeCreado1->mensaje]);
           }
			   				$count += 1;

       }

       }
		dd($count);
               return redirect()->route('acciones.index');
   }
	public function actualizar(){
	$count = 0;
	  $isAutomatico = Mensaje::where('is_automatic', true)
		   ->where('mensaje', null)
           ->where('created_at', '>=', '2024-10-09')
           ->get();

	 $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://crm.hawkins.es/api/getAyudas',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(['estado' => 18]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    $clientes = json_decode($response, true); // Suponiendo que esto devuelve una lista de clientes
   foreach ($isAutomatico as $mensaje) {
      foreach ($clientes as $cliente) {
          if (isset($cliente['id']) && $cliente['id'] == $mensaje->ayuda_id) {

			  $dataSend = [
                    'ayuda_id' =>  $mensaje->ayuda_id, // Asumiendo que esta es la manera correcta de obtener el id
                    'mensaje' => null,
                    'mensaje_interpretado' => 4
                ];

                // Enviar la actualización
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://crm.hawkins.es/api/updateMensajes',
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
				$count ++;
                curl_close($curl);
                }
            }
        }
		// Puedes retornar el contador o realizar otras acciones después del bucle
    return $count;
    }



	public function actualizar2() {
		$count = 0;
     $fechaDesde = '2024-10-09';
    $mensajesCreadosDesde = RespuestasMensajes::where('created_at', '>=', $fechaDesde)->get();
    // Obtener los datos de los clientes
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://crm.hawkins.es/api/getAyudas',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(['estado' => 18]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    $clientes = json_decode($response, true); // Suponiendo que esto devuelve una lista de clientes

    // Relacionar cada mensaje con los clientes y enviar los datos actualizados
    foreach ($mensajesCreadosDesde as $mensaje) {
        foreach ($clientes as $cliente) {
            // Asumiendo que cada cliente tiene un 'telefono' sin el prefijo '34'
            if (substr($mensaje->remitente, 2) == $cliente['telefono']) { // Quitamos el '34' y comparamos
                $dataSend = [
                    'ayuda_id' => $cliente['id'], // Asumiendo que esta es la manera correcta de obtener el id
                    'mensaje' => $mensaje->mensaje,
                    'mensaje_interpretado' => $mensaje->respuesta
                ];

                // Enviar la actualización
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://crm.hawkins.es/api/updateMensajes',
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
				$count += 1;
                curl_close($curl);
            }
        }
    }	dd($count);

    return redirect()->route('acciones.index');
}


}
