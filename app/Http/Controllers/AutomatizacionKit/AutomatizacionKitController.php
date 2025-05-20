<?php

namespace App\Http\Controllers\AutomatizacionKit;

use App\Http\Controllers\Controller;
use App\Models\Alerts\Alert;
use App\Models\Logs\LogActions;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutomatizacionKitController extends Controller
{
    public function getContratos($dias)
    {
        $estados = [
            8 => 'Justificado',
            9 => 'Justificado parcial',
            10 => 'Validada',
            12 => 'Pendiente subsanar 1',
            13 => 'Pendiente subsanar 2',
            14 => 'Subsanado 1',
            15 => 'Subsanado 2',
            20 => 'Pendiente 2ª Justificacion',
            21 => '2º Justificacion Realizada',
            25 => 'Validada 2ª justificacion',
            29 => 'Subsanado 3',
            30 => 'SASAK',
            31 => 'R SASAK',
            32 => '2º Subsanado 1',
            33 => '2º Subsanado 2',
            34 => '2º Subsanado 3',
            35 => 'Subsanacion incorrecta',
            36 => 'Finalizado plazo de subsanacion',
            37 => 'C.aleatoria',
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
                'empresa'       => $registro->empresa ?? null, // ← NUEVO
            ];
            
        })->values();
    }


    public function viewEstados2(Request $request)
    {
        // Asignamos valores por defecto si no se reciben
        $dias_laborales = $request->input('dias_laborales', 21);
        $dias = $request->input('dias', 15);
        $mas6Meses = $request->input('mas6Meses', false);
        if ($mas6Meses) {
            $dias = 30*6;
            $resultados = LogActions::mas6Meses()->get();
            //dd($resultados);
        } else {
            $resultados = $this->getContratos($dias_laborales);
        }
        if ($resultados->isEmpty()) {
            return redirect()->back()
            ->with('success_message', "Actualmente no existen kits con más de {$dias} días sin actualizar su estado ni con el SASAK enviado.")
            ->with('success_dias', $dias);
        }

        return view('kitDigital.estadosKit', compact('resultados', 'dias'));
    }

    public function viewEstados(Request $request)
{
    $dias_laborales = $request->input('dias_laborales', 21);
    $dias = $request->input('dias', 15);
    $empresa = $request->input('empresa'); // ← NUEVO

    if ($request->input('mas6Meses', false)) {
        $dias = 30 * 6;
        $resultados = LogActions::mas6Meses()->get();
    } else {
        $resultados = $this->getContratos($dias);
    }

    // Filtrado por empresa si se selecciona una
    if ($empresa) {
        $resultados = $resultados->filter(function ($item) use ($empresa) {
            return isset($item->empresa) && $item->empresa === $empresa;
        })->values();
    }

    if ($resultados->isEmpty()) {
        return redirect()->back()
            ->with('success_message', "Actualmente no existen kits con más de {$dias} días sin actualizar su estado ni con el SASAK enviado.")
            ->with('success_dias', $dias);
    }

    return view('kitDigital.estadosKit', compact('resultados', 'dias', 'empresa'));
}


    public function sendEmail()
    {
        $resultados = $this->getContratos(63);

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

    public function createAlert(Request $request)
    {

         $request->validate([
        'reference_id' => 'required|integer',
        'description' => 'required|string',
        'activation_date' => 'required|date',
        ]);

        $activationDateTime = Carbon::parse($request->activation_date)->startOfDay(); // lo deja a las 00:00

        $dataAlert = [
            'admin_user_id' => Auth::id(),
            'stage_id' => 53, // o dinámico si lo necesitas
            'activation_datetime' => $activationDateTime,
            'status_id' => 1,
            'reference_id' => $request->reference_id,
            'description' => $request->description,
        ];

        Alert::create($dataAlert);

        return redirect()->back()->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Alerta creada correctamente'
        ]);

    }
}
