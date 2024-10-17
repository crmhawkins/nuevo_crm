<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Email\UserEmailConfig;
use Illuminate\Http\Request;
use App\Models\Settings\Settings;
use App\Models\Settings\Schedule;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    public function emailSettings()
    {
        $configuracion = UserEmailConfig::where('admin_user_id', Auth::user()->id)->get();

        return view('settings.emailConfig', compact('configuracion'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'price_hour' => 'required|numeric',
            'fecha_inicio_verano' => 'required|date',
            'fecha_fin_verano' => 'required|date',
            'fecha_inicio_invierno' => 'required|date',
            'fecha_fin_invierno' => 'required|date',
        ]);

        $configuracion = Settings::create($request->only(['price_hour', 'fecha_inicio_verano', 'fecha_fin_verano', 'fecha_inicio_invierno', 'fecha_fin_invierno']));

        $this->saveHorarios($configuracion, $request->horarios);


        return redirect()->route('configuracion.index')->with('success', 'Configuración creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        dd($request->all());
        $configuracion = Settings::find($id);

        $request->validate([
            'price_hour' => 'required|numeric',
            'fecha_inicio_verano' => 'required|date',
            'fecha_fin_verano' => 'required|date',
            'fecha_inicio_invierno' => 'required|date',
            'fecha_fin_invierno' => 'required|date',
        ]);

        $configuracion->update($request->only(['price_hour', 'fecha_inicio_verano', 'fecha_fin_verano', 'fecha_inicio_invierno', 'fecha_fin_invierno']));

        // Actualizar horarios
        Schedule::where('settings_id', $configuracion->id)->delete();

        $this->saveHorarios($configuracion->id, $request->horarios);


        return redirect()->route('configuracion.index')->with('success', 'Configuración actualizada correctamente.');
    }

    private function saveHorarios( $id, $horarios)
    {
        $configuracion = Settings::find($id);
        foreach (['verano', 'invierno'] as $tipo) {
            foreach ($horarios[$tipo] as $dia => $horas) {
                foreach ($horas as $hora) {
                    if ($hora['inicio'] && $hora['fin']) {
                        $configuracion->horarios()->create([
                            'tipo' => $tipo,
                            'dia' => $dia,
                            'inicio' => $hora['inicio'],
                            'fin' => $hora['fin'],
                        ]);
                    }
                }
            }
        }
    }

}
