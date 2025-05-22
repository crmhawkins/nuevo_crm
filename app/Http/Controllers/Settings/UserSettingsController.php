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
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer',
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'firma' => 'required',
        ],[
            'smtp_host.required' => 'El campo SMTP Host es obligatorio.',
            'smtp_host.string' => 'El campo SMTP Host debe ser una cadena de texto.',
            'smtp_host.max' => 'El campo SMTP Host no puede tener más de 255 caracteres.',
            'smtp_port.required' => 'El campo SMTP Port es obligatorio.',
            'smtp_port.integer' => 'El campo SMTP Port debe ser un número entero.',
            'host.required' => 'El campo Host es obligatorio.',
            'host.string' => 'El campo Host debe ser una cadena de texto.',
            'host.max' => 'El campo Host no puede tener más de 255 caracteres.',
            'port.required' => 'El campo Port es obligatorio.',
            'port.integer' => 'El campo Port debe ser un número entero.',
            'username.required' => 'El campo Username es obligatorio.',
            'username.string' => 'El campo Username debe ser una cadena de texto.',
            'username.max' => 'El campo Username no puede tener más de 255 caracteres.',
            'password.required' => 'El campo Password es obligatorio.',
            'password.string' => 'El campo Password debe ser una cadena de texto.',
            'password.max' => 'El campo Password no puede tener más de 255 caracteres.',
            'firma.required' => 'El campo Firma es obligatorio.',
        ]);

        UserEmailConfig::create([
            'admin_user_id' => Auth::user()->id,
            'smtp_host' => $request->input('smtp_host'),
            'smtp_port' => $request->input('smtp_port'),
            'host' => $request->input('host'),
            'port' => $request->input('port'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
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
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer',
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'firma' => 'required',
        ],[
            'smtp_host.required' => 'El campo SMTP Host es obligatorio.',
            'smtp_host.string' => 'El campo SMTP Host debe ser una cadena de texto.',
            'smtp_host.max' => 'El campo SMTP Host no puede tener más de 255 caracteres.',
            'smtp_port.required' => 'El campo SMTP Port es obligatorio.',
            'smtp_port.integer' => 'El campo SMTP Port debe ser un número entero.',
            'host.required' => 'El campo Host es obligatorio.',
            'host.string' => 'El campo Host debe ser una cadena de texto.',
            'host.max' => 'El campo Host no puede tener más de 255 caracteres.',
            'port.required' => 'El campo Port es obligatorio.',
            'port.integer' => 'El campo Port debe ser un número entero.',
            'username.required' => 'El campo Username es obligatorio.',
            'username.string' => 'El campo Username debe ser una cadena de texto.',
            'username.max' => 'El campo Username no puede tener más de 255 caracteres.',
            'password.required' => 'El campo Password es obligatorio.',
            'password.string' => 'El campo Password debe ser una cadena de texto.',
            'password.max' => 'El campo Password no puede tener más de 255 caracteres.',
            'firma.required' => 'El campo Firma es obligatorio.',
        ]);

        $config = UserEmailConfig::findOrFail($id);
        $config->update([
            'smtp_host' => $request->input('smtp_host'),
            'smtp_port' => $request->input('smtp_port'),
            'host' => $request->input('host'),
            'port' => $request->input('port'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'firma' => $request->input('firma'),
        ]);

        return redirect()->back()->with('toast', [
                'icon' => 'success',
                'mensaje' => 'Configuración de correo actualizada correctamente.']);
    }

}
