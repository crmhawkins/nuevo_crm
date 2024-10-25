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
        $pendientes = Petition::where('status_id', 1)
        ->where('created_at', '<=', Carbon::now()->subHours(24))
        ->get();

        foreach ($pendientes as $petition) {
            $alertExists  = Alert::where('reference_id', $petition->id)->where('status_id', 1)->exists();
            if(!$alertExists ){
                $alert = Alert::create([
                    'reference_id' => $petition->id,
                    'admin_user_id' => $petition->admin_user_id,
                    'status_id' => 1,
                    'activation_datetime' => Carbon::now(),
                    'cont_postpone' => 0,
                    'description' => 'Peticion de ' . $petition->client->name,
                ]);
            }
        }

        $this->info('Comando completado: Creadion de alertas.');
    }

}
