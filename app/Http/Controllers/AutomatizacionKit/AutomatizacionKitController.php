<?php

namespace App\Http\Controllers\AutomatizacionKit;

use App\Http\Controllers\Controller;
use App\Models\Logs\LogActions;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class AutomatizacionKitController extends Controller
{
    public function get_contratos($dias)
    {
        $estados = [
            8 => 'Justificado',
            9 => 'Justificado parcial',
            14 => 'Subsanado 1',
            15 => 'Subsanado 2',
            29 => 'Subsanado 3',
            32 => '2º Subsanado 1',
            33 => '2º Subsanado 2',
            34 => '2º Subsanado 3',
        ];

        $fechaLimite = Carbon::now()->subDays($dias);
        $carbonFecha = Carbon::parse($fechaLimite);
        $fecha = $carbonFecha->format('Y-m-d');

        $registros = LogActions::automatizacionEmailsLogs(LogActions::query(), $fechaLimite, $fecha)->get();

        return $registros->filter(function ($registro) use ($estados) {
            return array_key_exists($registro->estado, $estados);
        })->map(function ($registro) use ($fecha) {
            $carbon_fecha_estado = Carbon::parse($registro->ultima_fecha);
            $fecha_estado = $carbon_fecha_estado->format('Y-m-d');
            
            return (object) [
                'reference_id'  => $registro->reference_id,
                'contratos'     => $registro->contratos,
                'estado'        => $registro->estado,
                'fecha_estado'  => $fecha_estado,
                'fecha_sasak'   => $registro->sasak ?? 'No enviado',
            ];
        })->values();
    }


    public function viewEstados(Request $request)
    {
        // Asignamos valores por defecto si no se reciben
        $dias_laborales = $request->input('dias_laborales', 21);
        $dias = $request->input('dias', 15);
    
        $resultados = $this->get_contratos($dias_laborales);

        if ($resultados->isEmpty()) {
            return redirect()->back()
            ->with('success_message', "Actualmente no existen kits con más de {$dias} días sin actualizar su estado ni con el SASAK enviado.")
            ->with('success_dias', $dias);
        }

        return view('kitDigital.estadosKit', compact('resultados', 'dias'));
    }

    public function send_email() 
    {
        $resultados = $this->get_contratos(30);

         try {
            // Generar contenido del correo
            $contenido = "SASAK:\n\nBuenos días,\nRecordamos que el contrato {$resultados->contratos} lleva más de 30 días sin actualizarse.\n\nSaludos, Hawkins.";

            // Enviar correo directamente
            Mail::raw($contenido, function ($message) use ($resultados) {
                $message->to('infodigitalizador@acelerapyme.gob.es')
                        ->subject('Sasak ' . $resultados->contratos);
            });
            
            // Registrar el envío del correo
            LogActions::registroCorreosEnviados($resultados);
        } catch(Exception $e) {
            echo 'Error al enviar el correo: ' . $e->getMessage();
        }
    }
}
