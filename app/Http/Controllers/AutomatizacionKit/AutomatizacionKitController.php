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
        // Definir los estados que quieres mostrar
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

        // Definir la fecha de corte: x dias atras
        $fechaLimite = Carbon::now()->subDays($dias);
        $carbonFecha = \Carbon\Carbon::parse($fechaLimite);
        $fecha = $carbonFecha->format('Y-m-d');

        // return response()->json($fechaLimite);

        // Usar el scope para obtener los registros recientes
        $registros = LogActions::automatizacionEmailsLogs(LogActions::query(), $fechaLimite, $fecha)->get();

        if ($registros->isEmpty()) {
            return redirect()->back()->with('success_message', 'No hay kits');
        }
        
        // Transformar los registros con los nombres de estado
        $resultado = [];

        foreach ($registros as $registro) {
            $estadoNumero = $registro->estado;

            // Solo mostrar los registros con estados definidos en el array
            if (array_key_exists($estadoNumero, $estados)) {
                $resultados[] = [
                    'reference_id' => $registro->reference_id,
                    'fecha' => $fecha,
                    'contratos' => $registro->contratos,
                    'estado' => $estadoNumero // Se traduce el número
                ];
            }
        }

        $resultados = json_decode(json_encode($resultados));

        return $resultados;
    }

    public function viewEstados(Request $request)
    {
        // Asignamos valores por defecto si no se reciben
        $dias_laborales = $request->input('dias_laborales', 21);
        $dias = $request->input('dias', 15);
    
        // Obtenemos los resultados de los contratos
        $resultados = $this->get_contratos($dias_laborales);
    
        return view('kitDigital.estadosKit', compact('resultados', 'dias'));
    }

    public function send_email() {
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
