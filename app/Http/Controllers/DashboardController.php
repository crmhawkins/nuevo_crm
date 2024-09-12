<?php

namespace App\Http\Controllers;

use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\Jornada\Jornada;
use App\Models\Jornada\Pause;
use App\Models\Llamadas\Llamada;
use App\Models\Projects\Project;
use App\Models\Tasks\LogTasks;
use App\Models\Tasks\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        $id = Auth::user()->id;
        $acceso = Auth::user()->access_level_id;
        $user = User::find($id);
        $users = User::where('inactive',0)->get();
        $to_dos = $user->todos->where('finalizada',false);
        $timeWorkedToday = $this->calculateTimeWorkedToday($user);
        $jornadaActiva = $user->activeJornada();
        $events = $user->eventos->map(function ($event) {
            return $event->nonNullAttributes(); // Usa el método que definimos antes
        });
        $pausaActiva = null;
        if ($jornadaActiva) {
            $pausaActiva = $jornadaActiva->pausasActiva();
        }
        switch($acceso){
            case(1):
                $clientes = Client::where('is_client',true)->get();
                $budgets = Budget::where('admin_user_id',$id)->get();
                $projects = Project::where('admin_user_id',$id)->get();
                $tareas = Task::where('gestor_id',$id)->get();
                return view('dashboards.dashboard_gestor', compact('user','tareas','to_dos','budgets','projects','clientes','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva'));
            case(2):
                $clientes = Client::where('is_client',true)->get();
                $budgets = Budget::where('admin_user_id',$id)->get();
                $projects = Project::where('admin_user_id',$id)->get();
                $tareas = Task::where('gestor_id',$id)->get();
                return view('dashboards.dashboard_gestor', compact('user','tareas','to_dos','budgets','projects','clientes','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva'));
            case(3):
                $clientes = Client::where('is_client',true)->get();
                $budgets = Budget::where('admin_user_id',$id)->get();
                $projects = Project::where('admin_user_id',$id)->get();
                $tareas = Task::where('gestor_id',$id)->get();
                return view('dashboards.dashboard_gestor', compact('user','tareas','to_dos','budgets','projects','clientes','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva'));
            case(4):
                $clientes = Client::where('is_client',true)->get();
                $budgets = Budget::where('admin_user_id',$id)->get();
                $projects = Project::where('admin_user_id',$id)->get();
                $tareas = Task::where('gestor_id',$id)->get();
                $v1 = count(Budget::where('admin_user_id',2)->whereYear('created_at',2202)->get())/12;
                return view('dashboards.dashboard_gestor', compact('user','tareas','to_dos','budgets','projects','clientes','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva'));
            case(5):
                $tareas = $user->tareas->whereIn('task_status_id', [1, 2, 5]);
                $tiempoProducidoHoy = $this->tiempoProducidoHoy();
                $tasks = $this->getTasks($user->id);
                return view('dashboards.dashboard_personal', compact('user','tiempoProducidoHoy','tasks','tareas','to_dos','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva'));
        }
    }

    public function tiempoProducidoHoy()
    {

        $hoy = Carbon::today();
        $tiempoTarea = 0;

        if (Auth::check()) {
            $userId = Auth::id();
            $tareasHoy = LogTasks::where('admin_user_id', $userId)
                ->whereDate('date_start', '=', $hoy)
                ->get();

            foreach ($tareasHoy as $tarea) {
                if ($tarea->status == 'Pausada') {
                    $tiempoInicio = Carbon::parse($tarea->date_start);
                    $tiempoFinal = Carbon::parse($tarea->date_end);
                    $tiempoTarea += $tiempoFinal->diffInSeconds($tiempoInicio);
                }
            }
        } else {
            $result = '00:00:00';
        }

        // Formatear el tiempo total en horas, minutos y segundos
        $hours = floor($tiempoTarea / 3600);
        $minutes = floor(($tiempoTarea % 3600) / 60);
        $seconds = $tiempoTarea % 60;

        $result = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        // Calcular el porcentaje de tiempo trabajado en relación con el total
        $horas_dia_porcentaje = $hours + ($minutes / 60);
        $totalHoras = 7;
        $porcentaje = ($horas_dia_porcentaje / $totalHoras) * 100;

        $data = [
            'horas' => $result,
            'porcentaje' => $porcentaje
        ];

        return $data;
    }

    public function timeworked(){
        $user = Auth::user();
        $timeWorkedToday = $this->calculateTimeWorkedToday($user);
        return response()->json(['success' => true ,'time' => $timeWorkedToday]);
    }

    public function llamada(Request $request){



        $user = Auth::user();
        $request->validate([
            '' => 'required|exists:admin_user,id',
            'fecha' => 'required|date',
            'archivo' => 'required|file|mimes:pdf|max:2048', // Asegura que sea un PDF y no supere los 2MB
        ]);
        $llamada =  Llamada::create([
            'admin_user_id' => $user->id,
            'start_time' => Carbon::now
(),
            'is_active' => true,
        ]);
        if($llamada){
            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
        }
    }

    public function finalizar()
    {


        $user = Auth::user();
        $llamada = Llamada::where('admin_user_id', $user->id)->where('is_active', true)->first();
        if ($llamada) {
            $finllamada = $llamada->update([
                'end_time' => Carbon::now
(),
                'is_active' => false,
            ]);

            if($finllamada){
                return response()->json(['success' => true]);
            }else{
                return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
            }
        }else{
            return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
        }

    }


    public function startJornada()
    {


        $user = User::find(Auth::user()->id);

        $activeJornada = $user->activeJornada();

        if ($activeJornada) {
            // Si ya hay una jornada activa, retornar un mensaje indicando que no se puede iniciar otra
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una jornada activa.'
            ]);
        }

        $jornada =  Jornada::create([
            'admin_user_id' => $user->id,
            'start_time' => Carbon::now
(),
            'is_active' => true,
        ]);
        if($jornada){
            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
        }
    }

    public function endJornada()
    {


        $user = Auth::user();
        $jornada = Jornada::where('admin_user_id', $user->id)->where('is_active', true)->first();
        if ($jornada) {
            $finJornada = $jornada->update([
                'end_time' => Carbon::now
(),
                'is_active' => false,
            ]);

            if($finJornada){
                return response()->json(['success' => true]);
            }else{
                return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
            }
        }else{
            return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
        }

    }

    public function startPause()
    {


        $user = Auth::user();
        $jornada = Jornada::where('admin_user_id', $user->id)->where('is_active', true)->first();
        if ($jornada) {
            $pause =  Pause::create([
                'jornada_id' =>$jornada->id,
                'start_time' => Carbon::now
(),
            ]);

            if($pause){
                return response()->json(['success' => true]);
            }else{
                return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
            }
        }else{
            return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
        }
    }

    public function endPause()
    {


        $user = Auth::user();
        $jornada = Jornada::where('admin_user_id', $user->id)->where('is_active', true)->first();
        if ($jornada) {
            $pause = Pause::where('jornada_id', $jornada->id)->whereNull('end_time')->first();
            if ($pause) {
                $finPause = $pause->update([
                    'end_time' => Carbon::now
(),
                    'is_active' => false,
                ]);

                if($finPause){
                    return response()->json(['success' => true]);
                }else{
                    return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
                }
            }else{
                return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
            }
        }else{
            return response()->json(['success' => false,'mensaje' => 'Error al iniciar jornada']);
        }
    }

    private function calculateTimeWorkedToday($user)
    {


        $todayJornadas = $user->jornadas()->whereDate('start_time', Carbon::today())->get();

        $totalWorkedSeconds = 0;

        foreach ($todayJornadas as $jornada) {
            $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? Carbon::now());
            $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
                return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? Carbon::now());
            });
            $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
        }

        return $totalWorkedSeconds;
    }

    public function getTasks($id)
    {
        $tasks = array();
        $tasksPause = Task::where("admin_user_id", $id)->where("task_status_id", 2)->get();
        $tasks["tasksPause"] = $tasksPause;
        $tasksRevision = Task::where("admin_user_id", $id)->where("task_status_id", 5)->get();
        $tasks["tasksRevision"] = $tasksRevision;
        $taskPlay = Task::where("admin_user_id", $id)->where("task_status_id", 1)->get()->first();
        $tasks["taskPlay"] = $taskPlay;

        return $tasks;
    }

    public function getDataTask(Request $request)
    {

        $tarea = Task::find($request->id);
        //$metas = DB::table('meta')->where("tasks_id", $request->id)->get();
        $autor = $tarea->usuario;
        if ($tarea) {
            $data = array();
            $data["id"] = $tarea->id;
            $data["user"] = $tarea->admin_user_id;
            $data["titulo"] = $tarea->title;
            $data["cliente"] = $tarea->presupuesto->cliente->name;
            $data["descripcion"] = $tarea->description;
            $data["estimado"] = $tarea->estimated_time;
            $data["real"] = $tarea->real_time;
            $data["proyecto"] = $tarea->proyecto->name;
            $data["prioridad"] = $tarea->prioridad->name;
            $data["gestor"] = $tarea->gestor->name;
            $data["gestorid"] = $tarea->gestor->id;
            $data["estado"] = $tarea->estado->name;
            $data["metas"] = '';
            $data["userName"] = $autor;



            $response = json_encode($data);

            return $response;
        } else {
            $response = json_encode(array(
                "estado" => "ERROR"
            ));

            return $response;
        }
    }

    public function getTasksRefresh()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $usuario = User::find($userId);
            $tareas = $this->getTasks($usuario->id);

            return $tareas;
        }
    }

    public function setStatusTask(Request $request)
    {



        $tarea = Task::find($request->id);
        $date = Carbon::now();
        $userId = Auth::id();
        $usuario = User::find($userId);

        $formatEstimated = strtotime($tarea->estimated_time);
        $formatReal = strtotime($tarea->real_time);


        $clientIP = request()->ip();

        $error = false;


        //if($clientIP == "81.45.82.225" || $usuario->access_level_id == 4 || $usuario->access_level_id == 3){

        if ($tarea) {
            switch ($request->estado) {
                case "Reanudar":
                    $tareaActiva = Task::where("admin_user_id", $usuario->id)->where("task_status_id", 1)->get()->first();

                    if (!$tareaActiva) {
                        $tarea->task_status_id = 1;
                    }

                    $logTaskC = DB::select("SELECT id FROM `log_tasks` WHERE `status` = 'Reanudada' AND `admin_user_id` = $usuario->id");
                    if (count($logTaskC) == 1) {
                        $error = true;
                    } else {


                        $createLog = LogTasks::create([
                            'admin_user_id' => $usuario->id,
                            'task_id' => $tarea->id,
                            'date_start' => $date,
                            'date_end' => null,
                            'status' => 'Reanudada'
                        ]);

                        $logTask = DB::select("SELECT id FROM `log_tasks` WHERE date_start BETWEEN DATE_SUB(now(), interval 6 hour) AND DATE_ADD(NOW(), INTERVAL 7 hour) AND `admin_user_id` = $usuario->id");
                        if (count($logTask) == 1) {
                            $horly = HourlyAverage::create(
                                [
                                    'admin_user_id' => $usuario->id,
                                    'log_task_id' => $createLog->id,
                                    'hours' => $date->format('H:i:s'),
                                ]
                            );


                            $note = $this->calculateNote($horly->hours);

                            $fechaNow = Carbon::now();

                            if ($note == 0) {

                                $hourlyAverage = DB::select("SELECT hours FROM `hourly_average` WHERE created_at BETWEEN LAST_DAY(now() - interval 1 month) AND LAST_DAY(NOW()) AND `admin_user_id` = $usuario->id AND `hours` > '09:05:00'");
                                if (count($hourlyAverage) > 2) {
                                    $data = [
                                        "admin_user_id" =>  1,
                                        "stage_id" => 15,
                                        "description" => $usuario->name . " ha llegado tarde 3 veces o mas este mes",
                                        "status_id" => AlertStatus::ALERT_STATUS_PENDING,
                                        "reference_id" => $horly->id,
                                        "activation_datetime" => $fechaNow->format('Y-m-d H:i:s')
                                    ];

                                    $alert = Alert::create($data);
                                    $alertSaved = $alert->save();
                                }
                            }

                            $text = $this->mensajeMediaHora($note);


                            $fechaNow = Carbon::now();



                            $data = [
                                "admin_user_id" =>  $usuario->id,
                                "stage_id" => 23,
                                "description" => $text,
                                "status_id" => AlertStatus::ALERT_STATUS_PENDING,
                                "reference_id" => $horly->id,
                                "activation_datetime" => $fechaNow->format('Y-m-d H:i:s')
                            ];

                            $alert = Alert::create($data);
                            $alertSaved = $alert->save();
                        }
                    }
                    break;
                case "Pausada":
                    if ($tarea->task_status_id == 1) {
                        if ($tarea->real_time == "00:00:00") {
                            $start = $tarea->updated_at;
                            $end   = new \DateTime("NOW");
                            $interval = $end->diff($start);

                            $time = sprintf(
                                '%02d:%02d:%02d',
                                ($interval->d * 24) + $interval->h,
                                $interval->i,
                                $interval->s
                            );
                        } else {
                            $start = $tarea->updated_at;
                            $end   = new \DateTime("NOW");
                            $interval = $end->diff($start);

                            $time = sprintf(
                                '%02d:%02d:%02d',
                                ($interval->d * 24) + $interval->h,
                                $interval->i,
                                $interval->s
                            );

                            $time = $this->sum_the_time($tarea->real_time, $time);
                        }
                        $tarea->real_time = $time;
                    }

                    $last = LogTasks::where("admin_user_id", $usuario->id)->get()->last();
                    if ($last) {
                        $last->date_end = $date;
                        $last->status = "Pausada";
                        $last->save();
                    }

                    $tarea->task_status_id = 2;
                    break;
                case "Revision":

                    //Crear Alerta tarea terminada antes de tiempo
                    // if ($formatEstimated > $formatReal) {
                    //     $dataAlert = [
                    //         'admin_user_id' => $usuario->id,
                    //         'stage_id' => 14,
                    //         'activation_datetime' => $date->format('Y-m-d H:i:s'),
                    //         'status_id' => 1,
                    //         'reference_id' => $tarea->id,
                    //     ];

                    //     $alert = Alert::create($dataAlert);
                    //     $alertSaved = $alert->save();
                    // }

                    $tarea->task_status_id = 5;
                    break;
            }

            $taskSaved = $tarea->save();

            if (($taskSaved || $tareaActiva == null) && !$error) {
                $response = json_encode(array(
                    "estado" => "OK"
                ));
            } else {
                $response = json_encode(array(
                    "estado" => "ERROR; TIENES OTRA TAREA ACTIVA. HABLA CON EL CREADOR .`,"
                ));
            }
        } else {
            $response = json_encode(array(
                "estado" => "ERROR"
            ));
        }
        //}

        return $response;
    }

    function sum_the_time($time1, $time2)
    {
        $times = array($time1, $time2);
        $seconds = 0;
        foreach ($times as $time) {
            list($hour, $minute, $second) = explode(':', $time);
            $seconds += $hour * 3600;
            $seconds += $minute * 60;
            $seconds += $second;
        }
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes  = floor($seconds / 60);
        $seconds -= $minutes * 60;
        // return "{$hours}:{$minutes}:{$seconds}";
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
    function convertToNumber($importe) {

        // Elimina los puntos de separación de miles

        $importe = str_replace('.', '', $importe);

        // Reemplaza la coma decimal por un punto decimal

        $importe = str_replace(',', '.', $importe);

        // Convierte a número flotante

        return (float)$importe;

    }

    public function getCommercialInformation($dashboard)

    {

        $temporals = $this->horasTareas();

        // dd($temporals);

        $budgets = Budget::where('commercial_id', Auth::id())->get();

        // $budgets = Budget::where('commercial_id', 10)->get();

        $usuario = AdminUser::find(Auth::id());

        // $usuario = AdminUser::where('id',10)->first();

        // dd($usuario);

        $ayudas = Ayuda::where('comercial_id', $usuario->id)->get();



        $pedienteCierre = 0;

        $comisionCurso = 0;

        $comisionPendiente = 0;

        $comisionTramitadas = 0;

        $comisionRestante = 0;



        foreach ($ayudas as $key => $ayuda) {



            if ($ayuda->estado == 18 || $ayuda->estado == 17 || $ayuda->estado == 24) {

                $pedienteCierre += ($this->convertToNumber($ayuda->importe)* 0.05);

            } else if($ayuda->estado == 10){

                $comisionCurso += ($this->convertToNumber($ayuda->importe)* 0.05);

            } else if($ayuda->estado == 4 || $ayuda->estado == 7 || $ayuda->estado == 5 || $ayuda->estado == 8 || $ayuda->estado == 9){

                $comisionPendiente += ($this->convertToNumber($ayuda->importe)* 0.05);

            } else if($ayuda->estado == 2){

                $comisionTramitadas += ($this->convertToNumber($ayuda->importe)* 0.05);

            } else if($ayuda->estado == 25){

                $comisionRestante += ($this->convertToNumber($ayuda->importe)* 0.05);

            }

        }



        $alertas = Alert::where('status_id', 1)->where('admin_user_id', $usuario->id)->get();



        $alertasActivadas = $this->getAlerts($alertas);



        //dd($alertasActivadas)



        $events = $this->getEventsCalendar($usuario->id);



        $calendar = \Calendar::addEvents($events)->setOptions([

            'displayEventTime' => true,

        ]);



        $calendar->setId('calendarioComercial');



        $presupuestoTable = [];

        $presupuestos = Budget::where('commercial_id', Auth::id())->get();



        foreach ($presupuestos as $key => $presupuesto) {

            $budgetsConcepts2 = BudgetConcepts::where('budget_id', $presupuesto->id)->get();

            foreach ($budgetsConcepts2 as $key => $concepto) {

                if ($concepto->services_category_id != 84) {

                    array_push($presupuestoTable, $presupuesto);

                }

            }

        }



        $pendiente = 0;

        $cobrado = 0;

        $curso = 0;

        $hasganado = 0;

        $autofacturas = [];

        $kitDigitalArray = [];



        foreach ($budgets as $budget) {

            $budgetsConceptsKit = BudgetConcepts::where('budget_id', $budget->id)->get();

            foreach ($budgetsConceptsKit as $key => $concepto) {

                if ($concepto->services_category_id == 84) {

                    array_push($kitDigitalArray, $budget);

                }

            }





            if ($budget->budget_status_id == 6) {



                $budgetsConcepts = BudgetConcepts::where('budget_id', $budget->id)->get();

                foreach ($budgetsConcepts as $budgetsConcept) {

                    if ($budgetsConcept->concept_type_id == 2) {

                        $comission = CommercialCommission::where('commercial_level_id', $usuario->access_level_id)->where('commercial_product_id', $budgetsConcept->services_category_id)->get()->first();

                        if ($comission) {

                            $invoice = Invoice::where('budget_id', $budget->id)->get()->first();

                            if ($invoice) {

                                if ($invoice->invoice_status_id == 2 || $invoice->invoice_status_id == 1) {

                                    $curso =  number_format($curso + (($comission->quantity / 100) * $budgetsConcept->total), 2);

                                } elseif ($invoice->invoice_status_id == 3 || $invoice->invoice_status_id == 4) {

                                    $invoiceCommercial = InvoiceCommercial::where('invoice_id', $invoice->id)->get()->first();

                                    if ($invoiceCommercial) {

                                        if ($invoiceCommercial->state == "NO COBRADO") {

                                            $cobrado = number_format($cobrado + $invoiceCommercial->base, 2);

                                        } else {

                                            $hasganado = number_format($hasganado + $invoiceCommercial->base, 2);

                                        }

                                    } else {

                                        if ($invoiceCommercial) {

                                            $cobrado = number_format($cobrado + $invoiceCommercial->base, 2);

                                        }

                                    }

                                }

                            }

                        }

                    } else {

                        $comission = 3;

                        $invoice = Invoice::where('budget_id', $budget->id)->get()->first();

                        if ($invoice) {

                            if ($invoice->invoice_status_id == 2 || $invoice->invoice_status_id == 1) {

                                $curso =  number_format($curso + (($comission->quantity / 100) * $budgetsConcept->total), 2);

                            } elseif ($invoice->invoice_status_id == 3 || $invoice->invoice_status_id == 4) {

                                $invoiceCommercial = InvoiceCommercial::where('invoice_id', $invoice->id)->get()->first();

                                if ($invoiceCommercial) {

                                    if ($invoiceCommercial->state == "NO COBRADO") {

                                        $cobrado = number_format($cobrado + $invoiceCommercial->base, 2);

                                    } else {

                                        $hasganado = number_format($hasganado + $invoiceCommercial->base, 2);

                                    }

                                } else {

                                    if ($invoiceCommercial) {

                                        $cobrado = number_format($cobrado + $invoiceCommercial->base, 2);

                                    }

                                }

                            }

                        }

                    }

                }

            } elseif ($budget->budget_status_id == 2) {

                $budgetsConcepts = BudgetConcepts::where('budget_id', $budget->id)->get();

                foreach ($budgetsConcepts as $budgetsConcept) {

                    if ($budgetsConcept->concept_type_id == 2) {



                        $comission = CommercialCommission::where('commercial_level_id', $budget->level_commission)->where('commercial_product_id', $budgetsConcept->services_category_id)->get();

                        if (count($comission) > 0) {

                            $pendiente = number_format((float)$pendiente + ((float)$comission[0]->quantity / 100) * (float)$budgetsConcept->total, 2);

                        }

                    }

                }

            } elseif ($budget->budget_status_id == 3 || $budget->budget_status_id == 5) {

                $budgetsConcepts = BudgetConcepts::where('budget_id', $budget->id)->get();

                foreach ($budgetsConcepts as $budgetsConcept) {

                    if ($budgetsConcept->concept_type_id == 2) {



                        $comission = CommercialCommission::where('commercial_level_id', $budget->level_commission)->where('commercial_product_id', $budgetsConcept->services_category_id)->get();

                        if (count($comission) > 0) {

                            $curso = number_format($curso + ($comission[0]->quantity / 100) * $budgetsConcept->total, 2);

                        }

                    }

                }

            }

        }



        $comerciales = AdminUser::Orwhere("access_level_id", 6)->Orwhere("access_level_id", 7)->Orwhere("access_level_id", 8)->Orwhere("access_level_id", 9)->Orwhere("access_level_id", 10)->Orwhere("access_level_id", 11)->Orwhere("access_level_id", 12)->get();

        $totalAll = 0;

        $numComerciales = count($comerciales);

        $webs = 0;

        $renders = 0;

        $design = 0;

        $redes = 0;

        $imprenta = 0;

        $audioVisual = 0;

        $renting = 0;

        $eventos = 0;

        $kitDigital = 0;



        $arrayMeses = array_fill(0, 12, 0);

        $arrayMesesCount = array_fill(0, 12, 0);

        if ($numComerciales > 0) {

            foreach ($comerciales as $comercial) {

                $presupuestos = Budget::where("commercial_id", $comercial->id)->where("budget_status_id", ">", 2)->orderBy("creation_date", "asc")->get();

                foreach ($presupuestos as $presupuesto) {

                    $mes = Carbon::parse($presupuesto->creation_date)->format('m');

                    $year = Carbon::parse($presupuesto->creation_date)->format('Y');

                    $yearNow = Carbon::now()->format('Y');

                    if ($year == $yearNow) {

                        switch ($mes) {

                            case '01':

                                $arrayMeses[0] = $arrayMeses[0]  + $presupuesto->total;

                                $arrayMesesCount[0] = $arrayMesesCount[0]  + 1;

                                break;

                            case '02':

                                $arrayMeses[1] = $arrayMeses[1]  + $presupuesto->total;

                                $arrayMesesCount[1] = $arrayMesesCount[1]  + 1;

                                break;

                            case '03':

                                $arrayMeses[2] = $arrayMeses[2]  + $presupuesto->total;

                                $arrayMesesCount[2] = $arrayMesesCount[2]  + 1;

                                break;

                            case '04':

                                $arrayMeses[3] = $arrayMeses[3]  + $presupuesto->total;

                                $arrayMesesCount[3] = $arrayMesesCount[3]  + 1;

                                break;

                            case '05':

                                $arrayMeses[4] = $arrayMeses[4]  + $presupuesto->total;

                                $arrayMesesCount[4] = $arrayMesesCount[4]  + 1;

                                break;

                            case '06':

                                $arrayMeses[5] = $arrayMeses[5]  + $presupuesto->total;

                                $arrayMesesCount[5] = $arrayMesesCount[5]  + 1;

                                break;

                            case '07':

                                $arrayMeses[6] = $arrayMeses[6]  + $presupuesto->total;

                                $arrayMesesCount[6] = $arrayMesesCount[6]  + 1;

                                break;

                            case '08':

                                $arrayMeses[7] = $arrayMeses[7]  + $presupuesto->total;

                                $arrayMesesCount[7] = $arrayMesesCount[7]  + 1;

                                break;

                            case '09':

                                $arrayMeses[8] = $arrayMeses[8]  + $presupuesto->total;

                                $arrayMesesCount[8] = $arrayMesesCount[8]  + 1;

                                break;

                            case '10':

                                $arrayMeses[9] = $arrayMeses[9]  + $presupuesto->total;

                                $arrayMesesCount[9] = $arrayMesesCount[9]  + 1;

                                break;

                            case '11':

                                $arrayMeses[10] = $arrayMeses[10]  + $presupuesto->total;

                                $arrayMesesCount[10] = $arrayMesesCount[10]  + 1;

                                break;

                            case '12':

                                $arrayMeses[11] = $arrayMeses[11]  + $presupuesto->total;

                                $arrayMesesCount[11] = $arrayMesesCount[11]  + 1;

                                break;

                        }

                    }

                }

            }

            for ($i = 0; $i < count($arrayMeses); $i++) {

                if ($arrayMeses[$i]  != 0) {

                    $arrayMeses[$i] = $arrayMeses[$i] / $arrayMesesCount[$i];

                }

            }

        }



        $arrayMesesComercial = array_fill(0, 12, 0);

        $arrayMesesComercialCount = array_fill(0, 12, 0);



        $presupuestos = Budget::where("commercial_id", $usuario->id)->where("budget_status_id", ">", 2)->orderBy("creation_date", "asc")->get();

        foreach ($presupuestos as $presupuesto) {

            $mes = Carbon::parse($presupuesto->creation_date)->format('m');

            $year = Carbon::parse($presupuesto->creation_date)->format('Y');

            $yearNow = Carbon::now()->format('Y');

            if ($year == $yearNow) {

                switch ($mes) {

                    case '01':

                        $arrayMesesComercial[0] = $arrayMesesComercial[0]  + $presupuesto->total;

                        $arrayMesesComercialCount[0] = $arrayMesesComercialCount[0]  + 1;

                        break;

                    case '02':

                        $arrayMesesComercial[1] = $arrayMesesComercial[1]  + $presupuesto->total;

                        $arrayMesesComercialCount[1] = $arrayMesesComercialCount[1]  + 1;

                        break;

                    case '03':

                        $arrayMesesComercial[2] = $arrayMesesComercial[2]  + $presupuesto->total;

                        $arrayMesesComercialCount[2] = $arrayMesesComercialCount[2]  + 1;

                        break;

                    case '04':

                        $arrayMesesComercial[3] = $arrayMesesComercial[3]  + $presupuesto->total;

                        $arrayMesesComercialCount[3] = $arrayMesesComercialCount[3]  + 1;

                        break;

                    case '05':

                        $arrayMesesComercial[4] = $arrayMesesComercial[4]  + $presupuesto->total;

                        $arrayMesesComercialCount[4] = $arrayMesesComercialCount[4]  + 1;

                        break;

                    case '06':

                        $arrayMesesComercial[5] = $arrayMesesComercial[5]  + $presupuesto->total;

                        $arrayMesesComercialCount[5] = $arrayMesesComercialCount[5]  + 1;

                        break;

                    case '07':

                        $arrayMesesComercial[6] = $arrayMesesComercial[6]  + $presupuesto->total;

                        $arrayMesesComercialCount[6] = $arrayMesesComercialCount[6]  + 1;

                        break;

                    case '08':

                        $arrayMesesComercial[7] = $arrayMesesComercial[7]  + $presupuesto->total;

                        $arrayMesesComercialCount[7] = $arrayMesesComercialCount[7]  + 1;

                        break;

                    case '09':

                        $arrayMesesComercial[8] = $arrayMesesComercial[8]  + $presupuesto->total;

                        $arrayMesesComercialCount[8] = $arrayMesesComercialCount[8]  + 1;

                        break;

                    case '10':

                        $arrayMesesComercial[9] = $arrayMesesComercial[9]  + $presupuesto->total;

                        $arrayMesesComercialCount[9] = $arrayMesesComercialCount[9]  + 1;

                        break;

                    case '11':

                        $arrayMesesComercial[10] = $arrayMesesComercial[10]  + $presupuesto->total;

                        $arrayMesesComercialCount[10] = $arrayMesesComercialCount[10]  + 1;

                        break;

                    case '12':

                        $arrayMesesComercial[11] = $arrayMesesComercial[11]  + $presupuesto->total;

                        $arrayMesesComercialCount[11] = $arrayMesesComercialCount[11]  + 1;

                        break;

                }

            }

        }



        for ($i = 0; $i < count($arrayMesesComercial); $i++) {

            if ($arrayMesesComercial[$i]  != 0) {

                $arrayMesesComercial[$i] = $arrayMesesComercial[$i] / $arrayMesesComercialCount[$i];

            }

        }



        $presupuestos = Budget::where("commercial_id", $usuario->id)->where("budget_status_id", ">", 2)->get();



        foreach ($presupuestos as $presupuesto) {

            $budgetConcepts = BudgetConcepts::where("budget_id", $presupuesto->id)->get();

            foreach ($budgetConcepts as $budgetConcept) {

                if ($budgetConcept->services_category_id == 57) {

                    $webs = $webs + 1;

                } elseif ($budgetConcept->services_category_id == 45) {

                    $renders = $renders + 1;

                } elseif ($budgetConcept->services_category_id == 59) {

                    $design = $design + 1;

                } elseif ($budgetConcept->services_category_id == 56) {

                    $redes = $redes + 1;

                } elseif ($budgetConcept->services_category_id == 60) {

                    $imprenta = $imprenta + 1;

                } elseif ($budgetConcept->services_category_id == 43) {

                    $audioVisual = $audioVisual + 1;

                } elseif ($budgetConcept->services_category_id == 72) {

                    $renting = $renting + 1;

                } elseif ($budgetConcept->services_category_id == 74) {

                    $eventos = $eventos + 1;

                } elseif ($budgetConcept->services_category_id == 84) {

                    $kitDigital = $kitDigital + 1;

                }

            }

        }



        $presupuestosComercial = Budget::where("commercial_id", $comercial->id)->where("budget_status_id", ">", 2)->get();



        $clients = Client::where('admin_user_id', Auth::user()->id)->get();

        $notes = Notes::where('admin_user_id', Auth::user()->id)->orderBy('created_at', 'asc')->get();

        $fechaEmision = Carbon::now();

        $fechaExpiracion = new Carbon('last day of this month');



        $diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);



        $invoiceCommercials = InvoiceCommercial::where('commercial_id', $usuario->id)->get();



        $contrato = CommercialContracts::where('admin_user_id', $usuario->id)->get()->first();



        /**** COMISIONES KIT DIGITAL ****/



        $comissionWebAsesor = CommercialCommission::where('commercial_product_id', 84)->where('commercial_level_id', 6)->pluck('quantity')->first();

        $comissionWebAsesorPrem = CommercialCommission::where('commercial_product_id', 84)->where('commercial_level_id', 11)->pluck('quantity')->first();

        $comissionWebManager = CommercialCommission::where('commercial_product_id', 84)->where('commercial_level_id', 8)->pluck('quantity')->first();

        $comissionWebEquipo = CommercialCommission::where('commercial_product_id', 84)->where('commercial_level_id', 9)->pluck('quantity')->first();



        /**** COMISIONES WEB ****/



        $comissionWebAsesor = CommercialCommission::where('commercial_product_id', 57)->where('commercial_level_id', 6)->pluck('quantity')->first();

        $comissionWebAsesorPrem = CommercialCommission::where('commercial_product_id', 57)->where('commercial_level_id', 7)->pluck('quantity')->first();

        $comissionWebManager = CommercialCommission::where('commercial_product_id', 57)->where('commercial_level_id', 8)->pluck('quantity')->first();

        $comissionWebEquipo = CommercialCommission::where('commercial_product_id', 57)->where('commercial_level_id', 9)->pluck('quantity')->first();



        /**** COMISIONES RENDERS ****/



        $comissionRenderAsesor = CommercialCommission::where('commercial_product_id', 45)->where('commercial_level_id', 6)->pluck('quantity')->first();

        $comissionRenderAsesorPrem = CommercialCommission::where('commercial_product_id', 45)->where('commercial_level_id', 7)->pluck('quantity')->first();

        $comissionRenderManager = CommercialCommission::where('commercial_product_id', 45)->where('commercial_level_id', 8)->pluck('quantity')->first();

        $comissionRenderEquipo = CommercialCommission::where('commercial_product_id', 45)->where('commercial_level_id', 9)->pluck('quantity')->first();



        /**** COMISIONES DISEÑO ****/



        $comissionDesignAsesor = CommercialCommission::where('commercial_product_id', 59)->where('commercial_level_id', 6)->pluck('quantity')->first();

        $comissionDesignAsesorPrem = CommercialCommission::where('commercial_product_id', 59)->where('commercial_level_id', 7)->pluck('quantity')->first();

        $comissionDesignManager = CommercialCommission::where('commercial_product_id', 59)->where('commercial_level_id', 8)->pluck('quantity')->first();

        $comissionDesignEquipo = CommercialCommission::where('commercial_product_id', 59)->where('commercial_level_id', 9)->pluck('quantity')->first();



        /**** COMISIONES REDES SOCIALES ****/



        $comissionRedesAsesor = CommercialCommission::where('commercial_product_id', 56)->where('commercial_level_id', 6)->pluck('quantity')->first();

        $comissionRedesAsesorPrem = CommercialCommission::where('commercial_product_id', 56)->where('commercial_level_id', 7)->pluck('quantity')->first();

        $comissionRedesManager = CommercialCommission::where('commercial_product_id', 56)->where('commercial_level_id', 8)->pluck('quantity')->first();

        $comissionRedesEquipo = CommercialCommission::where('commercial_product_id', 56)->where('commercial_level_id', 9)->pluck('quantity')->first();



        /**** COMISIONES IMPRENTA ****/



        $comissionImprAsesor = CommercialCommission::where('commercial_product_id', 60)->where('commercial_level_id', 6)->pluck('quantity')->first();

        $comissionImprnAsesorPrem = CommercialCommission::where('commercial_product_id', 60)->where('commercial_level_id', 7)->pluck('quantity')->first();

        $comissionImprManager = CommercialCommission::where('commercial_product_id', 60)->where('commercial_level_id', 8)->pluck('quantity')->first();

        $comissionImprEquipo = CommercialCommission::where('commercial_product_id', 60)->where('commercial_level_id', 9)->pluck('quantity')->first();



        /**** PRODUCTO WEB ***/



        $ProdWebStart = Services::find(191);



        $webStartAsesor = $this->getPercentOfNumber($ProdWebStart->price, $comissionWebAsesor);

        $webStartAsesorPrem = $this->getPercentOfNumber($ProdWebStart->price, $comissionWebAsesorPrem);

        $webStartManager = $this->getPercentOfNumber($ProdWebStart->price, $comissionWebManager);

        $webStartManagerEquipo = $this->getPercentOfNumber($ProdWebStart->price, $comissionWebEquipo);



        $ProdWebNegocio = Services::find(192);



        $webNegocioAsesor = $this->getPercentOfNumber($ProdWebNegocio->price, $comissionWebAsesor);

        $webNegocioAsesorPrem = $this->getPercentOfNumber($ProdWebNegocio->price, $comissionWebAsesorPrem);

        $webNegocioManager = $this->getPercentOfNumber($ProdWebNegocio->price, $comissionWebManager);

        $webNegocioManagerEquipo = $this->getPercentOfNumber($ProdWebNegocio->price, $comissionWebEquipo);



        $ProdWebNegocioPro = Services::find(193);



        $webNegocioProAsesor = $this->getPercentOfNumber($ProdWebNegocioPro->price, $comissionWebAsesor);

        $webNegocioProAsesorPrem = $this->getPercentOfNumber($ProdWebNegocioPro->price, $comissionWebAsesorPrem);

        $webNegocioProManager = $this->getPercentOfNumber($ProdWebNegocioPro->price, $comissionWebManager);

        $webNegocioProManagerEquipo = $this->getPercentOfNumber($ProdWebNegocioPro->price, $comissionWebEquipo);



        $ProdWebTienda = Services::find(194);



        $webTiendaAsesor = $this->getPercentOfNumber($ProdWebTienda->price, $comissionWebAsesor);

        $webTiendaAsesorPrem = $this->getPercentOfNumber($ProdWebTienda->price, $comissionWebAsesorPrem);

        $webTiendaManager = $this->getPercentOfNumber($ProdWebTienda->price, $comissionWebManager);

        $webTiendaManagerEquipo = $this->getPercentOfNumber($ProdWebTienda->price, $comissionWebEquipo);





        /********* PRODUCTO RENDERS *********/



        $ProdVivUnF = Services::find(195);



        $VivUnFAsesor = $this->getPercentOfNumber($ProdVivUnF->price, $comissionRenderAsesor);

        $VivUnFAsesorPrem = $this->getPercentOfNumber($ProdVivUnF->price, $comissionRenderAsesorPrem);

        $VivUnFManager = $this->getPercentOfNumber($ProdVivUnF->price, $comissionRenderManager);

        $VivUnFManagerEquipo = $this->getPercentOfNumber($ProdVivUnF->price, $comissionRenderEquipo);



        $ProdVivUnFPrem = Services::find(196);



        $VivUnFPremAsesor = $this->getPercentOfNumber($ProdVivUnFPrem->price, $comissionRenderAsesor);

        $VivUnFPremAsesorPrem = $this->getPercentOfNumber($ProdVivUnFPrem->price, $comissionRenderAsesorPrem);

        $VivUnFPremManager = $this->getPercentOfNumber($ProdVivUnFPrem->price, $comissionRenderManager);

        $VivUnFPremManagerEquipo = $this->getPercentOfNumber($ProdVivUnFPrem->price, $comissionRenderEquipo);



        $ProdVivRes = Services::find(197);



        $VivResAsesor = $this->getPercentOfNumber($ProdVivRes->price, $comissionRenderAsesor);

        $VivResAsesorPrem = $this->getPercentOfNumber($ProdVivRes->price, $comissionRenderAsesorPrem);

        $VivResManager = $this->getPercentOfNumber($ProdVivRes->price, $comissionRenderManager);

        $VivResManagerEquipo = $this->getPercentOfNumber($ProdVivRes->price, $comissionRenderEquipo);



        $ProdVivResPrem = Services::find(198);



        $VivResPremAsesor = $this->getPercentOfNumber($ProdVivResPrem->price, $comissionRenderAsesor);

        $VivResPremAsesorPrem = $this->getPercentOfNumber($ProdVivResPrem->price, $comissionRenderAsesorPrem);

        $VivResPremManager = $this->getPercentOfNumber($ProdVivResPrem->price, $comissionRenderManager);

        $VivResPremManagerEquipo = $this->getPercentOfNumber($ProdVivResPrem->price, $comissionRenderEquipo);



        /********* PRODUCTO DESIGN *********/



        $ProdLogo = Services::find(199);



        $LogoAsesor = $this->getPercentOfNumber($ProdLogo->price, $comissionDesignAsesor);

        $LogoAsesorPrem = $this->getPercentOfNumber($ProdLogo->price, $comissionDesignAsesorPrem);

        $LogoManager = $this->getPercentOfNumber($ProdLogo->price, $comissionDesignManager);

        $LogoManagerEquipo = $this->getPercentOfNumber($ProdLogo->price, $comissionDesignEquipo);



        $ProdTarjeta = Services::find(200);



        $TarjetaAsesor = $this->getPercentOfNumber($ProdTarjeta->price, $comissionDesignAsesor);

        $TarjetaAsesorPrem = $this->getPercentOfNumber($ProdTarjeta->price, $comissionDesignAsesorPrem);

        $TarjetaManager = $this->getPercentOfNumber($ProdTarjeta->price, $comissionDesignManager);

        $TarjetaManagerEquipo = $this->getPercentOfNumber($ProdTarjeta->price, $comissionDesignEquipo);



        $ProdFlyer = Services::find(201);



        $FlyerAsesor = $this->getPercentOfNumber($ProdFlyer->price, $comissionDesignAsesor);

        $FlyerAsesorPrem = $this->getPercentOfNumber($ProdFlyer->price, $comissionDesignAsesorPrem);

        $FlyerManager = $this->getPercentOfNumber($ProdFlyer->price, $comissionDesignManager);

        $FlyerManagerEquipo = $this->getPercentOfNumber($ProdFlyer->price, $comissionDesignEquipo);



        $ProdCarta = Services::find(202);



        $CartaAsesor = $this->getPercentOfNumber($ProdCarta->price, $comissionDesignAsesor);

        $CartaAsesorPrem = $this->getPercentOfNumber($ProdCarta->price, $comissionDesignAsesorPrem);

        $CartaManager = $this->getPercentOfNumber($ProdCarta->price, $comissionDesignManager);

        $CartaManagerEquipo = $this->getPercentOfNumber($ProdCarta->price, $comissionDesignEquipo);





        /********* PRODUCTO REDES *********/



        $ProdR1 = Services::find(203);

        // dd($ProdR1);



        $ProdR1Asesor = $this->getPercentOfNumber($ProdR1->price, $comissionRedesAsesor);

        $ProdR1AsesorPrem = $this->getPercentOfNumber($ProdR1->price, $comissionRedesAsesorPrem);

        $ProdR1Manager = $this->getPercentOfNumber($ProdR1->price, $comissionRedesManager);

        $ProdR1ManagerEquipo = $this->getPercentOfNumber($ProdR1->price, $comissionRedesEquipo);



        $ProdR2 = Services::find(204);



        $ProdR2Asesor = $this->getPercentOfNumber($ProdR2->price, $comissionRedesAsesor);

        $ProdR2AsesorPrem = $this->getPercentOfNumber($ProdR2->price, $comissionRedesAsesorPrem);

        $ProdR2Manager = $this->getPercentOfNumber($ProdR2->price, $comissionRedesManager);

        $ProdR2ManagerEquipo = $this->getPercentOfNumber($ProdR2->price, $comissionRedesEquipo);



        $ProdR3 = Services::find(205);



        $ProdR3Asesor = $this->getPercentOfNumber($ProdR3->price, $comissionRedesAsesor);

        $ProdR3AsesorPrem = $this->getPercentOfNumber($ProdR3->price, $comissionRedesAsesorPrem);

        $ProdR3Manager = $this->getPercentOfNumber($ProdR3->price, $comissionRedesManager);

        $ProdR3ManagerEquipo = $this->getPercentOfNumber($ProdR3->price, $comissionRedesEquipo);



        $ProdR4 = Services::find(206);



        $ProdR4Asesor = $this->getPercentOfNumber($ProdR4->price, $comissionRedesAsesor);

        $ProdR4AsesorPrem = $this->getPercentOfNumber($ProdR4->price, $comissionRedesAsesorPrem);

        $ProdR4Manager = $this->getPercentOfNumber($ProdR4->price, $comissionRedesManager);

        $ProdR4ManagerEquipo = $this->getPercentOfNumber($ProdR4->price, $comissionRedesEquipo);



        $ProdR5 = Services::find(207);



        $ProdR5Asesor = $this->getPercentOfNumber($ProdR5->price, $comissionRedesAsesor);

        $ProdR5AsesorPrem = $this->getPercentOfNumber($ProdR5->price, $comissionRedesAsesorPrem);

        $ProdR5Manager = $this->getPercentOfNumber($ProdR5->price, $comissionRedesManager);

        $ProdR5ManagerEquipo = $this->getPercentOfNumber($ProdR5->price, $comissionRedesEquipo);



        /***** APPS ****/



        $ProdAppAndroid = Services::find(208);



        $ProdApp1Asesor = $this->getPercentOfNumber($ProdAppAndroid->price, $comissionWebAsesor);

        $ProdApp1AsesorPrem = $this->getPercentOfNumber($ProdAppAndroid->price, $comissionWebAsesorPrem);

        $ProdApp1Manager = $this->getPercentOfNumber($ProdAppAndroid->price, $comissionWebManager);

        $ProdApp1ManagerEquipo = $this->getPercentOfNumber($ProdAppAndroid->price, $comissionWebEquipo);



        $ProdAppiOS = Services::find(209);



        $ProdApp2Asesor = $this->getPercentOfNumber($ProdAppiOS->price, $comissionWebAsesor);

        $ProdApp2AsesorPrem = $this->getPercentOfNumber($ProdAppiOS->price, $comissionWebAsesorPrem);

        $ProdApp2Manager = $this->getPercentOfNumber($ProdAppiOS->price, $comissionWebManager);

        $ProdApp2ManagerEquipo = $this->getPercentOfNumber($ProdAppiOS->price, $comissionWebEquipo);



        /***** IMPRENTA *****/



        $ProdImp1 = Services::find(211);



        $ProdImp1Asesor = $this->getPercentOfNumber($ProdImp1->price, $comissionImprAsesor);

        $ProdImp1AsesorPrem = $this->getPercentOfNumber($ProdImp1->price, $comissionImprnAsesorPrem);

        $ProdImp1Manager = $this->getPercentOfNumber($ProdImp1->price, $comissionImprnAsesorPrem);

        $ProdImp1ManagerEquipo = $this->getPercentOfNumber($ProdImp1->price, $comissionImprnAsesorPrem);



        $ProdImp2 = Services::find(212);



        $ProdImp2Asesor = $this->getPercentOfNumber($ProdImp2->price, $comissionImprAsesor);

        $ProdImp2AsesorPrem = $this->getPercentOfNumber($ProdImp2->price, $comissionImprnAsesorPrem);

        $ProdImp2Manager = $this->getPercentOfNumber($ProdImp2->price, $comissionImprManager);

        $ProdImp2ManagerEquipo = $this->getPercentOfNumber($ProdImp2->price, $comissionImprEquipo);



        $ProdImp3 = Services::find(213);



        $ProdImp3Asesor = $this->getPercentOfNumber($ProdImp3->price, $comissionImprAsesor);

        $ProdImp3AsesorPrem = $this->getPercentOfNumber($ProdImp3->price, $comissionImprnAsesorPrem);

        $ProdImp3Manager = $this->getPercentOfNumber($ProdImp3->price, $comissionImprManager);

        $ProdImp3ManagerEquipo = $this->getPercentOfNumber($ProdImp3->price, $comissionImprEquipo);



        $ProdImp4 = Services::find(214);



        $ProdImp4Asesor = $this->getPercentOfNumber($ProdImp4->price, $comissionImprAsesor);

        $ProdImp4AsesorPrem = $this->getPercentOfNumber($ProdImp4->price, $comissionImprnAsesorPrem);

        $ProdImp4Manager = $this->getPercentOfNumber($ProdImp4->price, $comissionImprManager);

        $ProdImp4ManagerEquipo = $this->getPercentOfNumber($ProdImp4->price, $comissionImprEquipo);



        $ProdImp5 = Services::find(215);



        $ProdImp5Asesor = $this->getPercentOfNumber($ProdImp5->price, $comissionImprAsesor);

        $ProdImp5AsesorPrem = $this->getPercentOfNumber($ProdImp5->price, $comissionImprnAsesorPrem);

        $ProdImp5Manager = $this->getPercentOfNumber($ProdImp5->price, $comissionImprManager);

        $ProdImp5ManagerEquipo = $this->getPercentOfNumber($ProdImp5->price, $comissionImprEquipo);



        $ProdImp6 = Services::find(216);



        $ProdImp6Asesor = $this->getPercentOfNumber($ProdImp6->price, $comissionImprAsesor);

        $ProdImp6AsesorPrem = $this->getPercentOfNumber($ProdImp6->price, $comissionImprnAsesorPrem);

        $ProdImp6Manager = $this->getPercentOfNumber($ProdImp6->price, $comissionImprManager);

        $ProdImp6ManagerEquipo = $this->getPercentOfNumber($ProdImp6->price, $comissionImprEquipo);



        $ProdImp7 = Services::find(217);



        $ProdImp7Asesor = $this->getPercentOfNumber($ProdImp7->price, $comissionImprAsesor);

        $ProdImp7AsesorPrem = $this->getPercentOfNumber($ProdImp7->price, $comissionImprnAsesorPrem);

        $ProdImp7Manager = $this->getPercentOfNumber($ProdImp7->price, $comissionImprManager);

        $ProdImp7ManagerEquipo = $this->getPercentOfNumber($ProdImp7->price, $comissionImprEquipo);



        $taskGestor = Task::where("admin_user_id", Auth::user()->id)->get()->first();

        $fechaEmision = Carbon::now();

        $fechaExpiracion = new Carbon('last day of this month');



        $diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);



        $getServices = ServicesCategories::where('type', 0)->get();

        $estadosKit = DB::table('ayudas_estados_kit')->get();



        return view($dashboard, compact(
            'taskGestor',
            'ayudas',
            'estadosKit',
            'pedienteCierre',
            'comisionCurso',
            'comisionPendiente',
            'comisionTramitadas',
            'comisionRestante',
            'alertasActivadas',
            'kitDigitalArray',
            'presupuestoTable',
            'diasDiferencia',

            'calendar',

            'cobrado',

            'pendiente',

            'budgets',

            'notes',

            'clients',

            'usuario',

            'hasganado',

            'audioVisual',

            'renting',

            'eventos',

            'kitDigital',

            'getServices',

            'curso',

            'arrayMesesComercial',

            'arrayMeses',

            'webs',

            'renders',

            'design',

            'redes',

            'imprenta',

            'diasDiferencia',

            'invoiceCommercials',

            'contrato',

            'webStartAsesor',

            'webStartAsesorPrem',

            'webStartManager',

            'webStartManagerEquipo',

            'webNegocioAsesor',

            'webNegocioAsesorPrem',

            'webNegocioManager',

            'webNegocioManagerEquipo',

            'webNegocioProAsesor',

            'webNegocioProAsesorPrem',

            'webNegocioProManager',

            'webNegocioProManagerEquipo',

            'webTiendaAsesor',

            'webTiendaAsesorPrem',

            'webTiendaManager',

            'webTiendaManagerEquipo',

            'VivUnFAsesor',

            'VivUnFAsesorPrem',

            'VivUnFManager',

            'VivUnFManagerEquipo',

            'VivUnFPremAsesor',

            'VivUnFPremAsesorPrem',

            'VivUnFPremManager',

            'VivUnFPremManagerEquipo',

            'VivResAsesor',

            'VivResAsesorPrem',

            'VivResManager',

            'VivResManagerEquipo',

            'VivResPremAsesor',

            'VivResPremAsesorPrem',

            'VivResPremManager',

            'VivResPremManagerEquipo',

            'LogoAsesor',

            'LogoAsesorPrem',

            'LogoManager',

            'LogoManagerEquipo',

            'TarjetaAsesor',

            'TarjetaAsesorPrem',

            'TarjetaManager',

            'TarjetaManagerEquipo',

            'FlyerAsesor',

            'FlyerAsesorPrem',

            'FlyerManager',

            'FlyerManagerEquipo',

            'CartaAsesor',

            'CartaAsesorPrem',
            'CartaManager',
            'CartaManagerEquipo',
            'ProdR1Asesor',
            'ProdR1AsesorPrem',
            'ProdR1Manager',
            'ProdR1ManagerEquipo',
            'ProdR2Asesor',
            'ProdR2AsesorPrem',
            'ProdR2Manager',
            'ProdR2ManagerEquipo',
            'ProdR3Asesor',
            'ProdR3AsesorPrem',
            'ProdR3Manager',
            'ProdR3ManagerEquipo',
            'ProdR4Asesor',
            'ProdR4AsesorPrem',
            'ProdR4Manager',
            'ProdR4ManagerEquipo',
            'ProdR5Asesor',
            'ProdR5AsesorPrem',
            'ProdR5Manager',
            'ProdR5ManagerEquipo',
            'ProdApp1Asesor',
            'ProdApp1AsesorPrem',
            'ProdApp1Manager',
            'ProdApp1ManagerEquipo',
            'ProdApp2Asesor',
            'ProdApp2AsesorPrem',
            'ProdApp2Manager',
            'ProdApp2ManagerEquipo',
            'ProdImp1Asesor',
            'ProdImp1AsesorPrem',
            'ProdImp1Manager',
            'ProdImp1ManagerEquipo',
            'ProdImp2Asesor',
            'ProdImp2AsesorPrem',
            'ProdImp2Manager',
            'ProdImp2ManagerEquipo',
            'ProdImp3Asesor',
            'ProdImp3AsesorPrem',
            'ProdImp3Manager',
            'ProdImp3ManagerEquipo',
            'ProdImp4Asesor',
            'ProdImp4AsesorPrem',
            'ProdImp4Manager',
            'ProdImp4ManagerEquipo',
            'ProdImp5Asesor',
            'ProdImp5AsesorPrem',
            'ProdImp5Manager',
            'ProdImp5ManagerEquipo',
            'ProdImp6Asesor',
            'ProdImp6AsesorPrem',
            'ProdImp6Manager',
            'ProdImp6ManagerEquipo',
            'ProdImp7Asesor',
            'ProdImp7AsesorPrem',
            'ProdImp7Manager',
            'ProdImp7ManagerEquipo',
        ));
    }

}
