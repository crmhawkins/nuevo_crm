<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KitDigital;
use Illuminate\Support\Facades\Mail;

class AvisarContratosAntiguos extends Command
{
    protected $signature = 'kitdigital:avisar-contratos-antiguos';
    protected $description = 'Avisar via email sobre los contratos que lleven mas de 11 meses pagados';

    public function handle()
    {
        $contratos = "";
        $contenido = "";
        $contratosArray = [];

        $emails = [
            'ivan@hawkins.es',
            'emma@hawkins.es',
            'administracion@hawkins.es'
        ];
        
        $full_data = $this->getContratos();
        

        foreach ($full_data as $data) {
            if ($data->avisados == 0 or $data->avisados == null) {
                KitDigital::where('id', $data->id)->update(['avisados' => 1]);
                $contratosArray[] = $data->contratos;
            }
        }

        if (!$contratosArray) {
            return;
        }

        $contratos = implode(", ", $contratosArray);       
        
        $contenido = "¡Alerta! Hay Kit Digitales que llevan más de 11 meses pagados. \n\n" .
                     "Los contratos son: \n" . $contratos . "\n\n";
        
        foreach ($emails as $email) {
            Mail::raw($contenido, function ($message) use ($email) {
                $message->to($email)
                        ->subject('¡Alerta! Hay Kit Digitales que llevan más de 11 meses pagados');
            });
        }
    }

    public function getContratos()
    {
        $fechaLimite = now()->subDays(335)->format('Y-m-d');
    
        $full_data = KitDigital::where('fecha_actualizacion', '<=', $fechaLimite)
            ->where('estado', 11)
            ->select([
                'id',
                'contratos',
                'estado',
                'fecha_actualizacion',
                'avisados'
            ])->get();
        
        return $full_data;
    }
}
