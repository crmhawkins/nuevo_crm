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
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        UserEmailConfig::create([
            'admin_user_id' => Auth::user()->id,
            'host' => $request->input('host'),
            'port' => $request->input('port'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ]);

        return redirect()->back()->with('toast', [
                'icon' => 'success',
                'mensaje' => 'Configuración de correo creada correctamente.']);
    }

    // Update method to edit existing email configuration
    public function update(Request $request, $id)
    {
        $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $config = UserEmailConfig::findOrFail($id);
        $config->update([
            'host' => $request->input('host'),
            'port' => $request->input('port'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ]);

        return redirect()->back()->with('toast', [
                'icon' => 'success',
                'mensaje' => 'Configuración de correo actualizada correctamente.']);
    }

}
