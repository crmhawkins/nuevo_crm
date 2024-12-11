<?php

namespace App\Console\Commands;

use App\Models\Holidays\Holidays;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CrearVacaciones extends Command
{
    protected $signature = 'vacaciones:create';
    protected $description = 'Añade vacaciones';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $usuarios = User::where('inactive', 0)->get();
        foreach ($usuarios as $usuario) {
            $vacaciones = DB::table('holidays')->where('admin_user_id', $usuario->id)->get();
            if (count($vacaciones) == 0) {
                $vacaciones = new Holidays();
                $vacaciones->admin_user_id = $usuario->id;
                $vacaciones->quantity = 0;
                $vacaciones->save();
            }
        }

        $this->info('Comando completado: creadas');
    }

}
