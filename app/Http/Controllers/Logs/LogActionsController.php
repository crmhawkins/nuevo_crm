<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\KitDigital;
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

    public function Clasificacion(Request $request)
    {
        $fecha = $request->fecha ?? Carbon::today()->subDay(1);

        // Obtener los logs del día específico
        $logActions = LogActions::where('tipo', 1)
        ->whereDate('created_at', $fecha)
        ->orderBy('admin_user_id')
        ->get()
        ->groupBy('admin_user_id');
        $usuarios = User::get()->keyBy('id');
        $referenceIds = $logActions->flatten()->pluck('reference_id')->unique();
        // Obtener los registros de KitDigital que coincidan con los reference_id únicos
        $kitdigital = KitDigital::whereIn('id', $referenceIds)->get()->keyBy('id');

        $clasificacion = [];

        foreach ($logActions as $adminUserId => $logs) {
            foreach ($logs as $log) {
                // 1. Identificar si la acción comienza con "Actualizar"
                if (strpos($log->action, 'Actualizar') === 0) {
                    // Extraer la segunda palabra (la propiedad actualizada)
                    $partesAccion = explode(' ', $log->action);
                    $propiedadActualizada = isset($partesAccion[1]) ? $partesAccion[1] : '';
                    $referencia = $log->reference_id;

                    // 2. Separar los valores en la descripción (De "valor antiguo" a "valor nuevo")
                    if (preg_match("/De (.+) a (.+)/", $log->description, $matches)) {
                        $valorAntiguo = $matches[1];
                        $valorNuevo = $matches[2];

                        // 3. Clasificar por usuario, propiedad actualizada y agregar los detalles
                        if (!isset($clasificacion[$adminUserId])) {
                            $clasificacion[$adminUserId] = []; // Inicializar si no existe
                        }
                        if (!isset($clasificacion[$adminUserId][$referencia])) {
                            $clasificacion[$adminUserId][$referencia] = []; // Inicializar si no existe
                        }

                        if (!isset($clasificacion[$adminUserId][$referencia][$propiedadActualizada])) {
                            $clasificacion[$adminUserId][$referencia][$propiedadActualizada] = []; // Inicializar si no existe
                        }

                        $clasificacion[$adminUserId][$referencia][$propiedadActualizada][] = [
                            'valor_antiguo' => $valorAntiguo,
                            'valor_nuevo' => $valorNuevo,
                            'created_at' => $log->created_at
                        ];
                    }
                }else{
                    $propiedadActualizada =  $log->description;
                    $referencia = $log->reference_id;


                    if (!isset($clasificacion[$adminUserId])) {
                        $clasificacion[$adminUserId] = []; // Inicializar si no existe
                    }

                    if (!isset($clasificacion[$adminUserId][$referencia])) {
                        $clasificacion[$adminUserId][$referencia] = []; // Inicializar si no existe
                    }

                    if (!isset($clasificacion[$adminUserId][$referencia][$propiedadActualizada])) {
                        $clasificacion[$adminUserId][$referencia][$propiedadActualizada] = []; // Inicializar si no existe
                    }
                    $clasificacion[$adminUserId][$referencia][$propiedadActualizada][] = [
                        'valor_antiguo' => '',
                        'valor_nuevo' => '',
                        'created_at' => $log->created_at

                    ];
                }
            }
        }

        foreach ($clasificacion as $adminUserId => &$referencias) {
            foreach ($referencias as $referenciaId => &$propiedades) {
                foreach ($propiedades as $propiedad => &$detalles) {
                    usort($detalles, function ($a, $b) {
                        return $a['created_at'] <=> $b['created_at'];
                    });
                }
            }
        }


        return view('logs.clasificacion', compact('clasificacion', 'usuarios','kitdigital'));
    }


    // public function Clasificacion(Request $request)
    // {
    //     $fecha = $request->fecha ?? Carbon::today()->subDay(1);

    //     // Obtener los logs del día específico
    //     $logActions = LogActions::where('tipo', 1)->whereDate('created_at', $fecha)->get();



    //     $token = env('OPENAI_API_KEY');
    //     $url = 'https://api.openai.com/v1/chat/completions';
    //     $headers = [
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $token
    //     ];

    //     // Dividir el logActions en partes de tamaño adecuado (ajusta el tamaño según sea necesario)
    //     $chunks = array_chunk($logActions, 150); // Por ejemplo, dividir en partes de 5

    //     $clasificacion = [];

    //     foreach ($chunks as $chunk) {
    //         // Convertir el chunk en JSON
    //         $chunkJson = json_encode($chunk);

    //         $data = [
    //             "model" => "gpt-4o",
    //             "messages" => [
    //                 [
    //                     "role" => "user",
    //                     "content" => [
    //                         [
    //                             "type" => "text",
    //                             "text" => 'Analiza el siguiente JSON que contiene registros de acciones de actualización y creación, asegurándote de que se clasifiquen según el "admin_user_id",
    //                                         que es el ID del usuario. Para cada actualización, identifica los valores antiguos y nuevos involucrados, extraídos del campo de descripción, que sigue el formato DE (valor antiguo) a (valor nuevo).
    //                                         Estructura la respuesta de manera que para cada "admin_user_id" se liste cada campo que fue actualizado y sus respectivos valores antiguos y nuevos.
    //                                         Entrega el resultado en formato JSON, agrupado por "admin_user_id".
    //                                         Manda solo le json no mandes mas texto.
    //                                         Para la respuesta usa el siguiente formato:
    //                                         {"admin_user_id": {"campo de actualizacion": [ { "antiguo": "valor antiguo", "nuevo": "valor nuevo" }, ], }, "admin_user_id_2": { "campo de actualizacion": [ { "antiguo": "valor antiguo", "nuevo": "valor nuevo" },],},}'
    //                         ],
    //                         [
    //                             "type" => "text",
    //                             "text" => $chunkJson
    //                         ],
    //                     ]
    //                 ]
    //             ]
    //         ];

    //         // Inicializar cURL y configurar las opciones
    //         $curl = curl_init();
    //         curl_setopt($curl, CURLOPT_URL, $url);
    //         curl_setopt($curl, CURLOPT_POST, true);
    //         curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    //         curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //         $response = curl_exec($curl);
    //         curl_close($curl);

    //         // Decodificar la respuesta JSON
    //         $response_data = json_decode($response, true);

    //         $content = $response_data['choices'][0]['message']['content'];
    //         $content = str_replace(['```json', '```'], '', $content);
    //         $clasificacionChunk = json_decode($content, true);

    //         // Combinar las clasificaciones parciales
    //         $clasificacion = array_merge_recursive($clasificacion, $clasificacionChunk);
    //     }

    //     $usuarios = User::get()->keyBy('id');
    //     return view('logs.clasificacion', compact('clasificacion', 'usuarios'));
    // }




}
