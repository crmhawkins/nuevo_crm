<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\Accounting\Gasto;
use App\Models\Accounting\Ingreso;
use App\Models\Accounting\UnclassifiedExpenses;
use App\Models\Budgets\Budget;
use App\Models\Invoices\Invoice;
use App\Models\Accounting\UnclassifiedIncome;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Imports\ExcelSimpleImport;

class TesoreriaContabilizarIa extends Controller
{
    // CONTABILIZAR TESORERIA CON IA
    // LEER XLSX

    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $client = Auth::user();

        return view('tesoreria.contabilizar-ia.contabilizar-ia', compact('client'));
    }

    private function excelDateToDate($excelSerial)
    {
        $unixTimestamp = ($excelSerial - 25569) * 86400; // 25569 = días entre 1/1/1900 y Unix Epoch
        return gmdate('Y-m-d', $unixTimestamp);
    }

    public function upload(Request $request)
    {
        try {
            if (!$request->hasFile('excel_file')) {
                throw new \Exception('No se ha proporcionado ningún archivo');
            }

            $client = Auth::user();

            $prompt = "Eres un asistente contable. Recibirás un conjunto de movimientos bancarios en formato JSON (cada fila es un array asociativo con campos como 'fecha valor', 'concepto', 'beneficiario', 'debe', 'haber', importe.).
            Tu tarea es:
            1. Detectar automáticamente si el movimiento es un \"ingreso\" (si hay un valor en 'haber') o un \"gasto\" (si hay un valor en 'debe').
            - El tipo de gasto (ingreso o gasto) puede deducirse por DEBE o HABER, INGRESO o GASTO, importe positivo o negativo etc...Nunca habra dos formas de identificar por archivo, solo estara presente una de ellas.
            - Si el importe es negativo, se catalogara como gasto pero debes devolver SIEMPRE un importe positivo.
            ES MUY IMPORTANTE QUE COMPRUEBES BIEN SI ES UN GASTO O INGRESO, Y NO FALLAR
            2. Ignorar cabeceras, nulos o filas irrelevantes.
            3. Para cada movimiento válido, devolver un objeto con los siguientes campos:
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

            Una vez
            Aquí están los datos a analizar:";

            $import = new ExcelSimpleImport();
            Excel::import($import, $request->file('excel_file'));

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

                foreach ($movimientos['movimientos'] as &$movimiento) {
                    if (is_numeric($movimiento['received_date'])) {
                        $movimiento['received_date'] = $this->excelDateToDate($movimiento['received_date']);
                    }
                }
                unset($movimiento); // romper referencia

                foreach ($movimientos['movimientos'] as &$movimiento) {
                    $fechaLimpia = trim(substr($movimiento['received_date'], 0, 10));
                    $movimiento['received_date'] = Carbon::createFromFormat('d/m/Y', $fechaLimpia);

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
                                    $q->where('total', $movimiento['amount'])->orWhere('total', $movimiento['amount'] * 0.5);
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

                    } else {
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


                return response()->json([
                    'success' => true,
                    'message' => 'Datos procesados correctamente',
                    'data' => $movimientos
                ]);

            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                throw new \Exception('Error al comunicarse con la API de OpenAI: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showGenerico(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $client = Auth::user();

        $tabla = $request->input('tabla');
        $id = $request->input('id');

        switch ($tabla) {
            case 'Factura':
                return redirect()->route('factura.edit', $id);
            case 'Ingreso':
                return redirect()->route('ingreso.edit', $id);
            case 'Gasto':
                return redirect()->route('gasto.edit', $id);
            case 'Gasto asociado':
                return redirect()->route('gasto-asociado.edit', $id);
            case 'Presupuesto':
                return redirect()->route('presupuesto.edit', $id);
            default:
                abort(404, 'Tabla no reconocida');
        }
    }

    public function acceptCoincidencias(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $client = Auth::user();

        $unclassifiedId = $request->input('unclassified_id');
        $tabla = $request->input('tabla');
        $coincidenciaId = $request->input('coincidencia_id');
        $tipo = $request->input('tipo');

        if ($tipo == 'ingreso') {
            // Obtener informacion de unclassified income
            $unclassified = UnclassifiedIncome::find($unclassifiedId);


            $bank = strtoupper($unclassified->bank);
            switch ($bank) {
                case 'BBVA':
                    $bank = 4;
                    break;
                case 'SABADELL':
                    $bank = 2;
                    break;
                case 'BANKINTER':
                    $bank = 3;
                    break;
                default:
                    $bank = null;
                    break;
            }
            if (!$unclassified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: No se encontró la coincidencia'
                ], 404);
            }
            // Comprobar si existe el invoice y obtener su id
            $received_date = Carbon::parse($unclassified->received_date)->format('Y-m-d');
            $invoice = Invoice::find($coincidenciaId);
            if ($invoice) {
                $budgetId = $invoice->budget_id;
                if ($budgetId) {
                    $budget = Budget::find($budgetId);
                    if ($budget) {
                        if($unclassified->parcial){
                            $budget->budget_status_id = '7';
                            $invoice->invoice_status_id = '4';
                        } else {
                            $budget->budget_status_id = '6';
                            $invoice->invoice_status_id = '3';
                        }

                        $budget->save();
                        $invoice->save();
                    } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se ha encontrado un presupuesto'
                    ], 404);
                    }
                }
                $ingreso = new Ingreso();
                $ingreso->quantity = $unclassified->amount;
                $ingreso->date = Carbon::parse($unclassified->received_date)->format('Y-m-d');
                $ingreso->title = $unclassified->message;
                $ingreso->bank_id = $bank;
                $ingreso->save();
            } else {
                $budget = Budget::find($coincidenciaId);
                if ($budget) {
                    if($unclassified->parcial){
                        $budget->budget_status_id = '7';
                    } else {
                        $budget->budget_status_id = '6';
                    }
                    $budget->save();
                    $unclassified->status = 1;
                    $unclassified->save();
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se ha encontrado un presupuesto'
                    ], 404);
                }
                    $ingreso = new Ingreso();
                    $ingreso->quantity = $unclassified->amount;
                    $ingreso->date = Carbon::parse($unclassified->received_date)->format('Y-m-d');
                    $ingreso->title = $unclassified->message;
                    $ingreso->bank_id = $bank;
                    $ingreso->save();
            }
            $unclassified->status = 1;
            $unclassified->save();


        } elseif ($tipo == 'gasto') {
            $unclassified = UnclassifiedExpenses::find($unclassifiedId);

            $bank = strtoupper($unclassified->bank);
            switch ($bank) {
                case 'BBVA':
                    $bank = 4;
                    break;
                case 'SABADELL':
                    $bank = 2;
                    break;
                case 'BANKINTER':
                    $bank = 3;
                    break;
                default:
                    $bank = null;
                    break;
            }

            if (AssociatedExpenses::where('id', $unclassified->id)->exists()) {
                $gasto = AssociatedExpenses::find($unclassified->id);
                $gasto->state = 'PAGADO';
                $gasto->save();
            }
        }

        if (!$unclassified) {
            return response()->json([
                'success' => false,
                'message' => 'Error: No se encontró la coincidencia'
            ], 404);
        }

        $unclassified->status = 1;
        $unclassified->save();

        return response()->json([
            'success' => true,
            ]);
        }

    public function storeGasto(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $client = Auth::user();

        // data: {
        //     _token: "{{ csrf_token() }}",
        //     company_name: company,
        //     bank: bank,
        //     iban: iban,
        //     amount: amount,
        //     date: date,
        //     description: message
        // }
        $bank = strtoupper($request->input('bank'));
            switch ($bank) {
                case 'BBVA':
                    $bank = 4;
                    break;
                case 'SABADELL':
                    $bank = 2;
                    break;
                case 'BANKINTER':
                    $bank = 3;
                    break;
                default:
                    $bank = null;
                    break;
            }

        $gasto = new Gasto();
        $gasto->title = $request->input('title');
        $gasto->reference = $request->input('title');
        $gasto->total = $request->input('total');
        $gasto->received_date = $request->input('received_date');
        $gasto->bank_id = $bank;
        $gasto->payment_method_id = 12;
        $gasto->state = 'PAGADO';
        $gasto->save();

        UnclassifiedExpenses::where('id', $request->input('unclassified_id'))->update(['status' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Gasto creado correctamente',
            'gasto_id' => $gasto->id
        ], 200);
    }


}
