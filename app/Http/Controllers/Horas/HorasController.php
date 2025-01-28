<?php

namespace App\Http\Controllers\Horas;

use App\Exports\JornadasExport;
use App\Http\Controllers\Controller;
use App\Models\Alerts\Alert;
use App\Models\Bajas\Baja;
use App\Models\Holidays\HolidaysPetitions;
use App\Models\Jornada\Jornada;
use App\Models\Tasks\LogTasks;
use App\Models\Users\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HorasController extends Controller
{
    // protected function indexHoras(Request $request){

    //     $selectedWeek = Carbon::parse($request->input('week', now()->format('Y-\WW')));

    //     // Días de la Semana
    //     $lunes = $selectedWeek->copy()->startOfWeek();
    //     $martes = $selectedWeek->copy()->startOfWeek()->addDays(1);
    //     $miercoles = $selectedWeek->copy()->startOfWeek()->addDays(2);
    //     $jueves = $selectedWeek->copy()->startOfWeek()->addDays(3);
    //     $viernes = $selectedWeek->copy()->startOfWeek()->addDays(4);

    //     // Obtengo todos los usuarios
    //     $users =  User::where('inactive',0)->get();
    //     $arrayUsuarios = [];


    //     // Recorro los usuarios
    //     foreach ($users as $usuario) {
    //         // Este if es para que no salgan los mensajes del segundo usuario de Camila, se puede borrar
    //         if($usuario->id != 81 && $usuario->id != 52){
    //             // Se imprimen las horas trabajadas de cada usuario en minutos y luego se pone en texto
    //             $horasTrabajadasLunes = $this->horasTrabajadasDia($lunes, $usuario->id);
    //             $horasTrabajadasMartes = $this->horasTrabajadasDia($martes, $usuario->id);
    //             $horasTrabajadasMiercoles = $this->horasTrabajadasDia($miercoles, $usuario->id);
    //             $horasTrabajadasJueves = $this->horasTrabajadasDia($jueves, $usuario->id);
    //             $horasTrabajadasViernes = $this->horasTrabajadasDia($viernes, $usuario->id);

    //             $horasTrabajadasSemana = $horasTrabajadasLunes + $horasTrabajadasMartes + $horasTrabajadasMiercoles + $horasTrabajadasJueves + $horasTrabajadasViernes;

    //             // Se imprimen las horas producidas de cada usuario en minutos y luego se pone en texto
    //             $horasProducidasLunes       = $this->tiempoProducidoDia($lunes, $usuario->id);
    //             $horasProducidasMartes      = $this->tiempoProducidoDia($martes, $usuario->id);
    //             $horasProducidasMiercoles   = $this->tiempoProducidoDia($miercoles, $usuario->id);
    //             $horasProducidasJueves      = $this->tiempoProducidoDia($jueves, $usuario->id);
    //             $horasProducidasViernes     = $this->tiempoProducidoDia($viernes, $usuario->id);

    //             $vacaciones = $this->vacaciones($lunes, $viernes, $usuario->id);
    //             $puntualidad = $this->puntualidad($lunes, $viernes, $usuario->id);
    //             $baja = $this->bajas($usuario->id, $lunes, $viernes);


    //             $horasProducidasSemana = $horasProducidasLunes + $horasProducidasMartes + $horasProducidasMiercoles + $horasProducidasJueves + $horasProducidasViernes;

    //             if($horasTrabajadasSemana > 0){
    //                 //semana
    //                 $horaHorasTrabajadas = floor($horasTrabajadasSemana / 60);
    //                 $minutoHorasTrabajadas = ($horasTrabajadasSemana % 60);
    //                 //lunes
    //                 $horaHorasTrabajadasLunes = floor($horasTrabajadasLunes / 60);
    //                 $minutoHorasTrabajadasLunes = ($horasTrabajadasLunes % 60);
    //                 //martes
    //                 $horaHorasTrabajadasMartes = floor($horasTrabajadasMartes / 60);
    //                 $minutoHorasTrabajadasMartes = ($horasTrabajadasMartes % 60);
    //                 //miercoles
    //                 $horaHorasTrabajadasMiercoles = floor($horasTrabajadasMiercoles / 60);
    //                 $minutoHorasTrabajadasMiercoles = ($horasTrabajadasMiercoles % 60);
    //                 //jueves
    //                 $horaHorasTrabajadasJueves = floor($horasTrabajadasJueves / 60);
    //                 $minutoHorasTrabajadasJueves = ($horasTrabajadasJueves % 60);
    //                 //viernes
    //                 $horaHorasTrabajadasViernes = floor($horasTrabajadasViernes / 60);
    //                 $minutoHorasTrabajadasViernes = ($horasTrabajadasViernes % 60);

    //                 //semana
    //                 $horaHorasProducidas = floor($horasProducidasSemana / 60);
    //                 $minutoHorasProducidas = ($horasProducidasSemana % 60);
    //                 //lunes
    //                 $horaHorasProducidasLunes = floor($horasProducidasLunes / 60);
    //                 $minutoHorasProducidasLunes = ($horasProducidasLunes % 60);
    //                 //martes
    //                 $horaHorasProducidasMartes = floor($horasProducidasMartes / 60);
    //                 $minutoHorasProducidasMartes = ($horasProducidasMartes % 60);
    //                 //miercoles
    //                 $horaHorasProducidasMiercoles = floor($horasProducidasMiercoles / 60);
    //                 $minutoHorasProducidasMiercoles = ($horasProducidasMiercoles % 60);
    //                 //jueves
    //                 $horaHorasProducidasJueves = floor($horasProducidasJueves / 60);
    //                 $minutoHorasProducidasJueves = ($horasProducidasJueves % 60);
    //                 //viernes
    //                 $horaHorasProducidasViernes = floor($horasProducidasViernes / 60);
    //                 $minutoHorasProducidasViernes = ($horasProducidasViernes % 60);



    //                 $arrayUsuarios[] = [
    //                     'usuario' => $usuario->name.' '.$usuario->surname ,
    //                     'departamento' => $usuario->departamento->name,
    //                     'vacaciones' => $vacaciones,
    //                     'puntualidad' => $puntualidad,
    //                     'baja' => $baja,
    //                     'horas_trabajadas' => "$horaHorasTrabajadas h $minutoHorasTrabajadas min",
    //                     'horasTrabajadasLunes' => "$horaHorasTrabajadasLunes h $minutoHorasTrabajadasLunes min",
    //                     'horasTrabajadasMartes' => "$horaHorasTrabajadasMartes h $minutoHorasTrabajadasMartes min",
    //                     'horasTrabajadasMiercoles' => "$horaHorasTrabajadasMiercoles h $minutoHorasTrabajadasMiercoles min",
    //                     'horasTrabajadasJueves' => "$horaHorasTrabajadasJueves h $minutoHorasTrabajadasJueves min",
    //                     'horasTrabajadasViernes' => "$horaHorasTrabajadasViernes h $minutoHorasTrabajadasViernes min",
    //                     'horas_producidas' => "$horaHorasProducidas h $minutoHorasProducidas min",
    //                     'horasProducidasLunes' => "$horaHorasProducidasLunes h $minutoHorasProducidasLunes min",
    //                     'horasProducidasMartes' => "$horaHorasProducidasMartes h $minutoHorasProducidasMartes min",
    //                     'horasProducidasMiercoles' => "$horaHorasProducidasMiercoles h $minutoHorasProducidasMiercoles min",
    //                     'horasProducidasJueves' => "$horaHorasProducidasJueves h $minutoHorasProducidasJueves min",
    //                     'horasProducidasViernes' => "$horaHorasProducidasViernes h $minutoHorasProducidasViernes min",
    //                 ];
    //             }
    //         }
    //     }
    //     return view('horas.index', ['usuarios' => $arrayUsuarios]);
    // }

    protected function indexHoras(Request $request){

        // Recibir rango de fechas en lugar de semana
        $fechaInicio = Carbon::parse($request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d')));
        $fechaFin = Carbon::parse($request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d')));

        // Generar array de todos los días dentro del rango
        $periodo = Carbon::parse($fechaInicio)->daysUntil($fechaFin);
        $todosLosDias = [];
        foreach ($periodo as $dia) {
            $todosLosDias[$dia->format('Y-m-d')] = $dia->copy();
        }

        // Obtener todos los usuarios activos
        $users = User::where('inactive', 0)->get();
        $arrayUsuarios = [];

        // Recorrido de usuarios y cálculo de horas por cada día
        foreach ($users as $usuario) {
            if ($usuario->id != 81 && $usuario->id != 52) { // Filtro de usuarios específicos
                $datosUsuario = [
                    'usuario' => $usuario->name . ' ' . $usuario->surname,
                    'departamento' => $usuario->departamento->name,
                    'horas_trabajadas' => [],
                    'horas_producidas' => []
                ];

                $totalHorasTrabajadas = 0;
                $totalHorasProducidas = 0;

                // Calcular horas por cada día dentro del rango
                foreach ($todosLosDias as $fecha => $dia) {
                    $horaHorasTrabajadasdia = 0;
                    $minutoHorasTrabajadasdia = 0;
                    $horaHorasProducidasdia = 0;
                    $minutoHorasProducidasdia = 0;
                    $horasTrabajadas = $this->horasTrabajadasDia($dia, $usuario->id);
                    $horasProducidas = $this->tiempoProducidoDia($dia, $usuario->id);
                    $totalHorasTrabajadas += $horasTrabajadas;
                    $totalHorasProducidas += $horasProducidas;
                    $horaHorasTrabajadasdia = floor($horasTrabajadas / 60);
                    $minutoHorasTrabajadasdia = ($horasTrabajadas % 60);
                    $horaHorasProducidasdia = floor($horasProducidas / 60);
                    $minutoHorasProducidasdia = ($horasProducidas % 60);
                    $horaInicio = $this->horaInicioJornada($dia, $usuario->id);
                    $datosUsuario['horas_trabajadas'][$fecha] = "$horaHorasTrabajadasdia h $minutoHorasTrabajadasdia min";
                    $datosUsuario['horas_producidas'][$fecha] = "$horaHorasProducidasdia h $minutoHorasProducidasdia min";
                    $datosUsuario['inicio_jornada'][$fecha] = $horaInicio;
                }

                $horaHorasTrabajadas = floor($totalHorasTrabajadas / 60);
                $minutoHorasTrabajadas = ($totalHorasTrabajadas % 60);

                $horaHorasProducidas = floor($totalHorasProducidas / 60);
                $minutoHorasProducidas = ($totalHorasProducidas % 60);

                $datosUsuario['total_horas_trabajadas'] = "$horaHorasTrabajadas h $minutoHorasTrabajadas min";
                $datosUsuario['total_horas_producidas'] = "$horaHorasProducidas h $minutoHorasProducidas min";
                $arrayUsuarios[] = $datosUsuario;
            }
        }

        return view('horas.index', ['usuarios' => $arrayUsuarios, 'todosLosDias' => array_keys($todosLosDias)]);
    }

    public function exportHoras(Request $request)
    {
        $week = $request->input('week', now()->format('Y-\WW'));
        return Excel::download(new JornadasExport($week), 'jornadas_semanales.xlsx');
    }

    public function vacaciones($ini, $fin, $id){
        $vacaciones = HolidaysPetitions::where('admin_user_id', $id)
        ->whereDate('from','>=', $ini)
        ->whereDate('to','<=', $fin)
        ->where('holidays_status_id', 1)
        ->get();

        $dias = $vacaciones->sum('total_days');

        return $dias;
    }

    public function puntualidad($ini, $fin, $id){
        $puntualidad = Alert::where('admin_user_id', $id)
        ->whereDate('created_at','>=', $ini)
        ->whereDate('created_at','<=', $fin)
        ->where('stage_id', 23)
        ->whereRaw('admin_user_id = reference_id')
        ->get();

        $dias = $puntualidad->count();

        return $dias;
    }

    public function horasTrabajadasDia($dia, $id){

        $totalWorkedSeconds = 0;
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
    public function horaInicioJornada($dia, $id){

        $jornada = Jornada::where('admin_user_id', $id)
        ->whereDate('start_time', $dia)
        ->get()->first();
        if(!isset($jornada)){
            return 'N/A';
        }
        $inicio = Carbon::createFromFormat('Y-m-d H:i:s', $jornada->start_time, 'UTC');
        $inicioEspaña = $inicio->setTimezone('Europe/Madrid');

        return $inicioEspaña->format('H:i:s');
    }

    public function tiempoProducidoDia($dia, $id) {
        $tiempoTarea = 0;
        $tareasHoy = LogTasks::where('admin_user_id', $id)->whereDate('date_start','=', $dia)->get();
        foreach($tareasHoy as $tarea) {
            if ($tarea->status == 'Pausada') {
                $tiempoini = Carbon::parse($tarea->date_start);
                $tiempoFinal = Carbon::parse($tarea->date_end);
                $tiempoTarea +=  $tiempoFinal->diffInMinutes($tiempoini);
            }
        }
        return $tiempoTarea;
    }

    public function bajas($id, $ini, $fin,) {

        $diasTotales = 0;
        $bajas = Baja::where('admin_user_id', $id)
            ->where(function ($query) use ($ini, $fin) {
            $query->whereBetween('inicio', [$ini, $fin])
                  ->orWhereBetween('fin', [$ini, $fin])
                  ->orWhere(function ($query) use ($ini, $fin) {
                      $query->where('inicio', '<=', $ini)
                            ->where('fin', '>=', $fin);
                  });
        })->get();

        foreach ($bajas as $baja) {
            $inicioBaja = Carbon::parse($baja->inicio);
            $finBaja = Carbon::parse($baja->fin) ?? Carbon::now();

            // Ajustar fechas al intervalo especificado
            $fechaInicio = $inicioBaja->greaterThan($ini) ? $inicioBaja : $ini;
            $fechaFin = $finBaja->lessThan($fin) ? $finBaja : $fin;

            // Calcular los días entre las fechas ajustadas y sumarlos
            $dias = $fechaInicio->diffInDays($fechaFin) + 1;
            $diasTotales += $dias;
        }

        return $diasTotales;

    }

    public function calendar($id)
    {
        $user = User::where('id', $id)->first();

        // Obtener los eventos de tareas para el usuario
        $events = $this->getjornadas($id);
        // Convertir los eventos en formato adecuado para FullCalendar (si no están ya en ese formato)
        $eventData = [];
        foreach ($events as $event) {

            $inicio = Carbon::createFromFormat('Y-m-d H:i:s', $event[1], 'UTC');
            $inicioEspaña = $inicio->setTimezone('Europe/Madrid');
            if(isset($event[2])){
                $fin = Carbon::createFromFormat('Y-m-d H:i:s', $event[2], 'UTC');
                $finEspaña = $fin->setTimezone('Europe/Madrid');
            }

            $eventData[] = [
                'title' => $event[0],
                'start' => $inicioEspaña->toIso8601String(), // Aquí debería estar la fecha y hora de inicio
                'end' => $event[2] ? $finEspaña->toIso8601String() : null , // Aquí debería estar la fecha y hora de fin
                'allDay' => false, // Indica si el evento es de todos los días
                'color' =>$event[3]
            ];
        }
        // Datos adicionales de horas trabajadas y producidas
        $horas_hoy = $this->getHorasTrabajadasHoy($user);
        $horas_hoy2 = $this->getHorasTrabajadasHoy2($user);

        // Pasar los datos de eventos a la vista como JSON
        return view('horas.timeLine', [
            'user' => $user,
            'horas_hoy' => $horas_hoy,
            'horas_hoy2' => $horas_hoy2,
            'events' => $eventData // Enviar los eventos como JSON
        ]);
    }


    // Horas producidas hoy
    public function getHorasTrabajadasHoy($user)
    {
        // Se obtiene los datos
        $id = $user->id;
        $fecha = Carbon::now()->toDateString();;
        $resultado = 0;
        $totalMinutos2 = 0;

        $logsTasks = LogTasks::where('admin_user_id', $id)
        ->whereDate('date_start', '=', $fecha)
        ->get();

        foreach($logsTasks as $item){
            if($item->date_end == null){
                $item->date_end = Carbon::now();
            }
            $to_time2 = strtotime($item->date_start);
            $from_time2 = strtotime($item->date_end);
            $minutes2 = ($from_time2 - $to_time2) / 60;
            $totalMinutos2 += $minutes2;
        }

        $hora2 = floor($totalMinutos2 / 60);
        $minuto2 = ($totalMinutos2 % 60);
        $horas_dia2 = $hora2 . ' Horas y ' . $minuto2 . ' minutos';

        $resultado = $horas_dia2;

        return $resultado;
    }

    // Horas trabajadas hoy
    public function getHorasTrabajadasHoy2($user)
    {
         // Se obtiene los datos
         $id = $user->id;
         $fecha = Carbon::now()->toDateString();
         $hoy = Carbon::now();
         $resultado = 0;
         $totalMinutos2 = 0;


        $almuerzoHoras = 0;

        $jornadas = Jornada::where('admin_user_id', $id)
        ->whereDate('start_time', $hoy)
        ->get();

        $totalWorkedSeconds = 0;
        foreach($jornadas as $jornada){
            $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? Carbon::now());
            $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
                return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? Carbon::now());
            });
            $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
        }
        $horasTrabajadasFinal = $totalWorkedSeconds / 60;

        $hora = floor($horasTrabajadasFinal / 60);
        $minuto = ($horasTrabajadasFinal % 60);

        $horas_dia = $hora . ' Horas y ' . $minuto . ' minutos';

        return $horas_dia;
    }

    public function getjornadas($idUsuario)
    {
        $events = [];
        $jornadas = Jornada::where('admin_user_id', $idUsuario)->get();
        $now = Carbon::now()->format('Y-m-d H:i:s');


        foreach ($jornadas as $index => $log) {

           $fin = $now;

           if ($log->end_time == null) {
                $events[] =[
                    'Jornada sin finalizar',
                    $log->start_time,
                    $fin,
                    '#FD994E'

                ];
            } else {
                $events[] = [
                    'Jornada finalizada',
                    $log->date_start,
                    $log->date_end,
                    '#FD994E'

                ];
            }

            $pausas = $log->pauses;
            foreach ($pausas as $pausa) {
                if ($pausa->end_time == null) {
                    $events[] = [
                        'Pausa sin finalizar',
                        $pausa->start_time,
                        $fin,
                        '#FF0000'
                    ];
                } else {
                    $events[] = [
                        'Pausa finalizada',
                        $pausa->start_time,
                        $pausa->end_time,
                        '#FF0000'
                    ];
                }
            }
        }
        return $events;
    }
}
