<?php

namespace App\Console\Commands;

use App\Models\Tasks\LogTasks;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinalizaJornada extends Command
{
    protected $signature = 'Jornada:finalizar';
    protected $description = 'Finaliza Jornadas';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::where('inactive',0)->where('access_level_id',5)->get();
        $jornadas = $users->flatMap(function ($user) {
            return $user->jornadas()->where('is_active', true)->get();
        });
        foreach ($jornadas as $jornada) {
            $fechaJornada = Carbon::parse($jornada->start_time)->toDateString();

            // Obtener la última tarea del mismo día que la jornada y con 'date_end' igual a 1
            $lastTask = LogTasks::where('admin_user_id', $jornada->admin_user_id)
                ->whereDate('date_start', $fechaJornada) // Coincide con la fecha de la jornada
                ->whereNotNull('date_end')
                ->orderBy('id', 'desc')
                ->first();

            $jornada->end_time = $lastTask->date_end;
            $jornada->is_active = false;
            $jornada->save();
        }

        $this->info('Comando completado: Jornadas finalizadas');
    }

}
