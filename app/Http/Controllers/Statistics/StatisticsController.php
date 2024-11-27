<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Budgets\Budget;
use App\Models\Tasks\Task;
use App\Models\Clients\Client;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\Accounting\Gasto;
use App\Models\PurcharseOrde\PurcharseOrder;
use App\Models\Services\ServiceCategories;
use App\Models\Invoices\Invoice;
use App\Models\Statistics\Statistics;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;

class StatisticsController extends Controller
{

    public function mesFiltro($mes, $anio)
    {
        ini_set('memory_limit', '9024M');

        $dataBudgets = $this->proyectosActivos();
        $dataIvaAnual = $this->ivaAnual($anio);
        $dataIva = $this->iva($mes,$anio);
        $dataGastosComunes = $this->gastosComunes($mes, $anio);
        $dataGastosComunesAnual = $this->gastosComunesAnual($anio);
        $dataFacturacion = $this->invoices($mes, $anio);
        $dataFacturacionAnno = $this->invoicesYear($anio);
        $dataAsociados = $this->gastosAsociados($mes, $anio);
        $dataAsociadosAnual = $this->gastosAsociadosAnual($anio);
        $departamentos = $this->departamentosFacturacionMes($mes, $anio);
        $departamentosBeneficios = $this->beneficioDepartamentos($mes, $anio);
        $userProductivity = $this->productividadEmpleados($mes, $anio);
        $productivityValues = collect($userProductivity)->pluck('productividad')->toArray();
        $totalBeneficio = $this->calcularTotalBeneficio($anio);
        $iva = $this->trimestreIva($mes, $anio);
        $anioActual = date("Y");
        $arrayAnios = [];
        for ($a = 2010; $a <= $anioActual; $a++) {
            $arrayAnios[] = $a;
        }
        $countTotalBudgets = $this->budgets();
        $totalBeneficioAnual = array_sum($totalBeneficio); // Suma total de los beneficios anuales
        // Definir todos los meses en orden
        $allMonths = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        // Obtener el número del mes actual
        $currentMonthIndex = date('n') - 1; // date('n') devuelve el mes sin ceros iniciales (1 = Enero, 12 = Diciembre), y restamos 1 para obtener el índice del arreglo
        $currentMonth = date('n'); // Mes actual en número (1 = Enero, ..., 12 = Diciembre)

        // Extraer los meses desde el comienzo del año hasta el mes actual
        $monthsToActually = array_slice($allMonths, 0, $currentMonthIndex + 1);        // Calcular la facturación mensual
        $billingMonthly = [];
        foreach (range(1,$currentMonth) as $month) {
            $billingMonthly[] = Invoice::whereMonth('created_at', $month)
                ->whereYear('created_at', $anio)
                ->sum('total');
        }
        $allArray = [];
        foreach ($arrayAnios as $year) {
            $annualData = [];
            if ($year < '2023'){
                $all = Statistics::where('year', (int)$year)->pluck('quantity')->toArray();
                $allArray[$year] = $all;
            }else{
            foreach (range(1, 12) as $month) {
                $annualData[] = Invoice::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('total');
            }
            $allArray[$year] = $annualData;
            }
        }

        // Inicializa un array para almacenar la suma de facturación por mes y el contador de años con datos
        $monthlyTotals = array_fill(0, 12, 0);
        $monthlyCounts = array_fill(0, 12, 0);

        foreach ($allArray as $year => $monthlyData) {
            foreach ($monthlyData as $index => $amount) {
                // Acumula el total para cada mes y cuenta los años con datos
                $monthlyTotals[$index] += $amount;
                $monthlyCounts[$index]++;
            }
        }

        // Calcula la media mensual dividiendo cada total entre el número de años con datos
        $monthlyAverages = [];
        foreach ($monthlyTotals as $index => $total) {
            $monthlyAverages[$index + 1] = $monthlyCounts[$index] ? $total / $monthlyCounts[$index] : 0;
        }

        $monthlyAveragesValues = array_values($monthlyAverages);



        $nameUsers = collect($userProductivity)->pluck('name')->toArray();


        return view('statistics.index', compact(
            'dataBudgets', 'dataGastosComunes', 'dataGastosComunesAnual',
            'dataFacturacion', 'dataFacturacionAnno', 'dataAsociados',
            'dataAsociadosAnual', 'departamentos', 'departamentosBeneficios',
            'userProductivity', 'iva', 'totalBeneficio', 'arrayAnios',
            'anioActual','countTotalBudgets','totalBeneficioAnual',
            'monthsToActually','billingMonthly','allArray',
            'nameUsers','productivityValues','monthlyAveragesValues','dataIvaAnual','dataIva'
        ));
    }

