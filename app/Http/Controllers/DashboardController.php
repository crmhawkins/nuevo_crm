<?php

namespace App\Http\Controllers;

use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\Jornada\Jornada;
use App\Models\Jornada\Pause;
use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
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
        $llamadaActiva = $user->activeLlamada();
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
                return view('dashboards.dashboard_gestor', compact('user','tareas','to_dos','budgets','projects','clientes','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva','llamadaActiva'));
            case(2):
                $clientes = Client::where('is_client',true)->get();
                $budgets = Budget::where('admin_user_id',$id)->get();
                $projects = Project::where('admin_user_id',$id)->get();
                $tareas = Task::where('gestor_id',$id)->get();
                return view('dashboards.dashboard_gestor', compact('user','tareas','to_dos','budgets','projects','clientes','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva','llamadaActiva'));
            case(3):
                $clientes = Client::where('is_client',true)->get();
                $budgets = Budget::where('admin_user_id',$id)->get();
                $projects = Project::where('admin_user_id',$id)->get();
                $tareas = Task::where('gestor_id',$id)->get();
                return view('dashboards.dashboard_gestor', compact('user','tareas','to_dos','budgets','projects','clientes','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva','llamadaActiva'));
            case(4):
                $clientes = Client::where('is_client',true)->get();
                $budgets = Budget::where('admin_user_id',$id)->get();
                $projects = Project::where('admin_user_id',$id)->get();
                $tareas = Task::where('gestor_id',$id)->get();
                $v1 = count(Budget::where('admin_user_id',2)->whereYear('created_at',2202)->get())/12;
                return view('dashboards.dashboard_gestor', compact('user','tareas','to_dos','budgets','projects','clientes','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva','llamadaActiva'));
            case(5):
                $tareas = $user->tareas->whereIn('task_status_id', [1, 2, 5]);
                $tiempoProducidoHoy = $this->tiempoProducidoHoy();
                $tasks = $this->getTasks($user->id);
                return view('dashboards.dashboard_personal', compact('user','tiempoProducidoHoy','tasks','tareas','to_dos','users','events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva'));
            case(6):
                $ayudas = KitDigital::where('comercial_id', $user->id)->get();
                $fechaEmision = Carbon::now();
                $fechaExpiracion = new Carbon('last day of this month');
                $diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);
                $pedienteCierre = 0;
                $comisionCurso = 0;
                $comisionPendiente = 0;
                $comisionTramitadas = 0;
                $comisionRestante = 0;
                $estadosKit = KitDigitalEstados::all();

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
                return view('dashboards.dashboard_comercial', compact('user','diasDiferencia','estadosKit','comisionRestante','ayudas','comisionTramitadas','comisionPendiente', 'comisionCurso', 'pedienteCierre','timeWorkedToday', 'jornadaActiva', 'pausaActiva'));
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
        $data = $request->validate([
            'client_id' => 'nullable|required_without:phone',
            'phone' => 'nullable|required_without:client_id',
            'comentario'=> 'nullable'
        ], [
            'client_id.required_without' => 'El campo cliente es obligatorio si el teléfono no está presente.',
            'phone.required_without' => 'El campo teléfono es obligatorio si el cliente no está presente.',
        ]);
        $llamada =  Llamada::create([
            'admin_user_id' => $user->id,
            'start_time' => Carbon::now(),
            'is_active' => true,
            'client_id' => $data['client_id'] ?? null,
            'phone'=> $data['phone'] ?? null,
            'comentario' => $data['comentario'] ?? null
        ]);
        return redirect()->back()->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Llamada iniciada'
        ]);

    }

    public function finalizar()
    {
        $user = Auth::user();
        $llamada = Llamada::where('admin_user_id', $user->id)->where('is_active', true)->first();
        if ($llamada) {
            $finllamada = $llamada->update([
                'end_time' => Carbon::now(),
                'is_active' => false,
            ]);
            return response()->json(['success' => true ,'mensaje' => 'Llamada Finalizada']);
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
            'start_time' => Carbon::now(),
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

                        // $logTask = DB::select("SELECT id FROM `log_tasks` WHERE date_start BETWEEN DATE_SUB(now(), interval 6 hour) AND DATE_ADD(NOW(), INTERVAL 7 hour) AND `admin_user_id` = $usuario->id");
                        // if (count(value: $logTask) == 1) {
                        //     $horly = HourlyAverage::create(
                        //         [
                        //             'admin_user_id' => $usuario->id,
                        //             'log_task_id' => $createLog->id,
                        //             'hours' => $date->format('H:i:s'),
                        //         ]
                        //     );


                        //     $note = $this->calculateNote($horly->hours);

                        //     $fechaNow = Carbon::now();

                        //     if ($note == 0) {

                        //         $hourlyAverage = DB::select("SELECT hours FROM `hourly_average` WHERE created_at BETWEEN LAST_DAY(now() - interval 1 month) AND LAST_DAY(NOW()) AND `admin_user_id` = $usuario->id AND `hours` > '09:05:00'");
                        //         if (count($hourlyAverage) > 2) {
                        //             $data = [
                        //                 "admin_user_id" =>  1,
                        //                 "stage_id" => 15,
                        //                 "description" => $usuario->name . " ha llegado tarde 3 veces o mas este mes",
                        //                 "status_id" => AlertStatus::ALERT_STATUS_PENDING,
                        //                 "reference_id" => $horly->id,
                        //                 "activation_datetime" => $fechaNow->format('Y-m-d H:i:s')
                        //             ];

                        //             $alert = Alert::create($data);
                        //             $alertSaved = $alert->save();
                        //         }
                        //     }

                        //     $text = $this->mensajeMediaHora($note);


                        //     $fechaNow = Carbon::now();



                        //     $data = [
                        //         "admin_user_id" =>  $usuario->id,
                        //         "stage_id" => 23,
                        //         "description" => $text,
                        //         "status_id" => AlertStatus::ALERT_STATUS_PENDING,
                        //         "reference_id" => $horly->id,
                        //         "activation_datetime" => $fechaNow->format('Y-m-d H:i:s')
                        //     ];

                        //     $alert = Alert::create($data);
                        //     $alertSaved = $alert->save();
                        // }
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

                    $dataAlert = [
                        'admin_user_id' => $tarea->gestor_id,
                        'stage_id' => 41,
                        'activation_datetime' => $date->format('Y-m-d H:i:s'),
                        'status_id' => 1,
                        'reference_id' => $tarea->id,
                    ];

                    $alert = Alert::create($dataAlert);
                    $alertSaved = $alert->save();


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
}
