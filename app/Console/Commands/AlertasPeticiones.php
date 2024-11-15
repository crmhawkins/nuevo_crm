<?php

namespace App\Console\Commands;

use App\Models\Alerts\Alert;
use App\Models\Petitions\Petition;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AlertasPeticiones extends Command
{
    protected $signature = 'Alertas:peticiones';
    protected $description = 'Crear alertas de peticiones';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pendientes = Petition::where('finished', 0)
        ->where('created_at', '<=', Carbon::now()->subHours(24))
        ->get();

        foreach ($pendientes as $petition) {
            $alertExists  = Alert::where('stage_id', 1)->where('reference_id', $petition->id)->where('status_id', 1)->exists();
            if(!$alertExists ){
                $latestAlertWithStatus2 = Alert::where('stage_id', 1)
                 ->where('reference_id', $petition->id)
                 ->where('status_id', 2)
                 ->orderBy('created_at', 'desc')
                 ->first();

                 // Determinar el valor de cont_postpone: suma 1 si hay una alerta previa, o inicia en 1
                 $contPostpone = $latestAlertWithStatus2 ? $latestAlertWithStatus2->cont_postpone + 1 : 0;
                $alert = Alert::create([
                    'reference_id' => $petition->id,
                    'admin_user_id' => $petition->admin_user_id,
                    'stage_id' => 1,
                    'status_id' => 1,
                    'activation_datetime' => Carbon::now(),
                    'cont_postpone' => $contPostpone,
                    'description' => 'Peticion de ' . $petition->cliente->company  ?? $petition->cliente->name,
                ]);
            }
        }

        $this->info('Comando completado: Creadion de alertas.');
    }

}
