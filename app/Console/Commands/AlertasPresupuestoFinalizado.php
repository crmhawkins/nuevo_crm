<?php

namespace App\Console\Commands;

use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AlertasPresupuestoFinalizado extends Command
{
    protected $signature = 'Alertas:presupuestoFinalizado';
    protected $description = 'Crear alertas de presupuesto Finalizado y no facturado';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pendientes = Budget::where('budget_status_id', 5)
            ->where('updated_at', '<=', Carbon::now()->subHours(24))
            ->doesntHave('factura') // Agrega esta línea para filtrar los presupuestos sin factura
            ->get();


        foreach ($pendientes as $pendiente) {
            $alertExists  = Alert::where('stage_id', 5)->where('reference_id', $pendiente->id)->where('status_id', 1)->exists();
            if(!$alertExists ){
                $latestAlertWithStatus2 = Alert::where('stage_id', 5)
                ->where('reference_id', $pendiente->id)
                ->where('status_id', 2)
                ->orderBy('created_at', 'desc')
                ->first();

                // Determinar el valor de cont_postpone: suma 1 si hay una alerta previa, o inicia en 1
                $contPostpone = $latestAlertWithStatus2 ? $latestAlertWithStatus2->cont_postpone + 1 : 0;

                $usuarios = User::where('access_level_id', 3)->get();
                foreach ($usuarios as $usuario) {
                    $alert = Alert::create([
                        'reference_id' => $pendiente->id,
                        'admin_user_id' => $usuario->id,
                        'stage_id' => 5,
                        'status_id' => 1,
                        'activation_datetime' => Carbon::now(),
                        'cont_postpone' => $contPostpone,
                        'description' => 'Presupuesto ' . $pendiente->reference.' esta finalizado y no esta facturado.'
                    ]);
                }
            }
        }

        $this->info('Comando completado: Creadion de alertas.');
    }

}
