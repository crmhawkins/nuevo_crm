<?php

namespace App\Console;

use App\Imports\ExcelSimpleImport;
use App\Mail\MailHorasTrabajadas;
use App\Mail\MailHorasTrabajadasUsuario;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\Accounting\UnclassifiedExpenses;
use App\Models\Accounting\UnclassifiedIncome;
use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use App\Models\HoursMonthly\HoursMonthly;
use App\Models\Invoices\Invoice;
use App\Models\Jornada\Jornada;
use App\Models\Tasks\LogTasks;
use App\Models\Users\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Log;
use Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('vacacioner:add')->monthlyOn(1, '08:00');
        $schedule->command('correos:categorizacion')->everyFiveMinutes();
        $schedule->command('correos:get')->everyMinute();
        $schedule->command('correos:getFacturas')->everyMinute();
        $schedule->command('Jornada:finalizar')->dailyAt('03:00');
        $schedule->command('Alertas:facturaFuera')->dailyAt('03:00');
        $schedule->command('Alertas:peticiones')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoAceptadoTareas')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoAceptadoTareasFinalizar')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoAceptar')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoConfirmar')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoFinalizado')->dailyAt('03:00');
        $schedule->command('Alertas:HorasTrabajadas')->weeklyOn(4, '07:30');
        $schedule->command('Ordenes:Alerta')->dailyAt('07:00')->when(function () {
            return now()->isLastOfMonth();
        });
        $schedule->command('vacacioner:discount')->weeklyOn(6, '08:00');
        $schedule->command('kitdigital:avisar-contratos-antiguos')->dailyAt('08:00');
        
        $schedule->call(function () {

            $files = Storage::files('public/excel');
            $cod1Files = array_filter($files, function($file) {
                return str_starts_with(basename($file), 'COD1_');
            });

            foreach ($cod1Files as $file) {
                $import = new ExcelSimpleImport();
                Excel::import($import, $file);


            try {
                $prompt = "Eres un asistente contable. Recibirás un conjunto de movimientos bancarios en formato JSON (cada fila es un array asociativo con campos como 'fecha valor', 'concepto', 'beneficiario', 'debe', 'haber', importe.).
                Tu tarea es:
                1. Detectar automáticamente si el movimiento es un \"ingreso\" (si hay un valor en 'haber') o un \"gasto\" (si hay un valor en 'debe').
                - El tipo de gasto (ingreso o gasto) puede deducirse por DEBE o HABER, INGRESO o GASTO, importe positivo o negativo etc...Nunca habra dos formas de identificar por archivo, solo estara presente una de ellas.
                - Si el importe es negativo, se catalogara como gasto pero debes devolver SIEMPRE un importe positivo.
                ES MUY IMPORTANTE QUE COMPRUEBES BIEN SI ES UN GASTO O INGRESO, Y NO FALLAR
                2. Ignorar cabeceras, nulos o filas irrelevantes.
                3. Para cada movimiento válido, hay movimientos pendientes o retenido no los cuentes ignoralos, devuelve un objeto con los siguientes campos:
                - tipo: \"ingreso\" o \"gasto\"
                - received_date: el valor de la columna 'fecha valor' o 'fecha contable'
                - message: descripción / concepto
                - company_name: nombre de la empresa, si la encuentras si no vacia (beneficiario)
                - amount: valor numerico (cantidad pagada o cobrada)
                - iban: numero de cuenta, puede estar en cualquier parte del documento, identificado como IBAN, Nº Cuenta, etc. Este siempre estara al principio del archivo, nunca en concepto descripcion ni nada parecido.
                - bank: si lo encuentras, debes poner el banco emisor del documento, EN TODO LA PETICION SERA EL MISMO. si no lo encuentras lo envias vacio
                - bank debe ser uno de estos 3: BBVA - SABADELL - BANKINTER (debes convertir el nombre segun el ejemplo).
                Si no encuentras el banco, puedes obtenerlo de esta forma:
                    Extrae el segundo bloque de numeros justo despues de ESXX, por ejemplo:
                    ES1234567890123456789012345678901234567890
                    El numero a extraer sera: 3456
                    Y siguiendo esta tabla:

                    Abanca: 2080
                    BBVA: 0182
                    Banco Caixa Geral: 0130
                    Banco de España: 9000
                    Banco de Madrid: 0059
                    Banco Sabadell: 0081
                    Banco Santander: 0049
                    Bankinter: 0128
                    Barclays Bank: 0065
                    Caixabank: 2100
                    Caixa Ontinyent: 2045
                    Cajasur Banco: 0237
                    Catalunya Bank: 2013
                    Deutsche Bank: 0019
                    Evo Banco: 0239
                    Ibercaja Banco: 2085
                    ING: 1465
                    Instituto de crédito oficial: 1000
                    Kutxabank: 2095
                    Openbank: 0073
                    Revolut: 1583
                    Societe Generale: 0108
                    Targobank: 0216
                    Unicaja Banco 2103

                Cuando lo hagas todo, vuelve a revisar el json para asegurarte de que no hay errores.
                NO DEVUELVAS NADA DE TEXTO QUE NO SEA JSON, NO QUIERO EXPLICACIONES, ACLARACIONES, NI NADA.
                Devuelve estrictamente un JSON como este:

                {
                \"movimientos\": [
                    {
                    \"tipo\": \"ingreso\",
                    \"received_date\": \"31-12-1900\",
                    \"message\": \"\",
                    \"amount\": 0,
                    \"iban\": \"\",
                    \"bank\": \"\",
                    \"company_name\": \"\",
                    \"saldo\": 0
                    },
                    {
                    \"tipo\": \"gasto\",
                    \"received_date\": \"31-12-1900\",
                    \"message\": \"\",
                    \"amount\": 0,
                    \"iban\": \"\",
                    \"bank\": \"\",
                    \"company_name\": \"\",
                    \"saldo\": 0
                    }
                ]
                }
                4. En ningun momento modifiques en nada el concepto del movimiento.
                5. IMPORTANTE: Devuelve tu respuesta en formato JSON como te pedi.
                Una vez
                Aquí están los datos a analizar:";

                if (empty($import->data)) {
                    throw new \Exception('El archivo no contiene datos válidos');
                }

                $rows = [];
                foreach ($import->data as $row) {
                    $rows[] = $row->toArray();
                }

                if (empty($rows)) {
                    throw new \Exception('No se pudieron procesar los datos del archivo');
                }

                $apiUrl = 'https://api.openai.com/v1/chat/completions';
                $apiKey = env('OPENAI_API_KEY');

                if (empty($apiKey)) {
                    throw new \Exception('No se ha configurado la API key de OpenAI');
                }

                $rows = array_map(function ($row) {
                    return array_filter($row, function ($value) {
                        return $value !== null;
                    });
                }, $rows);

                $content = $prompt . "\n\n```json\n" . json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n```";

                try {
                    $client = new \GuzzleHttp\Client();
                    $response = $client->post($apiUrl, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'model' => 'gpt-4o',
                            'messages' => [
                                [
                                    'role' => 'user',
                                    'format' => 'json',
                                    'content' => $content,
                                ],
                            ],
                        ],
                    ]);

                    $responseBody = json_decode($response->getBody(), true);

                    if (!isset($responseBody['choices'][0]['message']['content'])) {
                        throw new \Exception('La respuesta de la API no tiene el formato esperado. Respuesta: ' . json_encode($responseBody));
                    }

                    $jsonRaw = $responseBody['choices'][0]['message']['content'];

                    // Limpiar los marcadores de código si existen
                    $jsonRaw = preg_replace('/^```json\n/', '', $jsonRaw);
                    $jsonRaw = preg_replace('/\n```$/', '', $jsonRaw);

                    $movimientos = json_decode($jsonRaw, true);

                    // Validar que la respuesta tenga el formato esperado
                    if (!$movimientos || !isset($movimientos['movimientos']) || !is_array($movimientos['movimientos'])) {
                        throw new \Exception('La respuesta de la API no tiene el formato esperado. Contenido: ' . $jsonRaw);
                    }

                    // Convertir fechas Excel a string
                    foreach ($movimientos['movimientos'] as &$movimiento) {
                        if (is_numeric($movimiento['received_date'])) {
                            $movimiento['received_date'] = $this->excelDateToDate($movimiento['received_date']);
                        }
                    }
                    unset($movimiento); // Romper la referencia del foreach

                    // Normalizar las fechas usando Carbon
                    foreach ($movimientos['movimientos'] as &$movimiento) {
                        try {
                            $fechaLimpia = trim($movimiento['received_date']);

                            if (strpos($fechaLimpia, '/') !== false) {
                                // Ejemplo: 31/12/2023
                                $movimiento['received_date'] = Carbon::createFromFormat('d/m/Y', $fechaLimpia);
                            } elseif (strpos($fechaLimpia, '-') !== false) {
                                // Ejemplo: 2023-12-31
                                $movimiento['received_date'] = Carbon::createFromFormat('Y-m-d', $fechaLimpia);
                            } else {
                                throw new \Exception("Formato de fecha no reconocido: '$fechaLimpia'");
                            }
                        } catch (\Exception $e) {
                            throw new \Exception("Error al procesar la fecha '{$movimiento['received_date']}': " . $e->getMessage());
                        }

                        $relaciones = [];

                        if ($movimiento['tipo'] === 'ingreso') {
                            // Buscar en invoices
                            $invoiceMatches = Invoice::where(function ($query) use ($movimiento) {
                                if (!empty($movimiento['company_name'])) {
                                    $nombre = explode(' ', $movimiento['company_name'])[0];
                                    $query->where(function ($q) use ($nombre) {
                                        $q->where('concept', 'LIKE', "%$nombre%")
                                            ->orWhere('description', 'LIKE', "%$nombre%")
                                            ->orWhere('note', 'LIKE', "%$nombre%");
                                    });
                                }

                                $query->whereNotIn('invoice_status_id', [3, 4, 5])
                                    ->where(function ($q) use ($movimiento) {
                                        $q->where('total', $movimiento['amount'])
                                          ->orWhere('total', $movimiento['amount'] * 0.5);
                                    });
                            })->get();

                            // Si no hay coincidencias con nombre, buscar solo por importe
                            if ($invoiceMatches->isEmpty() && !empty($movimiento['company_name'])) {
                                $invoiceMatches = Invoice::whereNotIn('invoice_status_id', [3, 4, 5])
                                    ->where(function ($query) use ($movimiento) {
                                        $query->where('total', $movimiento['amount'])
                                            ->orWhere('total', $movimiento['amount'] * 0.5);
                                    })->get();
                            }

                            foreach ($invoiceMatches as $match) {
                                $esParcial = $match->total == ($movimiento['amount'] * 0.5);
                                $relaciones[] = [
                                    'tabla' => 1,
                                    'id' => $match->id,
                                    'parcial' => $esParcial
                                ];
                            }

                            // Buscar en budgets
                            $budgetMatches = Budget::where(function ($query) use ($movimiento) {
                                if (!empty($movimiento['company_name'])) {
                                    $nombre = explode(' ', $movimiento['company_name'])[0];
                                    $query->where(function ($q) use ($nombre) {
                                        $q->where('concept', 'LIKE', "%$nombre%")
                                            ->orWhere('description', 'LIKE', "%$nombre%");
                                    });
                                }

                                $query->where('budget_status_id', '!=', '6')
                                    ->where(function ($q) use ($movimiento) {
                                        $q->where('total', $movimiento['amount'])->orWhere('total', $movimiento['amount'] * 0.5);
                                    });
                            })->get();

                            // Si no hay coincidencias con nombre, buscar solo por importe
                            if ($budgetMatches->isEmpty() && !empty($movimiento['company_name'])) {
                                $budgetMatches = Budget::where('budget_status_id', '!=', '6')
                                    ->where(function ($query) use ($movimiento) {
                                        $query->where('total', $movimiento['amount'])
                                            ->orWhere('total', $movimiento['amount'] * 0.5);
                                    })->get();
                            }

                            foreach ($budgetMatches as $match) {
                                $esParcial = $match->total == ($movimiento['amount'] * 0.5);
                                $relaciones[] = [
                                    'tabla' => 5,
                                    'id' => $match->id,
                                    'parcial' => $esParcial
                                ];
                            }

                            // Buscar en associated_expenses
                            $associatedMatches = AssociatedExpenses::where(function ($query) use ($movimiento) {
                                if (!empty($movimiento['company_name'])) {
                                    $nombre = explode(' ', $movimiento['company_name'])[0];
                                    $query->where(function ($q) use ($nombre) {
                                        $q->where('title', 'LIKE', "%$nombre%");
                                    });
                                }

                                $query->where(function ($q) use ($movimiento) {
                                    $q->where('quantity', $movimiento['amount'])->orWhere('quantity', $movimiento['amount'] * 0.5);
                                });
                            })->where('state', '!=', 'PAGADO')
                            ->get();

                            // Si no hay coincidencias con nombre, buscar solo por importe
                            if ($associatedMatches->isEmpty() && !empty($movimiento['company_name'])) {
                                $associatedMatches = AssociatedExpenses::where(function ($query) use ($movimiento) {
                                    $query->where('quantity', $movimiento['amount'])
                                        ->orWhere('quantity', $movimiento['amount'] * 0.5);
                                })->where('state', '!=', 'PAGADO')->get();
                            }

                            foreach ($associatedMatches as $match) {
                                $esParcial = $match->quantity == ($movimiento['amount'] * 0.5);
                                $relaciones[] = [
                                    'tabla' => 4,
                                    'id' => $match->id,
                                    'parcial' => $esParcial
                                ];
                            }
                        }

                        $movimiento['relaciones'] = !empty($relaciones) ? $relaciones : null;
                    }

                    unset($movimiento); // romper referencia

                    foreach ($movimientos['movimientos'] as $movimiento) {
                        // Generar hash
                        $message = $movimiento['message'] ?? '';
                        $amount = $movimiento['amount'] ?? 0;
                        $received_date = $movimiento['received_date'] ? Carbon::parse($movimiento['received_date'])->format('Y-m-d') : '';
                        $saldo = $movimiento['saldo'] ?? 0;

                        $hashBase = $message . '|' . $amount . '|' . $received_date . '|' . $saldo;
                        $hash = hash('sha256', $hashBase);

                        if ($movimiento['tipo'] == 'ingreso') {
                            if (UnclassifiedIncome::where('hash', $hash)->exists()) {
                                continue;
                            }
                            $unclassifiedIncome = new UnclassifiedIncome();
                            $unclassifiedIncome->company_name = $movimiento['company_name'] ?? null;
                            $unclassifiedIncome->bank = $movimiento['bank'] ?? null;
                            $unclassifiedIncome->iban = $movimiento['iban'] ?? null;
                            $unclassifiedIncome->amount = $amount;
                            $unclassifiedIncome->received_date = $received_date;
                            $unclassifiedIncome->message = $message;
                            $unclassifiedIncome->documents = null;
                            $unclassifiedIncome->relacion = json_encode($movimiento['relaciones'] ?? []);
                            $unclassifiedIncome->status = 0;
                            $unclassifiedIncome->hash = $hash;
                            $unclassifiedIncome->parcial = $movimiento['parcial'] ?? false;
                            $unclassifiedIncome->save();
                        }

                        if ($movimiento['tipo'] == 'gasto') {
                            if (UnclassifiedExpenses::where('hash', $hash)->exists()) {
                                continue;
                            }
                            $unclassifiedExpenses = new UnclassifiedExpenses();
                            $unclassifiedExpenses->company_name = $movimiento['company_name'] ?? null;
                            $unclassifiedExpenses->bank = $movimiento['bank'] ?? null;
                            $unclassifiedExpenses->iban = $movimiento['iban'] ?? null;
                            $unclassifiedExpenses->amount = $amount;
                            $unclassifiedExpenses->received_date = $received_date;
                            $unclassifiedExpenses->message = $message;
                            $unclassifiedExpenses->documents = null;
                            $unclassifiedExpenses->relacion = json_encode($movimiento['relaciones'] ?? []);
                            $unclassifiedExpenses->status = 0;
                            $unclassifiedExpenses->hash = $hash;
                            $unclassifiedExpenses->parcial = $movimiento['parcial'] ?? false;
                            $unclassifiedExpenses->save();
                        }
                    }
                    $newFileName = str_replace('COD1_', 'COD2_', basename($file));
                    Storage::move($file, 'public/excel/procesados/' . $newFileName);

                } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                    throw new \Exception('Error al comunicarse con la API de OpenAI: ' . $e->getMessage());
                }

            } catch (\Exception $e) {
                Log::error('Error al procesar el archivo: ' . $e->getMessage());
            }
        }
        })->everyMinute();

        // $schedule->call(function () {
        //     $users = User::where('inactive', 0)->where('id', '!=', 101)->get();

        //     foreach ($users as $user) {
        //         // Obtiene el último mes (desde el inicio hasta el fin)
        //         $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
        //         $endOfMonth = Carbon::now()->subMonth()->endOfMonth();

        //         $jornadas = $user->jornadas()
        //             ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
        //             ->get();

        //         // Calcular tiempo trabajado por día
        //         $descontar = 0;

        //         foreach ($jornadas->groupBy(function($jornada) {
        //             return Carbon::parse($jornada->start_time)->format('Y-m-d'); // Agrupar por día
        //         }) as $day => $dayJornadas) {

        //             $totalWorkedSeconds = 0;
        //             $isFriday = Carbon::parse($day)->isFriday();

        //             foreach ($dayJornadas as $jornada) {
        //                 $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? $jornada->start_time);
        //                 $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
        //                     return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? $pause->start_time);
        //                 });
        //                 $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
        //             }

        //             // Convertir los segundos trabajados en horas
        //             $workedHours = $totalWorkedSeconds / 3600;
        //             // Calcular la diferencia: 7 horas si es viernes, 8 horas en el resto de días
        //             $targetHours = $isFriday ? 7 : 8;
        //             $difference = $targetHours - $workedHours;

        //             if ($difference > 0) {
        //                 // El usuario trabajó menos de las horas objetivo, debe compensar
        //                 $descontar += $difference;
        //             } elseif ($difference < 0) {
        //                 $descontar -= $difference;
        //             }
        //         }
        //         $descontarDias = $descontar / 24;
        //         DB::update('UPDATE holidays SET quantity = quantity - ? WHERE user_id = ?', [$descontarDias, $user->id]);
        //     }
        // })->monthlyOn(1, '09:00');


        $schedule->call(function () {
            $this->sendEmailHoras();
        //})->everyMinute();
        })->weeklyOn(6, '08:30');
        // $schedule->call(function () {
        //     $users = User::where('inactive',0)->get();
        //     $fechaNow = Carbon::now();
        //     $annio = $fechaNow->format('Y');
        //     $mes = $fechaNow->format('m');
        //     $dia = $fechaNow->format('d');
        //     foreach($users as $user)
        //     {
        //         if( $user->access_level_id == 4 || $user->access_level_id == 5 ){
        //         $fechaFormateada = $fechaNow->format('Y-m-d');
        //         $hoy2 = $fechaNow->format('l');
        //         $time = 0;

        //             if ($hoy2 == 'Monday') {
        //                 $dia = $dia - 3;
        //                 $time = 2;
        //             }elseif($hoy2 == 'Tuesday'){
        //                 $dia = $dia - 1;
        //                 $time = 2;

        //             }elseif($hoy2 == 'Wednesday'){
        //                 $dia = $dia - 1;
        //                 $time = 2;

        //             }elseif($hoy2 == 'Thursday'){
        //                 $dia = $dia - 1;
        //                 $time = 2;

        //             }elseif($hoy2 == 'Friday'){
        //                 $dia = $dia - 1;
        //                 $time = 2;

        //             }elseif($hoy2 == 'Saturday'){
        //                 $dia = $dia - 1;
        //                 $time = 6;

        //             }elseif($hoy2 == 'Sunday'){
        //                 $dia = $dia - 2;
        //                 $time = 7;

        //             }


        //             $alert_30 = Alert::where('admin_user_id', $user->id)->where('stage_id', 30)->whereDate('activation_datetime',$fechaFormateada)->get();
        //             $totalMinutos = 0;
        //             $horasProducidas = DB::select("SELECT SUM(TIMESTAMPDIFF(MINUTE,date_start,date_end)) AS minutos FROM `log_tasks` WHERE date_start BETWEEN ($fechaFormateada - INTERVAL $time DAY) AND NOW() AND `admin_user_id` = $user->id");

        //             if(count($alert_30) != 0){
        //                 $jornada = Jornada::where('admin_user_id', $user->id)
        //                 ->whereYear('date_start', $annio)
        //                 ->whereMonth('date_start', $mes)
        //                 ->whereDay('date_start', $dia)
        //                 ->get();

        //                 foreach($jornada as $item){
        //                 $to_time = strtotime($item->date_start);
        //                 $from_time = strtotime($item->date_end);
        //                 $minutes = ($from_time - $to_time) / 60;
        //                 $totalMinutos += $minutes;
        //                 }

        //                 $data = [
        //                     'admin_user_id' => $user->id,
        //                     'stage_id' => 30,
        //                     'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
        //                     'status_id' => 1,
        //                     'descripcion' => $totalMinutos, $horasProducidas
        //                 ];
        //                 $alert = Alert::create($data);
        //                 $alertSaved = $alert->save();
        //             }
        //         }
        //     }

        // })->weeklyOn(1, '17:20');

        $schedule->call(function () {
            $users = User::where('inactive',0)->get();
            $fechaNow = Carbon::now();
            foreach($users as $user)
            {
                $minutos = DB::select("SELECT SUM(TIMESTAMPDIFF(MINUTE,date_start,date_end)) as 'minutos' FROM `log_tasks` WHERE date_start BETWEEN LAST_DAY(now() - interval 2 month) AND LAST_DAY(NOW() - INTERVAL 1 month) AND admin_user_id = $user->id");
                if($minutos[0]->minutos !== null)
                {
                    $dataMonthly = [
                        'admin_user_id' => $user->id,
                        'hours' => $minutos[0]->minutos,
                        'acceptance_hours' => "NO CONFORME",
                    ];
                    $hoursMonthCreate = HoursMonthly::create($dataMonthly);
                    $hoursMonthSaved=$hoursMonthCreate->save();
                    $data = [
                        'admin_user_id' => $user->id,
                        'stage_id' => 22,
                        'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
                        'status_id' => 1,
                        'reference_id' => $hoursMonthCreate->id
                    ];
                    $alert = Alert::create($data);
                    $alertSaved = $alert->save();
                }

            }

            // ///**** ACTUALIZACION DE BASE DE DATOS MARKETING
            // $fechaNow = Carbon::now();

            // $data = [
            //     'admin_user_id' => 1,
            //     'stage_id' => 27,
            //     'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
            //     'status_id' => 1,
            //     'reference_id' => 0,
            //     'description' => "Aviso para actualizacion de base de datos mensual."
            // ];

            // $alertIvan = Alert::create($data);
            // $alertSaved = $alertIvan->save();

            // $data = [
            //     'admin_user_id' => 23,
            //     'stage_id' => 27,
            //     'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
            //     'status_id' => 1,
            //     'reference_id' => 0,
            //     'description' => "Aviso para actualizacion de base de datos mensual."
            // ];

            // $alertLaura = Alert::create($data);
            // $alertSaved = $alertLaura->save();

            // $data = [
            //     'admin_user_id' => 7,
            //     'stage_id' => 27,
            //     'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
            //     'status_id' => 1,
            //     'reference_id' => 0,
            //     'description' => "Aviso para actualizacion de base de datos mensual."
            // ];

            // $alertJose = Alert::create($data);
            // $alertSaved = $alertJose->save();

        })->monthlyOn(1, '08:00');

        // $schedule->command('queue:work --queue=default,newsletter_automatic,newsletter_manual,newsletter_smart --tries=3')
        //     ->cron('* * * * *')
        //     ->withoutOverlapping();
    }

    protected function sendEmailHoras(){

        // Días de la Semana
        $lunes = Carbon::now()->startOfWeek();
        $martes = Carbon::now()->startOfWeek()->addDays(1);
        $miercoles = Carbon::now()->startOfWeek()->addDays(2);
        $jueves = Carbon::now()->startOfWeek()->addDays(3);
        $viernes = Carbon::now()->startOfWeek()->addDays(4);

        // Obtengo todos los usuarios
        $users =  User::where('inactive',0)->get();
        $arrayUsuarios = [];
        $arrayHorasTrabajadas = [];
        $arrayHorasProducidas = [];
        $arrayHorasTotal = [];

        // Recorro los usuarios
        foreach ($users as $usuario) {
            // Este if es para que no salgan los mensajes del segundo usuario de Camila, se puede borrar
            if($usuario->id != 1){
                // Se imprimen las horas trabajadas de cada usuario en minutos y luego se pone en texto
                $horasTrabajadasLunes = $this->horasTrabajadasDia($lunes, $usuario->id);
                $horasTrabajadasMartes = $this->horasTrabajadasDia($martes, $usuario->id);
                $horasTrabajadasMiercoles = $this->horasTrabajadasDia($miercoles, $usuario->id);
                $horasTrabajadasJueves = $this->horasTrabajadasDia($jueves, $usuario->id);
                $horasTrabajadasViernes = $this->horasTrabajadasDia($viernes, $usuario->id);

                $horasTrabajadasSemana = $horasTrabajadasLunes + $horasTrabajadasMartes + $horasTrabajadasMiercoles + $horasTrabajadasJueves + $horasTrabajadasViernes;

                // Se imprimen las horas producidas de cada usuario en minutos y luego se pone en texto
                $horasProducidasLunes = $this->tiempoProducidoDia($lunes, $usuario->id);
                $horasProducidasMartes = $this->tiempoProducidoDia($martes, $usuario->id);
                $horasProducidasMiercoles = $this->tiempoProducidoDia($miercoles, $usuario->id);
                $horasProducidasJueves = $this->tiempoProducidoDia($jueves, $usuario->id);
                $horasProducidasViernes = $this->tiempoProducidoDia($viernes, $usuario->id);

                $horasProducidasSemana = $horasProducidasLunes + $horasProducidasMartes + $horasProducidasMiercoles + $horasProducidasJueves + $horasProducidasViernes;

                if($horasTrabajadasSemana > 0){

                    $horaHorasTrabajadas = floor($horasTrabajadasSemana / 60);
                    $minutoHorasTrabajadas = ($horasTrabajadasSemana % 60);

                    $horaHorasProducidas = floor($horasProducidasSemana / 60);
                    $minutoHorasProducidas = ($horasProducidasSemana % 60);


                        // Si el usuario es acces_level_id 5, se muestran las horas trabajadas y producidas, si no, se muestran las pruducidas solamente
                        if ($usuario->access_level_id == 5) {
                            $mensajeHorasTrabajadas = "Ha trabajado ". $horaHorasTrabajadas . " Horas y " . $minutoHorasTrabajadas . ' minutos'. ' esta semana.';
                            $mensajeHorasProducidas = "Ha producido ". $horaHorasProducidas . " Horas y " . $minutoHorasProducidas . ' minutos'. ' esta semana.';
                        } else{
                            $mensajeHorasTrabajadas = "Ha trabajado ". $horaHorasTrabajadas . " Horas y " . $minutoHorasTrabajadas . ' minutos'. ' esta semana.';
                            $mensajeHorasProducidas = "";
                        }

                    array_push($arrayUsuarios, $usuario->name);
                    array_push($arrayHorasTrabajadas, $mensajeHorasTrabajadas);
                    array_push($arrayHorasProducidas, $mensajeHorasProducidas);
                    array_push($arrayHorasTotal, $usuario->name);
                    array_push($arrayHorasTotal, $mensajeHorasTrabajadas);
                    array_push($arrayHorasTotal, $mensajeHorasProducidas);
                    $this->sendEmailHorasTrabajadasUsuario($usuario->email, $mensajeHorasTrabajadas, $mensajeHorasProducidas);
                }
            }
        }
    $this->sendEmailHorasTrabajadas($arrayHorasTotal);
}

    public function sendEmailHorasTrabajadasUsuario($usuario, $mensajeHorasTrabajadas, $mensajeHorasProducidas){



        $email = new MailHorasTrabajadasUsuario($mensajeHorasTrabajadas, $mensajeHorasProducidas);

        Mail::to($usuario)->send($email);

        return 200;

    }
    public function sendEmailHorasTrabajadas($arrayHorasTotal){

        $mail = "ivan@hawkins.es";
        $mail2 = "nacho.moreno@lchawkins.com";

        $email = new MailHorasTrabajadas($arrayHorasTotal);

        Mail::to($mail)->cc($mail2)->send($email);

        return 200;

    }
    public function tiempoProducidoDia($dia, $id) {

        $now = $dia->format('Y-m-d');
        $nowDay = Carbon::now()->format('d');
        $hoy = Carbon::today();
        $tiempoTarea = 0;
        $result = 0;

        $tareasHoy = LogTasks::where('admin_user_id', $id)->whereDate('date_start','=', $dia)->get();

        foreach($tareasHoy as $tarea) {
            if ($tarea->status == 'Pausada') {

                $tiempoInicio = Carbon::parse($tarea->date_start);
                $tiempoFinal = Carbon::parse($tarea->date_end);
                $tiempoTarea +=  $tiempoFinal->diffInMinutes($tiempoInicio);

            }

        }

                $dt = Carbon::now();
                $days = $dt->diffInDays($dt->copy()->addSeconds($tiempoTarea));
                $hours = $dt->diffInHours($dt->copy()->addSeconds($tiempoTarea)->subDays($days));
                $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($tiempoTarea)->subDays($days)->subHours($hours));
                $seconds = $dt->diffInSeconds($dt->copy()->addSeconds($tiempoTarea)->subDays($days)->subHours($hours)->subMinutes($minutes));
                $result = CarbonInterval::days($days)->hours($hours)->minutes($minutes)->seconds($seconds)->forHumans();


        return $tiempoTarea;
    }

    public function horasTrabajadasDia($dia, $id){


        $totalWorkedSeconds = 0;
        // Jornada donde el año fecha y día de hoy
        $jornadas = Jornada::where('admin_user_id', $id)
        ->whereDate('start_time', $dia)
        ->get();

        // Se recorren los almuerzos de hoy
        foreach($jornadas as $jornada){
            $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? Carbon::now());
            $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
                return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? Carbon::now());
            });
            $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
        }
        $horasTrabajadasFinal = $totalWorkedSeconds / 60;

        return $horasTrabajadasFinal;
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    private function excelDateToDate($excelSerial)
    {
        $unixTimestamp = ($excelSerial - 25569) * 86400; // 25569 = días entre 1/1/1900 y Unix Epoch
        return gmdate('Y-m-d', $unixTimestamp);
    }
}