    public function index(Request $request)
    {
        $date = Carbon::parse($request->mes) ?? Carbon::now();
        $mes = $date->format('m');
        $year = $date->year;
        return $this->mesFiltro($mes, $year);
    }

    // Definición del método que faltaba
    public function getBillingMonthly($anio)
    {
        // Aquí recuperas la facturación mensual agrupada por mes
        return Invoice::whereYear('created_at', $anio)
            ->whereIn('invoice_status_id', [1,3, 4])
            ->select(DB::raw('MONTH(created_at) as mes'), DB::raw('SUM(total) as total'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();
    }

    public function getExpensesMonthly($anio)
    {
        // Aquí recuperas los gastos mensuales agrupados por mes
        return Gasto::where(function($query) {
                $query->where('transfer_movement', 0)
                    ->orWhereNull('transfer_movement');
            })
            ->whereYear('received_date', $anio)
            ->select(DB::raw('MONTH(received_date) as mes'), DB::raw('SUM(quantity) as total'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();
    }

    public function getAssociatedExpensesMonthly($anio)
    {
        // Aquí recuperas los gastos asociados mensuales agrupados por mes
        return AssociatedExpenses::whereYear('received_date', $anio)
            ->select(DB::raw('MONTH(received_date) as mes'), DB::raw('SUM(quantity) as total'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();
    }

    protected function calcularTotalBeneficio($anio)
    {
        $facturacion = $this->getBillingMonthly($anio);
        $gastos = $this->getExpensesMonthly($anio);
        $gastosAsociados = $this->getAssociatedExpensesMonthly($anio);

        //dd(array_sum($facturacion) - array_sum($gastos) - array_sum($gastosAsociados));
        return array_map(function ($facturado, $gasto, $gastoAsociado) {
            return $facturado - ($gasto ?? 0) - ($gastoAsociado ?? 0);
        }, $facturacion, $gastos, $gastosAsociados);
    }

    public function getNotAccomplished()
    {
        $users = User::whereIn('access_level_id', [2, 5])->where('inactive', 0)->get();
        return $users->filter(function ($user) {
            $totalProductivity = $this->getProductivityByUser($user)[0];
            $onTime = $this->getHorasEntrada($user);
            return $totalProductivity < 60 || $onTime != 10;
        })->map(fn($user) => $user->name . " " . $user->surname)->toArray();
    }

    public function getProductivityByUser($user)
    {
        $startDate = Carbon::now();
        $tareasFinalizadas = Task::where("admin_user_id", $user->id)
            ->where("task_status_id", 3)
            ->whereBetween('updated_at', [$startDate->startOfMonth(), $startDate->endOfMonth()])
            ->get();

        $totalEstimadas = $tareasFinalizadas->sum(fn($tarea) => $this->getTiempoEstimadoTareaEnHoras($tarea->estimated_time));
        $totalReales = $tareasFinalizadas->sum(fn($tarea) => $this->getTiempoRealTareaEnHoras($tarea->real_time));

        $mProductividadEsteMes = $totalReales > 0
            ? number_format(($totalEstimadas / $totalReales) * 100, 2, ",", ".")
            : number_format(($totalEstimadas / 1) * 100, 2, ",", ".");

        return [intval($mProductividadEsteMes)];
    }

    public function getHorasEntrada($usuario)
    {
        $horasEntradas = DB::table('hourly_average')
            ->whereRaw('yearweek(DATE(created_at), 1) = yearweek(curdate(), 1)')
            ->where('admin_user_id', $usuario->id)
            ->pluck('hours');

        return $horasEntradas->contains(fn($hora) => intval(explode(":", $hora)[0]) >= 9) ? 0 : 10;
    }

    public function getProductivityAll()
    {
        return User::where('access_level_id', 5)->where('inactive', 0)->get()
            ->map(fn($user) => $this->getProductivityByUser($user)[0])->toArray();
    }

    public function getNameUsers()
    {
        return User::where('access_level_id', 5)->where('inactive', 0)
            ->pluck(DB::raw("CONCAT(name, ' ', surname)"))->toArray();
    }

    public function proyectosActivos()
    {
        $proyectos = Budget::whereIn('budget_status_id', [3, 7])->get();
        return [
            'ProjectsActive' => $proyectos,
            'total' => $proyectos->count(),
        ];
    }

    public function budgets()
    {
        return Budget::whereIn('budget_status_id', [3, 5, 7])->sum('base');
    }

    public function invoicesMes($mes, $year)
    {
        return Invoice::whereMonth('created_at', $mes)
            ->whereYear('created_at', $year)
            ->get();
    }

    public function invoicesYear($year)
    {
        $facturas = Invoice::whereYear('created_at', $year)
            ->whereIn('invoice_status_id', [1,3, 4])
            ->get();

        return [
            'facturas' => $facturas,
            'total' => $facturas->sum('total'),
        ];
    }

    public function invoices($mes, $year)
    {
        $facturas = Invoice::whereMonth('created_at', $mes)
            ->whereYear('created_at', $year)
            ->whereIn('invoice_status_id', [1,3, 4])
            ->get();

        return [
            'facturas' => $facturas,
            'total' => $facturas->sum('total'),
        ];
    }

    public function gastosComunes($mes, $year)
    {
        $gastosComunesMes = DB::table('gastos')
            ->whereMonth('received_date', $mes)
            ->whereYear('received_date', $year)
            ->whereNull('deleted_at')
            ->where(function($query) {
                $query->where('transfer_movement', 0)
                      ->orWhereNull('transfer_movement');
            })
            ->whereNotNull('iva') // Filtra que iva no sea null
            ->where('iva', '<>', 0) // Filtra que iva sea distinto de 0
            ->get();

        return [
            'gastos' => $gastosComunesMes,
            'total' => $gastosComunesMes->sum('quantity'),
        ];
    }

    public function gastosComunesAnual($year)
    {
        $gastosComunesAnual = DB::table('gastos')
            ->whereYear('received_date', $year)
            ->whereNull('deleted_at')
            ->where(function($query) {
                $query->where('transfer_movement', 0)
                      ->orWhereNull('transfer_movement');
            })
            ->get();

        return [
            'gastos' => $gastosComunesAnual,
            'total' => $gastosComunesAnual->sum('quantity'),
        ];
    }
    public function iva($mes, $year)
    {
        $gastosComunesMes = DB::table('gastos')
            ->whereMonth('received_date', $mes)
            ->whereYear('received_date', $year)
            ->whereNull('deleted_at')
            ->where(function($query) {
                $query->where('transfer_movement', 0)
                      ->orWhereNull('transfer_movement');
            })
            ->whereNotNull('iva')
            ->where('iva', '>', 0)
            ->get();

        $gastosAsociados = AssociatedExpenses::whereMonth('received_date', $mes)
            ->whereYear('received_date', $year)
            ->whereNotNull('iva')
            ->where('iva', '>', 0)
            ->get();

        $ivaGastosComunes = $gastosComunesMes->sum(function ($gasto) {
            return $gasto->quantity * ($gasto->iva / 100);
        });


        $ivaGastosAsociados = $gastosAsociados->sum(function ($gasto) {
            return $gasto->quantity * ($gasto->iva / 100);
        });

        // Sumar ambos totales de IVA
        $totalIva = $ivaGastosComunes + $ivaGastosAsociados;

        return $totalIva;
    }

    public function ivaAnual($year)
    {
        $gastosComunesAnual = DB::table('gastos')
            ->whereYear('received_date', $year)
            ->whereNull('deleted_at')
            ->where(function($query) {
                $query->where('transfer_movement', 0)
                      ->orWhereNull('transfer_movement');
            })
            ->whereNotNull('iva') // Solo registros donde IVA no sea null
            ->where('iva', '>', 0) // Solo registros donde IVA sea mayor que 0
            ->get();

        $gastosAsociados = AssociatedExpenses::whereYear('received_date', $year)
            ->whereNotNull('iva')
            ->where('iva', '>', 0)
            ->get();

        $ivaGastosComunes = $gastosComunesAnual->sum(function ($gasto) {
            return $gasto->quantity * ($gasto->iva / 100);
        });

        $ivaGastosAsociados = $gastosAsociados->sum(function ($gasto) {
            return $gasto->quantity * ($gasto->iva / 100);
        });

        // Sumar ambos totales de IVA
        $totalIva = $ivaGastosComunes + $ivaGastosAsociados;

        return $totalIva;
    }

    public function gastosAsociados($mes, $year)
    {

        $gastosAsociados = AssociatedExpenses::whereMonth('received_date', $mes)
        ->whereYear('received_date', $year)
        ->get();

        return [
            'array' => $gastosAsociados,
            'total' => $gastosAsociados->sum('quantity'),
        ];
        // $facturas = Invoice::whereMonth('created_at', $mes)
        //     ->whereYear('created_at', $year)
        //     ->whereIn('invoice_status_id', [3, 4])
        //     ->get();

        // $gastosAsociadosTotal = 0;
        // $arrayOrdenesCompra = [];

        // $facturas->each(function ($factura) use (&$gastosAsociadosTotal, &$arrayOrdenesCompra) {
        //     $budget = Budget::find($factura['budget_id']);
        //     if($budget){
        //         $cliente = Client::find($budget->client_id)->name;

        //         $budget->budgetConcepts->each(function ($concept) use ($budget, $factura, $cliente, &$gastosAsociadosTotal, &$arrayOrdenesCompra) {
        //             if ($concept->concept_type_id == 1 && $concept->purchase_price != '') {
        //                 $gastosAsociadosTotal += $concept->purchase_price;
        //                 $concept->budgetConcep = $budget->budgetConcepts;
        //                 $concept->budgetComparar = $budget;
        //                 $concept->client = $cliente;
        //                 $concept->idinvoices = $factura->id;
        //                 $concept->invoice = $factura;
        //                 $concept->asociate = AssociatedExpenses::where('budget_id', $budget->id)
        //                     ->whereMonth('received_date', $factura->created_at->format('m'))
        //                     ->whereYear('received_date', $factura->created_at->format('Y'))
        //                     ->get();
        //                 $arrayOrdenesCompra[] = $concept;
        //             }
        //         });
        //     }
        // });
        // return [
        //     'array' => $arrayOrdenesCompra,
        //     'total' => $gastosAsociadosTotal,
        // ];
    }

    public function gastosAsociadosAnual($year)
    {

        $gastosAsociados = AssociatedExpenses::whereYear('received_date', $year)->get();

        return [
            'array' => $gastosAsociados,
            'total' => $gastosAsociados->sum('quantity'),
        ];

        // $facturas = Invoice::whereYear('created_at', $year)
        //     ->whereIn('invoice_status_id', [3, 4])
        //     ->get();

        // $gastosAsociadosTotal = 0;
        // $arrayOrdenesCompra = [];

        // $facturas->each(function ($factura) use (&$gastosAsociadosTotal, &$arrayOrdenesCompra) {
        //     $budget = Budget::find($factura['budget_id']);
        //     if($budget){
        //         $cliente = Client::find($budget->client_id)->name;

        //         $budget->budgetConcepts->each(function ($concept) use ($budget, $factura, $cliente, &$gastosAsociadosTotal, &$arrayOrdenesCompra) {
        //             if ($concept->concept_type_id == 1 && $concept->purchase_price != '') {
        //                 $gastosAsociadosTotal += $concept->purchase_price;
        //                 $concept->budgetConcep = $budget->budgetConcepts;
        //                 $concept->budgetComparar = $budget;
        //                 $concept->client = $cliente;
        //                 $concept->idinvoices = $factura->id;
        //                 $concept->invoice = $factura;
        //                 $arrayOrdenesCompra[] = $concept;
        //             }
        //         });
        //     }
        // });
        // return [
        //     'array' => $arrayOrdenesCompra,
        //     'total' => $gastosAsociadosTotal,
        // ];

    }

    public function orderPago()
    {
        $result = PurcharseOrder::all();
        return view('admin.orderPago.index', compact('result'));
    }

    // public function getOrderPago()
    // {
    //     $ordenes = PurcharseOrder::select('payment_method_id', 'project_id', 'client_id', 'budget_concept_id', 'amount', 'status');
    //     return Datatables::of($ordenes)->make();
    // }

    public function beneficioDepartamentos($mes, $year)
    {
        $facturas = $this->invoices($mes, $year)['facturas']->toArray();
        $departamentos = [];

        foreach ($facturas as $factura) {
            $budget = Budget::find($factura['budget_id']);
            if($budget){
                foreach ($budget->budgetConcepts as $concept) {
                    $nameService = ServiceCategories::find($concept->services_category_id)->name ?? 'Sin Categoría';

                    $key = array_search($concept->services_category_id, array_column($departamentos, 'id'));

                    if ($key !== false) {
                        $departamentos[$key]['total'] += $concept->concept_type_id == 1
                            ? $concept->total - $concept->purchase_price
                            : $concept->total;
                    } else {
                        $departamentos[] = [
                            'id' => $concept->services_category_id,
                            'name' => $nameService,
                            'total' => $concept->concept_type_id == 1
                                ? $concept->total - $concept->purchase_price
                                : $concept->total
                        ];
                    }
                }
            }
        }

        return [$departamentos];
    }

    public function departamentosFacturacionMes($mes, $year)
    {
        $facturas = $this->invoices($mes, $year)['facturas']->toArray();
        $departamentos = [];

        foreach ($facturas as $factura) {
            $budget = Budget::find($factura['budget_id']);
            if($budget){
                foreach ($budget->budgetConcepts as $concept) {
                    $nameService = ServiceCategories::find($concept->services_category_id)->name ?? 'Sin Categoría';

                    $key = array_search($concept->services_category_id, array_column($departamentos, 'id'));

                    if ($key !== false) {
                        $departamentos[$key]['total'] += $concept->total;
                    } else {
                        $departamentos[] = [
                            'id' => $concept->services_category_id,
                            'name' => $nameService,
                            'total' => $concept->total
                        ];
                    }
                }
            }
        }

        return [array_merge($facturas, $departamentos), $departamentos];
    }

    protected function getTiempoEstimadoTareaEnHoras($tiempo_estimado)
    {
        if (!$tiempo_estimado) {
            return 0;
        }
        [$horas, $minutos, $segundos] = explode(':', $tiempo_estimado);
        return $horas + ($minutos / 60) + ($segundos / 3600);
    }

    protected function getTiempoRealTareaEnHoras($tiempo_real)
    {
        if (!$tiempo_real) {
            return 0;
        }
        [$horas, $minutos, $segundos] = explode(':', $tiempo_real);
        return $horas + ($minutos / 60) + ($segundos / 3600);
    }

    public function trimestreIva($mes, $year)
    {
        $trimestres = [
            'Primer Trimestre' => [$year . '-01-01', $year . '-03-31'],
            'Segundo Trimestre' => [$year . '-04-01', $year . '-06-30'],
            'Tercer Trimestre' => [$year . '-07-01', $year . '-09-30'],
            'Cuarto Trimestre' => [$year . '-10-01', $year . '-12-31'],
        ];

        return array_map(function ($fechas, $trimestre) use ($year) {
            $facturas = Invoice::whereBetween('created_at', $fechas)->get();

            $sumarIva = $facturas->sum('iva');
            $gastosAsociadosTotalIva = 0;

            foreach ($facturas as $item) {
                if (in_array($item->invoice_status_id, [1, 3, 4])) {
                    $budgetComparar = Budget::find($item->budget_id);
                    if($budgetComparar){
                        foreach ($budgetComparar->budgetConcepts as $concept) {
                            foreach ($concept->proveedor as $suplier) {
                                if ($concept->concept_type_id == 1 && $suplier->price != '') {
                                    $gastosAsociadosTotalIva += ($suplier->price * 21) / 100;
                                }
                            }
                        }
                    }
                }
            }

            return [
                'Trimestre' => $trimestre,
                'totalFacturasIva' => $sumarIva,
                'gastosAsociadosTotalIva' => $gastosAsociadosTotalIva,
            ];
        }, $trimestres, array_keys($trimestres));
    }

    public function productividadEmpleados($mes, $year)
    {
        $users = User::where('inactive', 0)->get();
        $horasMesProductivas = 175;
        $userProductivos = $users->where('access_level_id', 5)->count();
        $taskUserArray = [];

        foreach ($users as $usuario) {
            if ($usuario->access_level_id == 4) {
                $dataFacturacion = $this->invoices($mes, $year);
                $budgets = Budget::where("admin_user_id", $usuario->id)->whereYear('created_at', $year)->get();

                $totalFacturadoMes = collect($dataFacturacion['facturas'])
                    ->where('budget.admin_user_id', $usuario->id)
                    ->sum('total');

                $totalFacturadoAnio = $budgets->whereIn("budget_status_id", [3, 5, 6, 7])->sum('total');

                $taskUserArray[] = [
                    'id' => $usuario->id,
                    'name' => $usuario->name . ' ' . $usuario->surname,
                    'productividad' => count($budgets) ? number_format((count($budgets->whereIn("budget_status_id", [3, 5, 6, 7])) * 100) / count($budgets), 2, ",", ".") : 0,
                    'budgets' => $budgets->count(),
                    'noFacturados' => $budgets->where("budget_status_id", 4)->count(),
                    'totalFacturadoMes' => $totalFacturadoMes,
                    'totalFacturadoAnio' => $totalFacturadoAnio,
                    'budgetsAceptados' => $budgets->whereIn("budget_status_id", [3, 5, 6, 7])->count(),
                    'budgetsPendiente' => $budgets->whereIn("budget_status_id", [1, 2])->count(),
                    'budgetsAcceptsEuros' => $budgets->whereIn("budget_status_id", [3, 5, 6, 7])->sum('total'),
                ];
            } else {
                $tareasFinalizadas = Task::where("admin_user_id", $usuario->id)
                    ->where("task_status_id", 3)
                    ->whereMonth('updated_at', $mes)
                    ->whereYear('updated_at', $year)
                    ->get();

                $totalEstimadas = $tareasFinalizadas->sum(fn($tareas) => $this->getTiempoEstimadoTareaEnHoras($tareas->estimated_time));
                $totalReales = $tareasFinalizadas->sum(fn($tareas) => $this->getTiempoRealTareaEnHoras($tareas->real_time));

                $mProductividadEsteMes = $totalReales > 0
                    ? number_format(($totalEstimadas / $totalReales) * 100, 2, ",", ".")
                    : number_format(($totalEstimadas / 1) * 100, 2, ",", ".");

                $gastoscomunes = $this->gastosComunes($mes, $year);
                $total = $gastoscomunes['total'] / ($horasMesProductivas * $userProductivos);
                $inte = (intval($mProductividadEsteMes) / 100) * intval($totalReales);
                $fin = $inte * 76;

                $taskUserArray[] = [
                    'id' => $usuario->id,
                    'name' => $usuario->name . ' ' . $usuario->surname,
                    'productividad' => $mProductividadEsteMes,
                    'horasEstimadas' => $totalReales,
                    'horas' => $fin,
                    'total' => $total,
                    'numero' => $userProductivos,
                    'status' => $usuario->access_level_id,
                ];
            }
        }

        return $taskUserArray;
    }
}
