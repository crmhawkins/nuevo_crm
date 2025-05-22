<?php

namespace App\Console\Commands;

use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AlertasPresupuestoConfirmar extends Command
{
    protected $signature = 'Alertas:presupuestoConfirmar';
    protected $description = 'Crear alertas de presupuesto pendiente de confirmar';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pendientes = Budget::where('budget_status_id', 1)
        ->where('updated_at', '<=', Carbon::now()->subHours(24))
        ->get();

        foreach ($pendientes as $pendiente) {
            $alertExists  = Alert::where('stage_id', 2)->where('reference_id', $pendiente->id)->where('status_id', 1)->exists();
            if(!$alertExists ){

                $latestAlertWithStatus2 = Alert::where('stage_id', 2)
                ->where('reference_id', $pendiente->id)
                ->where('status_id', 2)
                ->orderBy('created_at', 'desc')
                ->first();

                // Determinar el valor de cont_postpone: suma 1 si hay una alerta previa, o inicia en 1
                $contPostpone = $latestAlertWithStatus2 ? $latestAlertWithStatus2->cont_postpone + 1 : 0;

                $alert = Alert::create([
                    'reference_id' => $pendiente->id,
                    'admin_user_id' => $pendiente->admin_user_id,
                    'stage_id' => 2,
                    'status_id' => 1,
                    'activation_datetime' => Carbon::now(),
                    'cont_postpone' => $contPostpone,
                    'description' => 'Presupuesto ' . $pendiente->reference.' pendiente de confirmación',
                ]);
            }
        }

        $this->info('Comando completado: Creadion de alertas.');
    }

}
