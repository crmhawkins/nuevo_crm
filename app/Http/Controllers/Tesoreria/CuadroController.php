<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Accounting\Gasto;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\Accounting\Ingreso;
use App\Models\Accounting\LastYearsBalance;
use App\Models\Accounting\Traspaso;
use App\Models\Other\BankAccounts;
use App\Models\Invoices\Invoice;
use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use Carbon\Carbon;

class CuadroController extends Controller
{

    /**
     * Mostrar el index de la tesorería
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $arrayresult = $this->calcBigArray($year, $month);

        $diasDelMes = $arrayresult['DiasDelMes'];
        $nameMonth = $arrayresult['nameMonth'];
        $banksAccounts = $arrayresult['bankAccounts'];
        $bigArray = $arrayresult['BigArray'];
        $month = $arrayresult['month'];
        $arrayTotal = $arrayresult['arrayTotal'];
        $arrayTotalPrevisto = $arrayresult['arrayTotalPrevisto'];
        $year = $arrayresult['year'];

        //Alerta Tesoreria Descubierta
        $contDias = 1;  //Contador de dias
        foreach ($arrayTotal['meses'][$month]['TOTAL'] as $totalDia) {
            if($totalDia>=0){
                $contDias++;
            }
        }

        //Una semana antes
        $contDias = $contDias - 7;
        if($contDias<0){
            $contDias = 1;
        }

        $alertsDescubierta = Alert::where('stage_id', 10)->get();

        $fechaNow = Carbon::now();
        $existAlert = false;	//Si existe alguna alerta hoy

        if($alertsDescubierta){
        	foreach ($alertsDescubierta as $alert) {
	        	if($alert->created_at->format('d') == $fechaNow->format('d')){
	        		$existAlert = true;
	        	}
        	}
        }

        if(!$existAlert){
            $dataAlert = [
                'admin_user_id' => 8,    //Elena Administracion
                'stage_id' => 10,
                'activation_datetime' => Carbon::create($year, $month, $contDias, 9, 0, 0)->format('Y-m-d H:i:s'),
                'status_id' => 1,
            ];

            $alert = Alert::create($dataAlert);
            $alertSaved = $alert->save();
        }


        /************** FACTURAS ****************/

        $globalArrayFactura = array();

        $facturas = $this->getFacturasThisMonth($year , date('m'));

        $PartialStatusArray = $this->getInvoicesPartialStatus($facturas, $month, $year);

        $globalArrayFactura['Facturas'] = $facturas;

        $globalArrayFactura['PartialStatus'] = $PartialStatusArray;

        //dd($globalArrayFactura);


        /*************** INGRESOS **************/

        $ingresos = $this->getIngresosThisMonth($year, date('m'));

        /**************** GASTOS ****************/

        $gastos = $this->getGastosThisMonth($year , date('m'));

        /*********** GASTOS ASOCIADOS ***********/

        $gastosAsociados = $this->getGastosAsociadosThisMonth($year , date('m'));

