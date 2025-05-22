<?php

namespace App\Http\Controllers\Email;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Email\Firma;

class FirmaController extends Controller
{
    public function firma()
    {
        $firma = Firma::get();

        return view('settings.firma', compact('firma'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'firma' => 'required',
        ],[
            'firma.required' => 'El campo Firma es obligatorio.',
        ]);

        Firma::create([
            'firma' => $request->input('firma'),
        ]);

        return redirect()->back()->with('toast', [
                'icon' => 'success',
                'mensaje' => 'Configuración de correo creada correctamente.']);
    }

    // Update method to edit existing email configuration
    public function update(Request $request, $id)
    {
        $request->validate([
            'firma' => 'required',
        ],[
            'firma.required' => 'El campo Firma es obligatorio.',
        ]);

        $config = Firma::findOrFail($id);
        $config->update([
            'firma' => $request->input('firma'),
        ]);

        return redirect()->back()->with('toast', [
                'icon' => 'success',
                'mensaje' => 'Configuración de correo actualizada correctamente.']);
    }

}
