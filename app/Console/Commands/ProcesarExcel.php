<?php

namespace App\Console\Commands;

use App\Imports\ExcelSimpleImport;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\Accounting\UnclassifiedExpenses;
use App\Models\Accounting\UnclassifiedIncome;
use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use App\Models\Invoices\Invoice;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class ProcesarExcel extends Command
{

    protected $signature = 'Tesoreria:ProcesarExcel';
    protected $description = 'Procesa excel de tesoreria';

    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        Log::info('Iniciando procesamiento de archivos Excel');
        $this->info('Procesando excel...');
        $files = Storage::files('public/excel');
            $cod1Files = array_filter($files, function($file) {
                return str_starts_with(basename($file), 'COD1_');
            });
            $this->info('Ficheros encontrados: ' . count($cod1Files));
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
                5. Todo el texto que devuelvas debe estar en mayusculas, TODO. Es muy importante.
                6. Los nombres de las variables de JSON no deben estar en mayusculas, siempre minusculas.
                7. IMPORTANTE: Devuelve tu respuesta en formato JSON como te pedi.
                Una vez
                Aquí están los datos a analizar:";

                if (empty($import->data)) {
                    $this->error('El archivo no contiene datos válidos');
                    throw new \Exception('El archivo no contiene datos válidos');
                }

                $rows = [];
                foreach ($import->data as $row) {
                    $rows[] = $row->toArray();
                }

                if (empty($rows)) {
                    $this->error('No se pudieron procesar los datos del archivo');
                    throw new \Exception('No se pudieron procesar los datos del archivo');
                }

                $apiUrl = 'https://api.openai.com/v1/chat/completions';
                $apiKey = env('OPENAI_API_KEY');

                if (empty($apiKey)) {
                    $this->error('No se ha configurado la API key de OpenAI');
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
                        $this->error('La respuesta de la API no tiene el formato esperado. Respuesta: ' . json_encode($responseBody));
                        throw new \Exception('La respuesta de la API no tiene el formato esperado. Respuesta: ' . json_encode($responseBody));
                    }

                    $jsonRaw = $responseBody['choices'][0]['message']['content'];

                    // Limpiar los marcadores de código si existen
                    $jsonRaw = preg_replace('/^```json\n/', '', $jsonRaw);
                    $jsonRaw = preg_replace('/\n```$/', '', $jsonRaw);

                    $movimientos = json_decode($jsonRaw, true);

                    // Validar que la respuesta tenga el formato esperado
                    if (!$movimientos || !isset($movimientos['movimientos']) || !is_array($movimientos['movimientos'])) {
                        $this->error('La respuesta de la API no tiene el formato esperado. Contenido: ' . $jsonRaw);
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

                            // Formatos aceptados
                            $formatos_posibles = [
                                'd/m/Y',
                                'Y-m-d',
                                'm/d/Y',
                                'd-m-Y',
                                'Y/m/d',
                                'd.m.Y',
                                'Y.m.d',
                            ];

                            $fecha = null;

                            foreach ($formatos_posibles as $formato) {
                                try {
                                    $fechaTemporal = Carbon::createFromFormat($formato, $fechaLimpia);
                                    if ($fechaTemporal && $fechaTemporal->format($formato) === $fechaLimpia) {
                                        $fecha = $fechaTemporal;
                                        break;
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }

                            if (!$fecha) {
                                $this->error("Formato de fecha no reconocido: '$fechaLimpia'");
                                throw new \Exception("Formato de fecha no reconocido: '$fechaLimpia'");
                            }

                            $movimiento['received_date'] = $fecha;


                        } catch (\Exception $e) {
                            Log::error('Error al procesar fecha en movimiento:', [
                                'fecha_original' => $movimiento['received_date'],
                                'error' => $e->getMessage()
                            ]);
                            continue; // saltar este movimiento
                        } catch (\Exception $e) {
                            $this->error("Error al procesar la fecha '{$movimiento['received_date']}': " . $e->getMessage());
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
                        if ($received_date < '2025-06-05') {
                            continue;
                        }
                        $saldo = $movimiento['saldo'] ?? 0;

                        $hashBase = $message . '|' . $amount . '|' . $received_date;
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
                    $this->error('Error al comunicarse con la API de OpenAI: ' . $e->getMessage());
                    throw new \Exception('Error al comunicarse con la API de OpenAI: ' . $e->getMessage());
                }

            } catch (\Exception $e) {
                $this->error('Error al procesar el archivo: ' . $e->getMessage());
                Log::error('Error al procesar el archivo: ' . $e->getMessage());
            }
        }
        Log::info('Comando ProcesarExcel ejecutado exitosamente');
        $this->info('¡Comando ejecutado exitosamente!');
    }

    private function excelDateToDate($excelSerial)
    {
        $unixTimestamp = ($excelSerial - 25569) * 86400; // 25569 = días entre 1/1/1900 y Unix Epoch
        return gmdate('Y-m-d', $unixTimestamp);
    }
}