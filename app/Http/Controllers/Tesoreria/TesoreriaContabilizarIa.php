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



    public function upload(Request $request)
    {
        try {
            if (!$request->hasFile('excel_file')) {
                throw new \Exception('No se ha proporcionado ningún archivo');
            }

            $file = $request->file('excel_file');
            $today = Carbon::now()->format('Ymd_His');
            $fileName = 'COD1_' . $today . '.' . $file->getClientOriginalExtension();
            if (!Storage::exists('public/excel')) {
                Storage::makeDirectory('public/excel');
            }
            try {
                $path = Storage::putFileAs('public/excel', $file, $fileName);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir el archivo: ' . $e->getMessage()
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Archivo subido correctamente',
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el archivo: ' . $e->getMessage()
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