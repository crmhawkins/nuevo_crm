<?php

namespace App\Console\Commands;

use App\Models\Alerts\Alert;
use App\Models\Holidays\Holidays;
use App\Models\Holidays\HolidaysPetitions;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DiscountVacaciones extends Command
{
    protected $signature = 'vacacioner:discount';
    protected $description = 'Quita vacaciones';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::where('inactive', 0)->where('id', '!=', 101)->get();

        foreach ($users as $user) {
            $holiday = Holidays::where('admin_user_id', $user->id)->first()->quantity;

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->addDays(5);

            $jornadas = $user->jornadas()
                ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
                ->get();

            // Calcular tiempo trabajado por día
            $descontar = 0;

            $jornadasPorDia = $jornadas->groupBy(function ($jornada) {
                return Carbon::parse($jornada->start_time)->format('Y-m-d'); // Agrupar por día
            });

            foreach ($jornadasPorDia as $day => $dayJornadas) {


                $totalWorkedSeconds = 0;
                $isFriday = Carbon::parse($day)->isFriday();
                $isHalfDay = HolidaysPetitions::where('admin_user_id', $user->id)->where('holidays_status_id', 1)->where('from', '<=', $day)->where('to', '>=', $day)->first();

                foreach ($dayJornadas as $jornada) {
                    $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? $jornada->start_time);
                    $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
                        return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? $pause->start_time);
                    });
                    $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
                }

                if($isHalfDay){
                    $targetHours = 5;
                }else{
                    $targetHours = $isFriday ? 7 : 8;
                }
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

            if ($descontar > 0) {
            $newHolidaySeconds = $holidaysecond - $descontar;
            $newHoliday = $newHolidaySeconds / 8 / 3600;
            $horasDescontadas = $holiday - $newHoliday;

            // SOLO MOSTRAR, NO GUARDAR
            $this->line("Usuario: {$user->name} {$user->surname}");
            $this->line("Descontar: {$descontar}");
            $this->line("Vacaciones actuales: {$holiday}");
            $this->line("Vacaciones nuevas simuladas: " . round($newHoliday, 2));
            $this->line("Días descontados: " . round($horasDescontadas, 2));
            $this->line("------");
            }
            // if ($descontar > 0) {
            //     $newHolidaysecond = $holidaysecond - $descontar ;
            //     $newHoliday = $newHolidaysecond / 8 / 3600;

            //     DB::update('UPDATE holidays SET quantity = ? WHERE user_id = ?', [$newHoliday, $user->id]);

            //     $horasDescontadas= $holiday - $newHoliday;

            //      Alert::create([
            //         'admin_user_id' => 8,
            //         'stage_id' => 50,
            //         'activation_datetime' => Carbon::now(),
            //         'reference_id' => $user->id,
            //         'cont_postpone' => 0,
            //         'description' => 'Se ha descontado ' . $horasDescontadas . ' dias de vacaciones de '.$user->name.' '. $user->surname,
            //     ]);
            // }
        }
        $this->info('Comando completado: Vacaciones');
    }

}
