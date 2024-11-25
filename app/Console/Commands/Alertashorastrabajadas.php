<?php

namespace App\Console\Commands;

use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use App\Models\Jornada\Jornada;
use App\Models\Tasks\LogTasks;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class Alertashorastrabajadas extends Command
{
    protected $signature = 'Alertas:HorasTrabajadas';
    protected $description = 'Crear alertas de presupuesto Finalizado y no facturado';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
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

        foreach ($users as $usuario) {
            $horasTrabajadasSemana = 0;
            $horasProducidasSemana = 0;
            $horasEsperadasSemana = 0;
            $horasProducidasEsperadas = 0;

            $balanceHorasTrabajadas = 0;
            $balanceHorasProducidas = 0;
            $detalleDiasDeuda = []; // Para registrar los días que contribuyen a la deuda

            foreach ($semana as $dia => $fecha) {
                $horasTrabajadas = $this->horasTrabajadasDia($fecha, $usuario->id);
                $horasProducidas = $this->tiempoProducidoDia($fecha, $usuario->id);

                // Calcular horas esperadas para este día
                $horasEsperadas = ($dia === 'viernes') ? $EnOficinaviernes * 60 : $EnOficina * 60;
                $horasProducidasDiaEsperadas = $producido * 60;

                // Actualizar totales
                $horasTrabajadasSemana += $horasTrabajadas;
                $horasProducidasSemana += $horasProducidas;
                $horasEsperadasSemana += $horasEsperadas;
                $horasProducidasEsperadas += $horasProducidasDiaEsperadas;

                // Calcular balance diario y actualizar balance semanal
                $balanceDiarioTrabajadas = $horasTrabajadas - $horasEsperadas;
                $balanceDiarioProducidas = $horasProducidas - $horasProducidasDiaEsperadas;

                $balanceHorasTrabajadas += $balanceDiarioTrabajadas;
                $balanceHorasProducidas += $balanceDiarioProducidas;

            }

            if ($balanceHorasTrabajadas < 0 || $balanceHorasProducidas < 0) {
                $mensajeDeuda = "Debe de la semana pasada :\n";
                if ($balanceHorasTrabajadas < 0) {
                    $mensajeDeuda .= " En oficina: " . floor(abs($balanceHorasTrabajadas) / 60) . " horas y " . (abs($balanceHorasTrabajadas) % 60) . " minutos.\n";
                }
                if ($balanceHorasProducidas < 0 && $balanceHorasTrabajadas < 0) $mensajeDeuda .= "y";
                if ($balanceHorasProducidas < 0) {
                    $mensajeDeuda .= " En producción: " . floor(abs($balanceHorasProducidas) / 60) . " horas y " . (abs($balanceHorasProducidas) % 60) . " minutos.\n";
                }

                Alert::Create([
                    'admin_user_id' => $usuario->id,
                    'stage_id' => 31,
                    'activation_datetime' => Carbon::now(),
                    'status_id' => 1,
                    'reference_id' => $usuario->id,
                    'description' => $mensajeDeuda
                ]);

            }

        }

        $this->info('Cálculo completado.');
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
