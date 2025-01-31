<?php

namespace App\Http\Controllers;

use App\Models\Holidays\Holidays;
use App\Models\Jornada\Jornada;

use App\Models\Tasks\LogTasks;
use App\Models\Users\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;


class test extends Controller
{
    public function test0(){
        $lunes = Carbon::now()->startOfWeek();
        $semana = [
            'lunes' => $lunes,
            'martes' => $lunes->copy()->addDays(1),
            'miércoles' => $lunes->copy()->addDays(2),
            'jueves' => $lunes->copy()->addDays(3),
            'viernes' => $lunes->copy()->addDays(4),
        ];

        // Obtengo todos los usuarios
        $users = User::where('inactive', 0)->where('access_level_id', 5)->get();

        $EnOficina = 8; // Horas esperadas en oficina
        $EnOficinaviernes = 7; // Horas esperadas en oficina el viernes
        $producido = 7; // Horas esperadas de producción
        $array = [];
        foreach ($users as $usuario) {
            $horasTrabajadasSemana = 0;
            $horasProducidasSemana = 0;
            $horasEsperadasSemana = 0;
            $horasProducidasEsperadas = 0;

            $balanceHorasTrabajadas = 0;
            $balanceHorasProducidas = 0;
            $detalleDiasDeuda = []; // Para registrar los días que contribuyen a la deuda

            foreach ($semana as $dia => $fecha) {
                $jornadas = Jornada::where('admin_user_id', $usuario->id)
                    ->whereDate('start_time', $fecha)
                    ->exists(); // Verifica si el usuario inició jornada

                if ($jornadas) {
                    $horasTrabajadas = $this->horasTrabajadasDia($fecha, $usuario->id);
                    $horasProducidas = $this->tiempoProducidoDia($fecha, $usuario->id);

                    $horasEsperadas = ($dia === 'viernes') ? $EnOficinaviernes * 60 : $EnOficina * 60;
                    $horasProducidasDiaEsperadas = $producido * 60;

                    $horasTrabajadasSemana += $horasTrabajadas;
                    $horasProducidasSemana += $horasProducidas;
                    $horasEsperadasSemana += $horasEsperadas;
                    $horasProducidasEsperadas += $horasProducidasDiaEsperadas;

                    $balanceDiarioTrabajadas = $horasTrabajadas - $horasEsperadas;
                    $balanceDiarioProducidas = $horasProducidas - $horasProducidasDiaEsperadas;

                    $balanceHorasTrabajadas += $balanceDiarioTrabajadas;
                    $balanceHorasProducidas += $balanceDiarioProducidas;

                }
            }
            array_push($array, $usuario->name);
            array_push($array, $balanceHorasTrabajadas / 60);
            array_push($array, $balanceHorasProducidas / 60);


            if ($balanceHorasTrabajadas < 0 || $balanceHorasProducidas < 0) {
                $mensajeDeuda = $usuario->name.''.$usuario->surname." debe las sigientes horas :\n";
                if ($balanceHorasTrabajadas < 0) {
                    $mensajeDeuda .= " En oficina: " . floor(abs($balanceHorasTrabajadas) / 60) . " horas y " . (abs($balanceHorasTrabajadas) % 60) . " minutos.\n";
                }
                if ($balanceHorasProducidas < 0 && $balanceHorasTrabajadas < 0) $mensajeDeuda .= "y";
                if ($balanceHorasProducidas < 0) {
                    $mensajeDeuda .= " En producción: " . floor(abs($balanceHorasProducidas) / 60) . " horas y " . (abs($balanceHorasProducidas) % 60) . " minutos.\n";
                }

            }

        }
        dd($array);
    }
    public function test(){
            $users = User::where('inactive', 0)->where('id', '!=', 101)->get();
            $descontarausuarios = [];
            foreach ($users as $user) {
                $holiday = Holidays::where('admin_user_id', $user->id)->first()->quantity;
                $startOfWeek = Carbon::now()->startOfWeek();
                $endOfWeek = $startOfWeek->copy()->addDays(4);

                $jornadas = $user->jornadas()
                    ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
                    ->whereNotNull('end_time')
                    ->get();

                // Calcular tiempo trabajado por día
                $descontar = 0;

                $jornadasPorDia = $jornadas->groupBy(function ($jornada) {
                    return Carbon::parse($jornada->start_time)->format('Y-m-d'); // Agrupar por día
                });
                array_push($descontarausuarios, $user->name);
                array_push($descontarausuarios, $holiday);
                foreach ($jornadasPorDia as $day => $dayJornadas) {


                    $totalWorkedSeconds = 0;
                    $isFriday = Carbon::parse($day)->isFriday();

                    foreach ($dayJornadas as $jornada) {
                        $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? $jornada->start_time);
                        $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
                            return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? $pause->start_time);
                        });
                        $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
                    }

                     // Calcular la diferencia: 7 horas si es viernes, 8 horas en el resto de días
                    $targetHours = $isFriday ? 7 : 8;
                    $targetseconds = $targetHours * 3600;
                    $difference = $targetseconds - $totalWorkedSeconds;

                    if ($difference > 0) {
                        // El usuario trabajó menos de las horas objetivo, debe compensar
                        $descontar += $difference;
                    } elseif ($difference < 0) {
                        $descontar += $difference;
                    }

                }
                $holidaysecond = $holiday * 8 * 60 * 60;
                array_push($descontarausuarios, $holidaysecond);
                array_push($descontarausuarios, $descontar);
                if ($descontar > 0) {
                    $newHolidaysecond = $holidaysecond - $descontar ;
                    $newHoliday = $newHolidaysecond / 8 / 3600;
                }else{
                    $newHoliday = $holidaysecond/ 8 / 3600;
                }
                array_push($descontarausuarios, $newHoliday);


            }
            dd($descontarausuarios);
            $this->info('Comando completado: Vacaciones');
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
}