        return view('tesoreria.cuadro.index', compact('diasDelMes', 'nameMonth', 'banksAccounts', 'bigArray', 'month', 'arrayTotal', 'arrayTotalPrevisto', 'year', "day", 'globalArrayFactura', 'ingresos', 'gastos', 'gastosAsociados'));
    }

    public function indexYear($year)
    {

        $year = $year;
        $month = 01;
        $day = 01;

        $arrayresult = $this->calcBigArray($year, $month);

        $diasDelMes = $arrayresult['DiasDelMes'];
        $nameMonth = $arrayresult['nameMonth'];
        $banksAccounts = $arrayresult['bankAccounts'];
        $bigArray = $arrayresult['BigArray'];
        $month = $arrayresult['month'];
        $arrayTotal = $arrayresult['arrayTotal'];
        $arrayTotalPrevisto = $arrayresult['arrayTotalPrevisto'];
        $year = $arrayresult['year'];

        //Alerta Tesoreria Descubierta
        $contDias = 1;  //Contador de dias
        foreach ($arrayTotal['meses'][$month]['TOTAL'] as $totalDia) {
            if($totalDia>=0){
                $contDias++;
            }
        }
        $hasNegativeDay = false;
        foreach ($arrayTotal['meses'][$month]['TOTAL'] as $totalDia) {
            if ($totalDia < 0) {
                $hasNegativeDay = true;
                break;
            }
        }
        //Una semana antes
        $contDias = $contDias - 7;
        if($contDias<0){
            $contDias = 1;
        }

        $alertsDescubierta = Alert::where('stage_id', 10)->get();

        $fechaNow = Carbon::now();
        $existAlert = false;	//Si existe alguna alerta hoy

        if($alertsDescubierta){
        	foreach ($alertsDescubierta as $alert) {
	        	if($alert->created_at->format('d') == $fechaNow->format('d')){
	        		$existAlert = true;
	        	}
        	}
        }

        if(!$existAlert && $hasNegativeDay){
            $dataAlert = [
                'admin_user_id' => 8,    //Elena Administracion
                'stage_id' => 10,
                'activation_datetime' => Carbon::create($year, $month, $contDias, 9, 0, 0)->format('Y-m-d H:i:s'),
                'status_id' => 1,
            ];

            $alert = Alert::create($dataAlert);
            $alertSaved = $alert->save();
        }


        /************** FACTURAS ****************/

        $globalArrayFactura = array();

        $facturas = $this->getFacturasThisMonth($year, 01);

        $PartialStatusArray = $this->getInvoicesPartialStatus($facturas, $month, 01);

        $globalArrayFactura['Facturas'] = $facturas;

        $globalArrayFactura['PartialStatus'] = $PartialStatusArray;

        //dd($globalArrayFactura);


        /*************** INGRESOS **************/

        $ingresos = $this->getIngresosThisMonth($year , 01);

        /**************** GASTOS ****************/

        $gastos = $this->getGastosThisMonth($year , 01);

        /*********** GASTOS ASOCIADOS ***********/

        $gastosAsociados = $this->getGastosAsociadosThisMonth($year , 01);


        return view('tesoreria.cuadro.index', compact('diasDelMes', 'nameMonth', 'banksAccounts', 'bigArray', 'month', 'arrayTotal', 'arrayTotalPrevisto', 'year', "day", 'globalArrayFactura', 'ingresos', 'gastos', 'gastosAsociados'));
    }

    public function initArray($array){
        $banksAccounts = BankAccounts::all();

        foreach ($banksAccounts as $bankAccount) {
            $array['bancos'][$bankAccount->id]['id']= '';
            $array['bancos'][$bankAccount->id]['nombre'] = '';
            for ($j = 1; $j <= 12; $j++) {
                $diaFormat = sprintf("%02d", $j);
                $array['bancos'][$bankAccount->id][$j]['Ultimo'] = 0;
            }
        }
        return $array;
    }

    public function saveTotalBalance($array, $mes, $dias, $total){
        $array['meses'][$mes]['TOTAL'][$dias] = $total;

        return $array;
    }

    public function saveTotalBalancePrevisto($array, $mes, $dias, $total){
        $array['meses'][$mes]['TOTAL'][$dias] = $total;

        return $array;
    }

    public function putDaysAndMonths($dias, $nameMes, $array, $mes){
        $array['meses'][$mes]['NOMBRE'] = $nameMes;
        $array['meses'][$mes]['DIAS_TOTALES'] = $dias;

        return $array;
    }

    public function calcGasto($year, $mes, $diaFormat, $idBank, $gastoTotal){
        $dateFilter =  $year . '-' . $mes . '-' . $diaFormat;

        $gastos = Gasto::where('bank_id', $idBank)->where('date', $dateFilter)->get();
        if ($gastos) {
             //Recorremos todos los gastos de ese dia
            foreach ($gastos as $gasto) {
                if ($gasto->created_at >= Carbon::createFromFormat('d/m/Y', '17/10/2024'))
                {
                    if(isset($gasto->iva)){
                        $gastoTotal += $gasto->quantity * (1 + ($gasto->iva/100));
                    }else{
                        $gastoTotal += $gasto->quantity;
                    }
                }else{
                    $gastoTotal += $gasto->quantity;
                }
            }
        }
        return $gastoTotal;
    }

    public function calcAssociatedExpenses($year, $mes, $diaFormat, $idBank, $gastoTotal){
        $dateFilter =  $year . '-' . $mes . '-' . $diaFormat;

        $gastosAsociados = AssociatedExpenses::where('bank_id', $idBank)->where('state','PAGADO')->where('date', $dateFilter)->get();
        if ($gastosAsociados) {
             //Recorremos todos los gastos de ese dia
             foreach ($gastosAsociados as $gasto) {
                if ($gasto->created_at >= Carbon::createFromFormat('d/m/Y', '17/10/2024'))
                {
                    if(isset($gasto->iva)){
                        $gastoTotal += $gasto->quantity * (1 + ($gasto->iva/100));
                    }else{
                        $gastoTotal += $gasto->quantity;
                    }
                }else{
                    $gastoTotal += $gasto->quantity;
                }
            }
        }
        return $gastoTotal;
    }

    public function calcTraspaso($year, $mes, $diaFormat, $idBank, $ingresoTotal){
        $dateFilter =  $year . '-' . $mes . '-' . $diaFormat;

        $ingresos = Ingreso::where('bank_id', $idBank)->where('date', $dateFilter)->get();
        if ($ingresos) {
            //$ingresoTotal = 0;
            foreach ($ingresos as $ingreso) {
                $ingresoTotal += $ingreso->quantity;
            }
        }
        return $ingresoTotal;
    }
    public function calcIngreso($year, $mes, $diaFormat, $idBank, $ingresoTotal){
        $dateFilter =  $year . '-' . $mes . '-' . $diaFormat;

        $ingresos = Ingreso::where('bank_id', $idBank)->where('date', $dateFilter)->get();
        if ($ingresos) {
            //$ingresoTotal = 0;
            foreach ($ingresos as $ingreso) {
                $ingresoTotal += $ingreso->quantity;
            }
        }
        return $ingresoTotal;
    }

    public function saveBalance($totalbalance, $array, $mes, $idBank, $dia){
        $array['meses'][$mes]['bancos'][$idBank]['Balance'][$dia] = $totalbalance;

        return $array;
    }

    public function saveGasto($array, $mes, $idBank, $dias, $gasto, $diaFormat){
        $array['meses'][$mes]['bancos'][$idBank]['gastoMes'][$dias] = $diaFormat;
        $array['meses'][$mes]['bancos'][$idBank]['gastoMes'][$dias] = $gasto;

        return $array;

    }

    public function saveAssociatedExpenses($array, $mes, $idBank, $dias, $gasto, $diaFormat){
        $array['meses'][$mes]['bancos'][$idBank]['gastoMesAsociados'][$dias] = $diaFormat;
        $array['meses'][$mes]['bancos'][$idBank]['gastoMesAsociados'][$dias] = $gasto;

        return $array;

    }

    public function saveIngreso($array, $mes, $idBank, $dias, $diaFormat, $ingresos){
        $array['meses'][$mes]['bancos'][$idBank]['ingresosMes'][$dias] = $diaFormat;
        $array['meses'][$mes]['bancos'][$idBank]['ingresosMes'][$dias] = $ingresos;

        return $array;
    }

    public function savesBanks($array, $mes, $idBank, $nameBank){
        $array['meses'][$mes]['bancos'][$idBank]['id'] = $idBank;
        $array['meses'][$mes]['bancos'][$idBank]['nombre'] = $nameBank;

        return $array;
    }

    public function savelastData($array1, $id_bank, $name, $mes, $ultimosgastos){
        $array1['bancos'][$id_bank]['id']=$id_bank;
        $array1['bancos'][$id_bank]['nombre'] = $name;
        $array1['bancos'][$id_bank][$mes]['Ultimo'] = $ultimosgastos;

        return $array1;
    }

    public function getLastData($arrayBanks, $mes, $id_bank, $year){
        if ($mes!=1){
            $mes = $mes - 1;
            $ultmes = $arrayBanks['bancos'][$id_bank][$mes]['Ultimo'];
            return $ultmes;
        }else
        {
            $lastYearBalance = LastYearsBalance::where('year',$year)->get();

            foreach($lastYearBalance as $balance){

                if($balance->bank_id == $id_bank)
                {
                    return $balance->quantity;
                }
            }
        }
    }

    public function getMonth()
    {
        $dias = date('t');
        return $dias;
    }

    public function getNameMonthActual()
    {

        $mes = date("F");

        if ($mes == "January") $mes = "ENERO";
        if ($mes == "February") $mes = "FEBRERO";
        if ($mes == "March") $mes = "MARZO";
        if ($mes == "April") $mes = "ABRIL";
        if ($mes == "May") $mes = "MAYO";
        if ($mes == "June") $mes = "JUNIO";
        if ($mes == "July") $mes = "JULIO";
        if ($mes == "August") $mes = "AGOSTO";
        if ($mes == "September") $mes = "SEPTIEMBRE";
        if ($mes == "October") $mes = "OCTUBRE";
        if ($mes == "November") $mes = "NOVIEMBRE";
        if ($mes == "December") $mes = "DICIEMBRE";

        return $mes;
    }

    public function getNamesMonth($mes)
    {
        switch ($mes) {
            case '01':
                return 'ENERO';
                break;
            case '02':
                return 'FEBRERO';
                break;
            case '03':
                return 'MARZO';
                break;
            case '04':
                return 'ABRIL';
                break;
            case '05':
                return 'MAYO';
                break;
            case '06':
                return 'JUNIO';
                break;
            case '07':
                return 'JULIO';
                break;
            case '08':
                return 'AGOSTO';
                break;
            case '09':
                return 'SEPTIEMBRE';
                break;
            case '10':
                return 'OCTUBRE';
                break;
            case '11':
                return 'NOVIEMBRE';
                break;
            case '12':
                return 'DICIEMBRE';
                break;
        }
    }


    public function calcDaysMonth($month, $year){
        // calculate number of days in a month
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    public function calcBigArray($year, $month)
    {
        $arrayResult = [
            'nameMonth' => $this->getNamesMonth(sprintf("%02d", $month)),
            'bankAccounts' => BankAccounts::all(),
            'DiasDelMes' => $this->calcDaysMonth($month, $year),
            'BigArray' => [],
            'month' => sprintf("%02d", $month),
            'arrayTotal' => [],
            'arrayTotalPrevisto' => [],
            'year' => $year,
        ];
        $banksAccounts = BankAccounts::all();
        $bigArray = [];
        $arrayTotal = [];
        $arrayTotalPrevisto = [];
        $anteriorbank = $this->initArray([]);

        $fechaIVA = Carbon::createFromFormat('d/m/Y', '17/10/2024');

        // Agrupamos gastos, gastos asociados e ingresos con claves únicas "bank_id-fecha"
        $gastosPorFecha = Gasto::whereYear('date', $year)->get()->groupBy(function($gasto) {
            return $gasto->bank_id . '-' . Carbon::parse($gasto->date)->format('m-d');
        });
        //var_dump($gastosPorFecha);

        $gastosAsociadosPorFecha = AssociatedExpenses::where('state', 'PAGADO')
            ->whereYear('date', $year)
            ->get()
            ->groupBy(function($gasto) {
                return $gasto->bank_id . '-' . Carbon::parse($gasto->date)->format('m-d');
        });

        $ingresosPorFecha = Ingreso::whereYear('date', $year)->get()->groupBy(function($ingreso) {
            return $ingreso->bank_id . '-' . Carbon::parse($ingreso->date)->format('m-d');
        });

        $traspasoOutPorFecha = Traspaso::whereYear('fecha', $year)->get()->groupBy(function($traspaso) {
            return $traspaso->from_bank_id . '-' . Carbon::parse($traspaso->date)->format('m-d');
        });

        $traspasoInPorFecha = Traspaso::whereYear('fecha', $year)->get()->groupBy(function($traspaso) {
            return $traspaso->to_bank_id . '-' . Carbon::parse($traspaso->date)->format('m-d');
        });

        // Procesamiento principal
        for ($i = 1; $i <= 12; $i++) {
            $mesFormat = sprintf("%02d", $i);
            $diasMes = $this->calcDaysMonth($i, $year);
            $bigArray['meses'][$mesFormat] = [
                'nombre' => $this->getNamesMonth($mesFormat),
                'diasTotal' => $diasMes
            ];

            foreach ($arrayResult['bankAccounts'] as $bankAccount) {
                $gastoTotal = 0;
                $gastoC = 0;
                $gastoA = 0;
                $ingresoTotal = 0;
                $traspasoIn = 0;
                $traspasoOut = 0;

                for ($j = 1; $j <= $diasMes; $j++) {
                    $diaFormat = sprintf("%02d", $j);
                    $dateKey = "{$bankAccount->id}-{$mesFormat}-{$diaFormat}";

                    // Balance del mes anterior
                    $totalGastosIngresos = $this->getLastData($anteriorbank, $i, $bankAccount->id, $year);
                    $bigArray = $this->savesBanks($bigArray, $mesFormat, $bankAccount->id, $bankAccount->name);

                    // Gastos Asociados con IVA aplicado si corresponde
                    $gastoA += isset($gastosAsociadosPorFecha[$dateKey])
                        ? $gastosAsociadosPorFecha[$dateKey]->sum(function($gasto) use ($fechaIVA) {
                            return $gasto->quantity * ($gasto->created_at >= $fechaIVA ? 1 + ($gasto->iva / 100) : 1);
                        })
                        : 0;
                    $bigArray = $this->saveAssociatedExpenses($bigArray, $mesFormat, $bankAccount->id, $j, $gastoA, $diaFormat);

                    // Gastos Comunes con IVA aplicado si corresponde
                    $gastoC += isset($gastosPorFecha[$dateKey])
                        ? $gastosPorFecha[$dateKey]->sum(function($gasto) use ($fechaIVA) {
                            return $gasto->quantity * ($gasto->created_at >= $fechaIVA ? 1 + ($gasto->iva / 100) : 1);
                        })
                        : 0;

                    $bigArray = $this->saveGasto($bigArray, $mesFormat, $bankAccount->id, $j, $gastoC, $diaFormat);

                    // Ingresos
                    $ingresoTotal += isset($ingresosPorFecha[$dateKey])
                        ? $ingresosPorFecha[$dateKey]->sum('quantity')
                        : 0;
                    $bigArray = $this->saveIngreso($bigArray, $mesFormat, $bankAccount->id, $j, $diaFormat, $ingresoTotal);

                    // Traspasos
                    $traspasoIn += isset($traspasoInPorFecha[$dateKey])
                        ? $traspasoInPorFecha[$dateKey]->sum('amount')
                        : 0;
                    $traspasoOut += isset($traspasoOutPorFecha[$dateKey])
                        ? $traspasoOutPorFecha[$dateKey]->sum('amount')
                        : 0;

                    // Cálculo diario del gasto total, balance y acumulación del balance diario
                    $gastoTotal = $gastoA + $gastoC + $traspasoOut;
                    $balance = $ingresoTotal + $traspasoIn - $gastoTotal ;
                    $totalGastosIngresos += $balance;
                    $bigArray = $this->saveBalance($totalGastosIngresos, $bigArray, $mesFormat, $bankAccount->id, $j);

                    // Guardar último balance para el mes siguiente
                    $anteriorbank = $this->savelastData($anteriorbank, $bankAccount->id, $bankAccount->name, $i, $totalGastosIngresos);
                }
            }
        }
        //dd($bigArray);
        // Cálculo de los totales acumulados de cada mes
        $arrayTotal = $this->calculateMonthlyTotals($bigArray, $arrayResult['bankAccounts'], $arrayTotal, $year);
        // Cálculo de los totales previstos basados en las facturas pendientes
        $aux = 0;
        $facturas = $this->getAllInvoices($year);

        foreach (range(1, 12) as $i) {
            $mesFormat = sprintf("%02d", $i);
            $dias = $this->calcDaysMonth($i, $year);
            $nameMes = $this->getNamesMonth($i);
            $arrayTotalPrevisto = $this->putDaysAndMonths($dias, $nameMes, $arrayTotalPrevisto, $mesFormat);

            for ($j = 1; $j <= $dias; $j++){
                $diaFormat = sprintf("%02d", $j);

                foreach($banksAccounts as $bankAccount){
                    $balance += $bigArray['meses'][$mesFormat]['bancos'][$bankAccount->id]['Balance'][$j];
                }


                foreach($facturas as $factura){
                    $dia = Carbon::parse($factura->paid_date)->format('d');
                    $mes = Carbon::parse($factura->paid_date)->format('m');

                    if($dia == $j && $mes == $i){
                        if($factura->invoice_status_id == 1 || $factura->invoice_status_id == 4){
                            $balance = 0;
                            if($factura->invoice_status_id == 4){
                                $facturaTotal = (float)$factura->total - (float)$factura->paid_amount;
                            }else{
                                $facturaTotal = (float)$factura->total;
                            }

                            $balance += (float)$facturaTotal + $aux;

                            $aux += (float)$facturaTotal;

                        }
                    }else{
                        $balance = $aux;
                    }
                }
                $balance += $arrayTotal['meses'][$mesFormat]['TOTAL'][$j];

                $arrayTotalPrevisto = $this->saveTotalBalancePrevisto($arrayTotalPrevisto, $mesFormat, $j, $balance);

            }
        }

        // Asignación a array de resultado final
        $arrayResult['BigArray'] = $bigArray;
        $arrayResult['arrayTotal'] = $arrayTotal;
        $arrayResult['arrayTotalPrevisto'] = $arrayTotalPrevisto;

        return $arrayResult;
    }


    // Método auxiliar para calcular los totales mensuales
    protected function calculateMonthlyTotals($bigArray, $bankAccounts, $arrayTotal, $year)
    {
        for ($i = 1; $i <= 12; $i++) {
            $mesFormat = sprintf("%02d", $i);
            $dias = $this->calcDaysMonth($i, $year);
            $arrayTotal = $this->putDaysAndMonths($dias, $this->getNamesMonth($mesFormat), $arrayTotal, $mesFormat);

            for ($j = 1; $j <= $dias; $j++) {
                $balance = 0;
                foreach ($bankAccounts as $bankAccount) {
                    $balance += $bigArray['meses'][$mesFormat]['bancos'][$bankAccount->id]['Balance'][$j] ?? 0;
                }
                $arrayTotal = $this->saveTotalBalance($arrayTotal, $mesFormat, $j, $balance);
            }
        }
        return $arrayTotal;
    }

    // Método auxiliar para calcular los totales previstos
    protected function calculatePredictedTotals($bigArray, $arrayTotal, $year)
    {
        $arrayTotalPrevisto = [];
        $aux = 0;
        $facturas = $this->getAllInvoices($year);

        foreach (range(1, 12) as $i) {
            $mesFormat = sprintf("%02d", $i);
            $dias = $this->calcDaysMonth($i, $year);
            $arrayTotalPrevisto = $this->putDaysAndMonths($dias, $this->getNamesMonth($mesFormat), $arrayTotalPrevisto, $mesFormat);

            foreach (range(1, $dias) as $j) {
                $balance = $arrayTotal['meses'][$mesFormat]['TOTAL'][$j] ?? 0;

                foreach ($facturas as $factura) {
                    $diaFactura = Carbon::parse($factura->paid_date)->format('d');
                    $mesFactura = Carbon::parse($factura->paid_date)->format('m');

                    if ($diaFactura == $j && $mesFactura == $mesFormat) {
                        $facturaTotal = ($factura->invoice_status_id == 4)
                            ? (float)$factura->total - (float)$factura->paid_amount
                            : (float)$factura->total;
                        $balance += $facturaTotal + $aux;
                        $aux += $facturaTotal;
                    }
                }

                $arrayTotalPrevisto = $this->saveTotalBalancePrevisto($arrayTotalPrevisto, $mesFormat, $j, $balance);
            }
        }

        return $arrayTotalPrevisto;
    }



    public function getMonthYear ($year, $month){

        if (!empty($month) && !empty($year)){
            $arrayresult = $this->calcBigArray($year, $month);

            $ingresos = Ingreso::whereMonth('date', $month )
            ->whereYear('date', '=', $year)
            ->orderBy('date', 'asc')
            ->get();

            $gastos = Gasto::whereMonth('date', $month )
            ->whereYear('date', '=', $year)
            ->orderBy('date', 'asc')
            ->get();

            $gastosAsociados = AssociatedExpenses::where('state','PAGADO')
            ->whereYear('date', $year)
            ->whereMonth('date', $month )
            ->orderBy('date', 'asc')
            ->get();

            return response()->json([
            'result' => $arrayresult,
            'ingresos' => $ingresos,
            'gastos' => $gastos,
            'gastosAsociados' => $gastosAsociados,
            ]);
        }
    }


/********************* PREVISION ********************/

    public function getFacturasThisMonth($year , $currentMonth){

        $data = [];

        $dataNoPagadas = Invoice::whereNull('paid_date')
        ->where('invoice_status_id', '<>', '5')
        ->whereYear('paid_date', '=', $year)
        ->orderBy('created_at', 'asc')
        ->get();

        //dd($dataNoPagadas);
        $dataPasadas = Invoice::whereRaw('MONTH(paid_date) = ?', [$currentMonth])
        ->where('invoice_status_id', '<>', '5')
        ->whereYear('paid_date', '=', $year)
        ->orderBy('created_at', 'asc')
        ->get();

        //dd($dataPasadas);

        foreach ($dataNoPagadas as $noPagadas) {
            $data[] = $noPagadas;
        }

        foreach ($dataPasadas as $pasadas) {
            $data[] = $pasadas;
        }

        return $data;
    }

    public function getIngresosThisMonth($year , $currentMonth ){

        $data = Ingreso::whereRaw('MONTH(date) = ?',[$currentMonth])
        ->whereYear('date', '=', $year)
        ->orderBy('date', 'asc')
        ->get();

        return $data;
    }

    public function getGastosThisMonth($year , $currentMonth){

        $data = Gasto::whereMonth('date', $currentMonth )
            ->whereYear('date', '=', $year)
            ->orderBy('date', 'asc')
            ->get();
            //dd($data);
        return $data;
    }

    public function getGastosAsociadosThisMonth($year , $currentMonth ){
        $data = AssociatedExpenses::where('state','PAGADO')
        ->whereYear('date', $year)
        ->whereRaw('MONTH(date) = ?',[$currentMonth])
        ->orderBy('date', 'asc')
        ->get();

        return $data;
    }

    public function getAllInvoices($year){
        $data = Invoice::whereYear('paid_date', '=', $year)->get();

        $facturas = $data->sortBy('paid_date');

        return $facturas;
    }


    public function getInvoicesPartialStatus($facturas, $month, $year){
        $ingresosLista = array();
        $contador=0;

        foreach ($facturas as $factura){
            if ($factura->invoice_status_id == 4){
                $ingresos = Ingreso::where("invoice_id", $factura->id)
                ->whereYear('date', '=', $year)
                ->whereRaw('MONTH(date) = ?',[$month])
                ->orderBy('date', 'asc')
                ->get()->all();
                foreach ($ingresos as $ingreso){
                    $ingresosLista[$contador]["id_factura"] = $factura->id;
                    $ingresosLista[$contador]["id_ingreso"] = $ingreso->id;
                    $ingresosLista[$contador]["date"] = $ingreso->date;
                    $ingresosLista[$contador]["quantity"] = $ingreso->quantity;
                    $contador++;
                }
            }
        }

        return $ingresosLista;
    }

    public function SaveInvoice(Request $request){
        $diaFormat = sprintf("%02d", $request->dia);
        $fecha =  $request->anio . '-' . $request->mes . '-' . $diaFormat;
        $factura = Invoice::where("id", $request->id)->get()->first();
        $factura->paid_date = $fecha;
        $result = $factura->save();

        if ($result){
            //Actualizar alerta
            $fechaEntera = $fecha." 09:00:00";
            $fechaNow = Carbon::parse($fechaEntera);
            //$alert = Alert::where('reference_id', $factura->id)->get()->first();
            //$alert->activation_datetime = $fechaNow->addDay()->format('Y-m-d H:i:s');
           // $alertSaved = $alert->save();

            $currentMonth = date('m');
            $data = DB::table("invoices")
            ->whereRaw('MONTH(paid_date) = ?',[$currentMonth])
            ->orderBy('paid_date', 'asc')->get();

            $array = array();

            $PartialStatusArray = $this->getInvoicesPartialStatus($data, $request->mes, $request->anio);

            $array['Facturas'] = $data;
            $array['PartialStatus'] = $PartialStatusArray;

            $response = json_encode($array);
            return $response;
        }else{
            return 503;
        }
    }

    public function saveDateContabilidad(Request $request){
        $diaFormat = sprintf("%02d", $request->dia);
        $fecha =  $request->anio . '-' . $request->mes . '-' . $diaFormat;

        switch($request->type){
            case "ingreso":
                $ingreso = Ingreso::where("id", $request->id)->get()->first();
                $ingreso->date = $fecha;
                $result = $ingreso->save();
                if ($result){
                    $currentMonth = date('m');
                    $data = DB::table("ingresos")
                    ->whereRaw('MONTH(date) = ?',[$currentMonth])
                    ->orderBy('date', 'asc')->get();
                    $response = json_encode($data);
                }else{
                    $response = 503;
                }
                break;
            case "gasto":
                $gasto = Gasto::where("id", $request->id)->get()->first();
                $gasto->date = $fecha;
                $result = $gasto->save();
                if ($result){
                    $currentMonth = date('m');
                    $data = DB::table("gastos")
                    ->whereRaw('MONTH(date) = ?',[$currentMonth])
                    ->orderBy('date', 'asc')->get();
                    $response = json_encode($data);
                }else{
                    $response = 503;
                }
                break;
            case "gastoAsociado":
                $gastoAsociado = AssociatedExpenses::where("id", $request->id)->where('state','PAGADO')->get()->first();
                $gastoAsociado->date = $fecha;
                $result = $gastoAsociado->save();
                if ($result){
                    $currentMonth = date('m');
                    $data = DB::table("associated_expenses")
                    ->whereRaw('MONTH(date) = ?',[$currentMonth])
                    ->orderBy('date', 'asc')
                    ->get();
                    $response = json_encode($data);
                }else{
                    $response = 503;
                }
                break;
        }
        return $response;
    }

    public function getInvoices (Request $request){


        $dataNoPagadas = Invoice::whereYear('paid_date', '=', $request->year)
        ->whereNull('paid_date')
        ->where('invoice_status_id', '<>', '5')
        ->orderBy('created_at', 'asc')
        ->get();

        //dd($dataNoPagadas);

        $dataPasadas = Invoice::whereYear('paid_date', '=', $request->year)
        ->whereRaw('MONTH(paid_date) = ?', $request->month)
        ->where('invoice_status_id', '<>', '5')
        ->orderBy('created_at', 'asc')
        ->get();

        //dd($dataPasadas);

        $data = [];

        foreach ($dataNoPagadas as $noPagadas) {
            $data[] = $noPagadas;
        }

        foreach ($dataPasadas as $pasadas) {
            $data[] = $pasadas;
        }

        //dd($data);

        $array = array();

        $PartialStatusArray = $this->getInvoicesPartialStatus($data, $request->month, $request->year);

        $array['Facturas'] = $data;
        $array['PartialStatus'] = $PartialStatusArray;

        //dd($array);

        $response = json_encode($array);
        return $response;
    }


    public function getIngresos (Request $request){

        $data = Ingreso::whereYear('date', '=', $request->year)
              ->whereMonth('date', '=', $request->month)
              ->orderBy('date', 'asc')
              ->get();

        $response = json_encode($data);
        return $response;
    }

    public function getGastos (Request $request){

        $data = Gasto::whereYear('date', '=', $request->year)
              ->whereMonth('date', '=', $request->month)
              ->orderBy('date', 'asc')
              ->get();

        $response = json_encode($data);
        return $response;
    }

    public function getGastosAsociados (Request $request){

        $data = AssociatedExpenses::whereYear('date', '=', $request->year)
              ->whereMonth('date', '=', $request->month)
              ->where('state','PAGADO')
              ->orderBy('date', 'asc')
              ->get();

        $response = json_encode($data);
        return $response;
    }

    public function getInvoicesForPrevision ($year, $month){

        $data = Invoice::whereYear('created_at', '=', $year)
              ->whereMonth('created_at', '=', $month)
              ->whereNotNull('paid_date')
              ->orderBy('paid_date', 'asc')
              ->get();

        $array = array();

        $PartialStatusArray = $this->getInvoicesPartialStatus($data, $month, $year);

        $array['Facturas'] = $data;
        $array['PartialStatus'] = $PartialStatusArray;

        $response = json_encode($array);
        return $response;

    }


    public function SaveInvoiceData(Request $request){

        if ($request->estado !=0){
            //Actualizar estado
            $factura = Invoice::where("id", $request->id)->first();

            if ($request->estado ==1 && $factura->invoice_status_id == 3){
                $factura->paid_amount = 0;

                $res=Ingreso::where('invoice_id', $factura->id)->delete();
            }

            $factura->invoice_status_id = $request->estado;


            if($request->estado==3){

                $factura->paid_amount = $factura->total;
                $titulo = $factura->reference ." - ".($factura->cliente->company ?? $factura->cliente->name) ." - ".$factura->concept;
                $date = Carbon::now()->format('Y-m-d');
                $factura->paid_date = $request->anio.'-'.$request->mes.'-'.$request->dia;
                $budget = Budget::where('id', $factura->budget_id)->first();

                // if($budget->commercial_id != null){
                //     $this->addInvoiceCommercial($budget, $factura);
                //     $haveManager = CommercialHaveEmployee::where('commercial_id', $budget->commercial_id)->get()->first();

                //     if ($haveManager){
                //         $this->addInvoiceManager($budget, $factura, $haveManager);
                //     }
                // }

                $ingreso = Ingreso::create([
                    "title" => $titulo,
                    "bank_id" => $request->banco,
                    "invoice_id" => $request->id,
                    "quantity" => $request->importe,
                    "budget_date" => $date,
                    "date" => $date
                ]);

                //Actualizar alerta como resuelta
              //  $alert = Alert::where('reference_id', $request->id)->get()->first();

                // if ($alert){
                // 	$alert->status_id = 2;
                // 	$alert->save();
                // }

            }else if($request->estado == 4){
                $totalPagado = $factura->paid_amount;
                $totalPagado += $request->importe;
                $factura->paid_amount = $totalPagado;
                $titulo = $factura->reference ." - ".($factura->cliente->company ?? $factura->cliente->name)." - ".$factura->concept;

                $date = Carbon::now()->format('Y-m-d');
                $factura->paid_date = $date;

                $ingreso = Ingreso::create(array(
                    "title" => $titulo,
                    "bank_id" => $request->banco,
                    "invoice_id" => $request->id,
                    "quantity" => $request->importe,
                    "budget_date" => $date,
                    "date" => $date
                ));
            }

            $facturaSaved = $factura->save();

            if($facturaSaved){
                $response = json_encode(array(
                    "estado" => "OK"
                ));
            }else{
                $response = json_encode(array(
                    "estado" => "ERROR"
                ));
            }

            return $response;

        }else{
            $response = json_encode(array(
                "estado" => "ERROR"
            ));

            return $response;
        }

    }


    // public function addInvoiceCommercial($budget, $factura){
    //     $cobrado = 0;
    //     $budgetsConcepts = BudgetConcepts::where('budget_id', $budget->id)->get();
    //     foreach($budgetsConcepts as $budgetsConcept){
    //         $comission = CommercialCommission::where('commercial_level_id', $budget->commercial->access_level_id)->where('commercial_product_id', $budgetsConcept->services_category_id)->get()->first();
    //         if ($comission){
    //             $cobrado = number_format($cobrado + (($comission->quantity / 100) * $budgetsConcept->total), 2);
    //         }
    //     }

    //     $iva = $cobrado*0.21;
    //     $total = $cobrado+$iva;
    //     $invoiceCommercial = InvoiceCommercial::where('commercial_id', $budget->commercial_id)->orderBy('created_at', 'desc')->get()->first();

    //     if($invoiceCommercial){
    //         preg_match('#\((.*?)\)#', 'DÍGITOS_AUTO(6)', $match);
    //         $numberOfAutoDigits = $match[1];

    //         // La cadena tendrá tantos ceros como dígitos
    //         $numbrOfAutoDigitsZeros = '';
    //         for($i=0; $i<$numberOfAutoDigits;$i++){
    //             $numbrOfAutoDigitsZeros .=  0;
    //         }

    //         $numbReference = $numbrOfAutoDigitsZeros.(explode('-', $invoiceCommercial->reference)[1]+1);
    //         $reference = substr($budget->commercial->name, 0, 1).substr($budget->commercial->surname, 0, 1).$budget->commercial_id.'-'.$numbReference;
    //         $data = [
    //             'budget_id' => $budget->id,
    //             'invoice_id' => $factura->id,
    //             'reference' => $reference,
    //             'commercial_id' => $budget->commercial_id,
    //             'project_id' => $factura->project_id,
    //             'concept' => $factura->concept,
    //             'base' => $cobrado,
    //             'iva' => $iva,
    //             'iva_percentage' => $factura->iva_percentage,
    //             'total' => $total,
    //         ];
    //         $newInvoiceCommercial = InvoiceCommercial::create($data);
    //         $newInvoiceCommercial->save();

    //     }else{
    //         $reference = substr($budget->commercial->name, 0, 1).substr($budget->commercial->surname, 0, 1).$budget->commercial_id.'-'.'0000001';
    //         $data = [
    //             'budget_id' => $budget->id,
    //             'invoice_id' => $factura->id,
    //             'reference' => $reference,
    //             'commercial_id' => $budget->commercial_id,
    //             'project_id' => $factura->project_id,
    //             'concept' => $factura->concept,
    //             'base' => $cobrado,
    //             'iva' => $iva,
    //             'iva_percentage' => $factura->iva_percentage,
    //             'total' => $total,
    //         ];
    //         $newInvoiceCommercial = InvoiceCommercial::create($data);
    //         $newInvoiceCommercial->save();
    //     }

    // }

    // public function addInvoiceManager($budget, $factura, $haveManager){
    //     $cobrado = 0;
    //     $budgetsConcepts = BudgetConcepts::where('budget_id', $budget->id)->get();
    //     foreach($budgetsConcepts as $budgetsConcept){
    //         $comission = CommercialCommission::where('commercial_level_id', 9)->where('commercial_product_id',$budgetsConcept->services_category_id)->get()->first();
    //         $cobrado = number_format($cobrado + (($comission->quantity / 100) * $budgetsConcept->total), 2);
    //     }
    //     $iva = $cobrado*0.21;
    //     $total = $cobrado+$iva;
    //     $invoiceCommercial = InvoiceCommercial::where('commercial_id', $haveManager->admin_user_id)->orderBy('created_at', 'desc')->get()->first();
    //     if($invoiceCommercial){
    //         preg_match('#\((.*?)\)#', 'DÍGITOS_AUTO(6)', $match);
    //         $numberOfAutoDigits = $match[1];
    //         // La cadena tendrá tantos ceros como dígitos
    //         $numbrOfAutoDigitsZeros = '';
    //         for($i=0; $i<$numberOfAutoDigits;$i++){
    //             $numbrOfAutoDigitsZeros .=  0;
    //         }
    //         $numbReference = $numbrOfAutoDigitsZeros.(explode('-',$invoiceCommercial->reference)[1]+1);
    //         $reference = substr($haveManager->adminUser->name, 0, 1).substr($haveManager->adminUser->surname, 0, 1).$haveManager->admin_user_id.'-'.$numbReference;
    //         $data = [
    //             'budget_id' => $budget->id,
    //             'invoice_id' => $factura->id,
    //             'reference' => $reference,
    //             'commercial_id' => $haveManager->admin_user_id,
    //             'project_id' => $factura->project_id,
    //             'concept' => $factura->concept,
    //             'base' => $cobrado,
    //             'iva' => $iva,
    //             'iva_percentage' => $factura->iva_percentage,
    //             'total' => $total,
    //         ];
    //         $newInvoiceCommercial = InvoiceCommercial::create($data);
    //         $newInvoiceCommercial->save();
    //     }else{
    //         $reference = substr($haveManager->adminUser->name, 0, 1).substr($haveManager->adminUser->surname, 0, 1).$haveManager->admin_user_id.'-'.'0000001';
    //         $data = [
    //             'budget_id' => $budget->id,
    //             'invoice_id' => $factura->id,
    //             'reference' => $reference,
    //             'commercial_id' => $haveManager->admin_user_id,
    //             'project_id' => $factura->project_id,
    //             'concept' => $factura->concept,
    //             'base' => $cobrado,
    //             'iva' => $iva,
    //             'iva_percentage' => $factura->iva_percentage,
    //             'total' => $total,
    //         ];
    //         $newInvoiceCommercial = InvoiceCommercial::create($data);
    //         $newInvoiceCommercial->save();
    //     }
    // }

    public function ChangeInvoiceStatus(Request $request){

        $factura = Invoice::where("id", $request->id)->first();
        $budget = Budget::where('id', $factura->budget_id)->first();
        $factura->invoice_status_id = $request->estado;
        $result = $factura->save();
        if ($result){
            $response = json_encode(array(
                "estado" => "OK"
            ));
        }else{
            $response = json_encode(array(
                "estado" => "ERROR"
            ));
        }
        return $response;
     }
}
