<?php

namespace App\Console;

use App\Mail\MailHorasTrabajadas;
use App\Mail\MailHorasTrabajadasUsuario;
use App\Models\Alerts\Alert;
use App\Models\HoursMonthly\HoursMonthly;
use App\Models\Jornada\Jornada;
use App\Models\Tasks\LogTasks;
use App\Models\Users\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('vacacioner:add')->monthlyOn(1, '08:00');
        $schedule->command('correos:get')->everyMinute();
        $schedule->command('correos:getFacturas')->everyMinute();
        $schedule->command('Jornada:finalizar')->dailyAt('03:00');
        $schedule->command('Alertas:facturaFuera')->dailyAt('03:00');
        $schedule->command('Alertas:peticiones')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoAceptadoTareas')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoAceptadoTareasFinalizar')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoAceptar')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoConfirmar')->dailyAt('03:00');
        $schedule->command('Alertas:presupuestoFinalizado')->dailyAt('03:00');
        $schedule->command('Alertas:HorasTrabajadas')->weeklyOn(5, '23:30');


        // $schedule->call(function () {
        //     $users = User::where('inactive', 0)->where('id', '!=', 101)->get();

        //     foreach ($users as $user) {
        //         // Obtiene el último mes (desde el inicio hasta el fin)
        //         $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
        //         $endOfMonth = Carbon::now()->subMonth()->endOfMonth();

        //         $jornadas = $user->jornadas()
        //             ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
        //             ->get();

        //         // Calcular tiempo trabajado por día
        //         $descontar = 0;

        //         foreach ($jornadas->groupBy(function($jornada) {
        //             return Carbon::parse($jornada->start_time)->format('Y-m-d'); // Agrupar por día
        //         }) as $day => $dayJornadas) {

        //             $totalWorkedSeconds = 0;
        //             $isFriday = Carbon::parse($day)->isFriday();

        //             foreach ($dayJornadas as $jornada) {
        //                 $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? $jornada->start_time);
        //                 $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
        //                     return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? $pause->start_time);
        //                 });
        //                 $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
        //             }

        //             // Convertir los segundos trabajados en horas
        //             $workedHours = $totalWorkedSeconds / 3600;
        //             // Calcular la diferencia: 7 horas si es viernes, 8 horas en el resto de días
        //             $targetHours = $isFriday ? 7 : 8;
        //             $difference = $targetHours - $workedHours;

        //             if ($difference > 0) {
        //                 // El usuario trabajó menos de las horas objetivo, debe compensar
        //                 $descontar += $difference;
        //             } elseif ($difference < 0) {
        //                 $descontar -= $difference;
        //             }
        //         }
        //         $descontarDias = $descontar / 24;
        //         DB::update('UPDATE holidays SET quantity = quantity - ? WHERE user_id = ?', [$descontarDias, $user->id]);
        //     }
        // })->monthlyOn(1, '09:00');


        $schedule->call(function () {
            $this->sendEmailHoras();
        //})->everyMinute();
        })->weeklyOn(5, '23:30');
        // $schedule->call(function () {
        //     $users = User::where('inactive',0)->get();
        //     $fechaNow = Carbon::now();
        //     $annio = $fechaNow->format('Y');
        //     $mes = $fechaNow->format('m');
        //     $dia = $fechaNow->format('d');
        //     foreach($users as $user)
        //     {
        //         if( $user->access_level_id == 4 || $user->access_level_id == 5 ){
        //         $fechaFormateada = $fechaNow->format('Y-m-d');
        //         $hoy2 = $fechaNow->format('l');
        //         $time = 0;

        //             if ($hoy2 == 'Monday') {
        //                 $dia = $dia - 3;
        //                 $time = 2;
        //             }elseif($hoy2 == 'Tuesday'){
        //                 $dia = $dia - 1;
        //                 $time = 2;

        //             }elseif($hoy2 == 'Wednesday'){
        //                 $dia = $dia - 1;
        //                 $time = 2;

        //             }elseif($hoy2 == 'Thursday'){
        //                 $dia = $dia - 1;
        //                 $time = 2;

        //             }elseif($hoy2 == 'Friday'){
        //                 $dia = $dia - 1;
        //                 $time = 2;

        //             }elseif($hoy2 == 'Saturday'){
        //                 $dia = $dia - 1;
        //                 $time = 6;

        //             }elseif($hoy2 == 'Sunday'){
        //                 $dia = $dia - 2;
        //                 $time = 7;

        //             }


        //             $alert_30 = Alert::where('admin_user_id', $user->id)->where('stage_id', 30)->whereDate('activation_datetime',$fechaFormateada)->get();
        //             $totalMinutos = 0;
        //             $horasProducidas = DB::select("SELECT SUM(TIMESTAMPDIFF(MINUTE,date_start,date_end)) AS minutos FROM `log_tasks` WHERE date_start BETWEEN ($fechaFormateada - INTERVAL $time DAY) AND NOW() AND `admin_user_id` = $user->id");

        //             if(count($alert_30) != 0){
        //                 $jornada = Jornada::where('admin_user_id', $user->id)
        //                 ->whereYear('date_start', $annio)
        //                 ->whereMonth('date_start', $mes)
        //                 ->whereDay('date_start', $dia)
        //                 ->get();

        //                 foreach($jornada as $item){
        //                 $to_time = strtotime($item->date_start);
        //                 $from_time = strtotime($item->date_end);
        //                 $minutes = ($from_time - $to_time) / 60;
        //                 $totalMinutos += $minutes;
        //                 }

        //                 $data = [
        //                     'admin_user_id' => $user->id,
        //                     'stage_id' => 30,
        //                     'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
        //                     'status_id' => 1,
        //                     'descripcion' => $totalMinutos, $horasProducidas
        //                 ];
        //                 $alert = Alert::create($data);
        //                 $alertSaved = $alert->save();
        //             }
        //         }
        //     }

        // })->weeklyOn(1, '17:20');

        $schedule->call(function () {
            $users = User::where('inactive',0)->get();
            $fechaNow = Carbon::now();
            foreach($users as $user)
            {
                $minutos = DB::select("SELECT SUM(TIMESTAMPDIFF(MINUTE,date_start,date_end)) as 'minutos' FROM `log_tasks` WHERE date_start BETWEEN LAST_DAY(now() - interval 2 month) AND LAST_DAY(NOW() - INTERVAL 1 month) AND admin_user_id = $user->id");
                if($minutos[0]->minutos !== null)
                {
                    $dataMonthly = [
                        'admin_user_id' => $user->id,
                        'hours' => $minutos[0]->minutos,
                        'acceptance_hours' => "NO CONFORME",
                    ];
                    $hoursMonthCreate = HoursMonthly::create($dataMonthly);
                    $hoursMonthSaved=$hoursMonthCreate->save();
                    $data = [
                        'admin_user_id' => $user->id,
                        'stage_id' => 22,
                        'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
                        'status_id' => 1,
                        'reference_id' => $hoursMonthCreate->id
                    ];
                    $alert = Alert::create($data);
                    $alertSaved = $alert->save();
                }

            }

            // ///**** ACTUALIZACION DE BASE DE DATOS MARKETING
            // $fechaNow = Carbon::now();

            // $data = [
            //     'admin_user_id' => 1,
            //     'stage_id' => 27,
            //     'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
            //     'status_id' => 1,
            //     'reference_id' => 0,
            //     'description' => "Aviso para actualizacion de base de datos mensual."
            // ];

            // $alertIvan = Alert::create($data);
            // $alertSaved = $alertIvan->save();

            // $data = [
            //     'admin_user_id' => 23,
            //     'stage_id' => 27,
            //     'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
            //     'status_id' => 1,
            //     'reference_id' => 0,
            //     'description' => "Aviso para actualizacion de base de datos mensual."
            // ];

            // $alertLaura = Alert::create($data);
            // $alertSaved = $alertLaura->save();

            // $data = [
            //     'admin_user_id' => 7,
            //     'stage_id' => 27,
            //     'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
            //     'status_id' => 1,
            //     'reference_id' => 0,
            //     'description' => "Aviso para actualizacion de base de datos mensual."
            // ];

            // $alertJose = Alert::create($data);
            // $alertSaved = $alertJose->save();

        })->monthlyOn(1, '08:00');

        // $schedule->command('queue:work --queue=default,newsletter_automatic,newsletter_manual,newsletter_smart --tries=3')
        //     ->cron('* * * * *')
        //     ->withoutOverlapping();
    }

    protected function sendEmailHoras(){

        // Días de la Semana
        $lunes = Carbon::now()->startOfWeek();
        $martes = Carbon::now()->startOfWeek()->addDays(1);
        $miercoles = Carbon::now()->startOfWeek()->addDays(2);
        $jueves = Carbon::now()->startOfWeek()->addDays(3);
        $viernes = Carbon::now()->startOfWeek()->addDays(4);

        // Obtengo todos los usuarios
        $users =  User::where('inactive',0)->get();
        $arrayUsuarios = [];
        $arrayHorasTrabajadas = [];
        $arrayHorasProducidas = [];
        $arrayHorasTotal = [];

        // Recorro los usuarios
        foreach ($users as $usuario) {
            // Este if es para que no salgan los mensajes del segundo usuario de Camila, se puede borrar
            if($usuario->id != 1){
                // Se imprimen las horas trabajadas de cada usuario en minutos y luego se pone en texto
                $horasTrabajadasLunes = $this->horasTrabajadasDia($lunes, $usuario->id);
                $horasTrabajadasMartes = $this->horasTrabajadasDia($martes, $usuario->id);
                $horasTrabajadasMiercoles = $this->horasTrabajadasDia($miercoles, $usuario->id);
                $horasTrabajadasJueves = $this->horasTrabajadasDia($jueves, $usuario->id);
                $horasTrabajadasViernes = $this->horasTrabajadasDia($viernes, $usuario->id);

                $horasTrabajadasSemana = $horasTrabajadasLunes + $horasTrabajadasMartes + $horasTrabajadasMiercoles + $horasTrabajadasJueves + $horasTrabajadasViernes;

                // Se imprimen las horas producidas de cada usuario en minutos y luego se pone en texto
                $horasProducidasLunes = $this->tiempoProducidoDia($lunes, $usuario->id);
                $horasProducidasMartes = $this->tiempoProducidoDia($martes, $usuario->id);
                $horasProducidasMiercoles = $this->tiempoProducidoDia($miercoles, $usuario->id);
                $horasProducidasJueves = $this->tiempoProducidoDia($jueves, $usuario->id);
                $horasProducidasViernes = $this->tiempoProducidoDia($viernes, $usuario->id);

                $horasProducidasSemana = $horasProducidasLunes + $horasProducidasMartes + $horasProducidasMiercoles + $horasProducidasJueves + $horasProducidasViernes;

                if($horasTrabajadasSemana > 0){

                    $horaHorasTrabajadas = floor($horasTrabajadasSemana / 60);
                    $minutoHorasTrabajadas = ($horasTrabajadasSemana % 60);

                    $horaHorasProducidas = floor($horasProducidasSemana / 60);
                    $minutoHorasProducidas = ($horasProducidasSemana % 60);

                        // Si el usuario es acces_level_id 5, se muestran las horas trabajadas y producidas, si no, se muestran las pruducidas solamente
                        if ($usuario->access_level_id == 5) {
                            $mensajeHorasTrabajadas = "Ha trabajado ". $horaHorasTrabajadas . " Horas y " . $minutoHorasTrabajadas . ' minutos'. ' esta semana.';
                            $mensajeHorasProducidas = "Ha producido ". $horaHorasProducidas . " Horas y " . $minutoHorasProducidas . ' minutos'. ' esta semana.';
                        } else{
                            $mensajeHorasTrabajadas = "Ha trabajado ". $horaHorasTrabajadas . " Horas y " . $minutoHorasTrabajadas . ' minutos'. ' esta semana.';
                            $mensajeHorasProducidas = "";
                        }

                    array_push($arrayUsuarios, $usuario->name);
                    array_push($arrayHorasTrabajadas, $mensajeHorasTrabajadas);
                    array_push($arrayHorasProducidas, $mensajeHorasProducidas);
                    array_push($arrayHorasTotal, $usuario->name);
                    array_push($arrayHorasTotal, $mensajeHorasTrabajadas);
                    array_push($arrayHorasTotal, $mensajeHorasProducidas);
                    $this->sendEmailHorasTrabajadasUsuario($usuario->email, $mensajeHorasTrabajadas, $mensajeHorasProducidas);
                }
            }
        }
    $this->sendEmailHorasTrabajadas($arrayHorasTotal);
}

    public function sendEmailHorasTrabajadasUsuario($usuario, $mensajeHorasTrabajadas, $mensajeHorasProducidas){



        $email = new MailHorasTrabajadasUsuario($mensajeHorasTrabajadas, $mensajeHorasProducidas);

        Mail::to($usuario)->send($email);

        return 200;

    }
    public function sendEmailHorasTrabajadas($arrayHorasTotal){

        $mail = "ivan@hawkins.es";
        $mail2 = "nacho.moreno@lchawkins.com";

        $email = new MailHorasTrabajadas($arrayHorasTotal);

        Mail::to($mail)->cc($mail2)->send($email);

        return 200;

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

        // Fechas, la función se llamará los viernes por lo que se manipulan respecto a esto,
        $lunes = Carbon::now()->startOfWeek();
        $hoy = $dia->format('Y/m/d');

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

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
