<?php

namespace App\Http\Controllers\Horas;

use App\Exports\JornadasExport;
use App\Http\Controllers\Controller;
use App\Models\Holidays\HolidaysPetitions;
use App\Models\Jornada\Jornada;
use App\Models\Tasks\LogTasks;
use App\Models\Users\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HorasController extends Controller
{
    protected function indexHoras(Request $request){

        $selectedWeek = Carbon::parse($request->input('week', now()->format('Y-\WW')));

        // Días de la Semana
        $lunes = $selectedWeek->copy()->startOfWeek();
        $martes = $selectedWeek->copy()->startOfWeek()->addDays(1);
        $miercoles = $selectedWeek->copy()->startOfWeek()->addDays(2);
        $jueves = $selectedWeek->copy()->startOfWeek()->addDays(3);
        $viernes = $selectedWeek->copy()->startOfWeek()->addDays(4);

        // Obtengo todos los usuarios
        $users =  User::where('inactive',0)->get();
        $arrayUsuarios = [];


        // Recorro los usuarios
        foreach ($users as $usuario) {
            // Este if es para que no salgan los mensajes del segundo usuario de Camila, se puede borrar
            if($usuario->id != 81 && $usuario->id != 52){
                // Se imprimen las horas trabajadas de cada usuario en minutos y luego se pone en texto
                $horasTrabajadasLunes = $this->horasTrabajadasDia($lunes, $usuario->id);
                $horasTrabajadasMartes = $this->horasTrabajadasDia($martes, $usuario->id);
                $horasTrabajadasMiercoles = $this->horasTrabajadasDia($miercoles, $usuario->id);
                $horasTrabajadasJueves = $this->horasTrabajadasDia($jueves, $usuario->id);
                $horasTrabajadasViernes = $this->horasTrabajadasDia($viernes, $usuario->id);

                $horasTrabajadasSemana = $horasTrabajadasLunes + $horasTrabajadasMartes + $horasTrabajadasMiercoles + $horasTrabajadasJueves + $horasTrabajadasViernes;

                // Se imprimen las horas producidas de cada usuario en minutos y luego se pone en texto
                $horasProducidasLunes       = $this->tiempoProducidoDia($lunes, $usuario->id);
                $horasProducidasMartes      = $this->tiempoProducidoDia($martes, $usuario->id);
                $horasProducidasMiercoles   = $this->tiempoProducidoDia($miercoles, $usuario->id);
                $horasProducidasJueves      = $this->tiempoProducidoDia($jueves, $usuario->id);
                $horasProducidasViernes     = $this->tiempoProducidoDia($viernes, $usuario->id);

                $vacaciones = $this->vacaciones($lunes, $viernes, $usuario->id);


                $horasProducidasSemana = $horasProducidasLunes + $horasProducidasMartes + $horasProducidasMiercoles + $horasProducidasJueves + $horasProducidasViernes;

                if($horasTrabajadasSemana > 0){
                    //semana
                    $horaHorasTrabajadas = floor($horasTrabajadasSemana / 60);
                    $minutoHorasTrabajadas = ($horasTrabajadasSemana % 60);
                    //lunes
                    $horaHorasTrabajadasLunes = floor($horasTrabajadasLunes / 60);
                    $minutoHorasTrabajadasLunes = ($horasTrabajadasLunes % 60);
                    //martes
                    $horaHorasTrabajadasMartes = floor($horasTrabajadasMartes / 60);
                    $minutoHorasTrabajadasMartes = ($horasTrabajadasMartes % 60);
                    //miercoles
                    $horaHorasTrabajadasMiercoles = floor($horasTrabajadasMiercoles / 60);
                    $minutoHorasTrabajadasMiercoles = ($horasTrabajadasMiercoles % 60);
                    //jueves
                    $horaHorasTrabajadasJueves = floor($horasTrabajadasJueves / 60);
                    $minutoHorasTrabajadasJueves = ($horasTrabajadasJueves % 60);
                    //viernes
                    $horaHorasTrabajadasViernes = floor($horasTrabajadasViernes / 60);
                    $minutoHorasTrabajadasViernes = ($horasTrabajadasViernes % 60);

                    //semana
                    $horaHorasProducidas = floor($horasProducidasSemana / 60);
                    $minutoHorasProducidas = ($horasProducidasSemana % 60);
                    //lunes
                    $horaHorasProducidasLunes = floor($horasProducidasLunes / 60);
                    $minutoHorasProducidasLunes = ($horasProducidasLunes % 60);
                    //martes
                    $horaHorasProducidasMartes = floor($horasProducidasMartes / 60);
                    $minutoHorasProducidasMartes = ($horasProducidasMartes % 60);
                    //miercoles
                    $horaHorasProducidasMiercoles = floor($horasProducidasMiercoles / 60);
                    $minutoHorasProducidasMiercoles = ($horasProducidasMiercoles % 60);
                    //jueves
                    $horaHorasProducidasJueves = floor($horasProducidasJueves / 60);
                    $minutoHorasProducidasJueves = ($horasProducidasJueves % 60);
                    //viernes
                    $horaHorasProducidasViernes = floor($horasProducidasViernes / 60);
                    $minutoHorasProducidasViernes = ($horasProducidasViernes % 60);



                    $arrayUsuarios[] = [
                        'usuario' => $usuario->name.' '.$usuario->surname ,
                        'departamento' => $usuario->departamento->name,
                        'vacaciones' => $vacaciones,
                        'horas_trabajadas' => "$horaHorasTrabajadas h $minutoHorasTrabajadas min",
                        'horasTrabajadasLunes' => "$horaHorasTrabajadasLunes h $minutoHorasTrabajadasLunes min",
                        'horasTrabajadasMartes' => "$horaHorasTrabajadasMartes h $minutoHorasTrabajadasMartes min",
                        'horasTrabajadasMiercoles' => "$horaHorasTrabajadasMiercoles h $minutoHorasTrabajadasMiercoles min",
                        'horasTrabajadasJueves' => "$horaHorasTrabajadasJueves h $minutoHorasTrabajadasJueves min",
                        'horasTrabajadasViernes' => "$horaHorasTrabajadasViernes h $minutoHorasTrabajadasViernes min",
                        'horas_producidas' => "$horaHorasProducidas h $minutoHorasProducidas min",
                        'horasProducidasLunes' => "$horaHorasProducidasLunes h $minutoHorasProducidasLunes min",
                        'horasProducidasMartes' => "$horaHorasProducidasMartes h $minutoHorasProducidasMartes min",
                        'horasProducidasMiercoles' => "$horaHorasProducidasMiercoles h $minutoHorasProducidasMiercoles min",
                        'horasProducidasJueves' => "$horaHorasProducidasJueves h $minutoHorasProducidasJueves min",
                        'horasProducidasViernes' => "$horaHorasProducidasViernes h $minutoHorasProducidasViernes min",
                    ];
                }
            }
        }
        return view('horas.index', ['usuarios' => $arrayUsuarios]);
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

    public function horasTrabajadasDia($dia, $id){

        // Fechas, la función se llamará los viernes por lo que se manipulan respecto a esto,
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

    public function tiempoProducidoDia($dia, $id) {
        $tiempoTarea = 0;
        $tareasHoy = LogTasks::where('admin_user_id', $id)->whereDate('date_start','=', $dia)->get();
        foreach($tareasHoy as $tarea) {
            if ($tarea->status == 'Pausada') {
                $tiempoInicio = Carbon::parse($tarea->date_start);
                $tiempoFinal = Carbon::parse($tarea->date_end);
                $tiempoTarea +=  $tiempoFinal->diffInMinutes($tiempoInicio);
            }
        }
        return $tiempoTarea;
    }

}
