<?php

namespace App\Http\Controllers\Passwords;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Passwords\CompanyPassword;
use App\Services\PasswordGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PasswordsController extends Controller
{
    public function index()
    {
        $passwords = CompanyPassword::paginate(2);
        return view('passwords.index', compact('passwords'));
    }

    public function edit($id)
    {
        $clientes = Client::all();
        $password = CompanyPassword::find($id);
        return view('passwords.edit', compact('password','clientes',));
    }
    public function create()
    {
        $clientes = Client::all();
        return view('passwords.create', compact('clientes'));
    }

    /**
     * Muestra la herramienta independiente para generar contraseñas
     */
    public function generator()
    {
        return view('passwords.generator');
    }

    public function store(Request $request)
    {
        // Validamos los campos
        $data = $this->validate($request, [
            'password' => 'required|max:200',
            'client_id' => 'nullable',
            'user' => 'required',
            'website' => 'required',

        ], [
            'password.required' => 'La contraseña es requerida para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'user.required' => 'El usuario es requerido para continuar',

        ]);

        $crearDominio = CompanyPassword::create($data);

        return redirect()->route('passwords.edit',$crearDominio->id)->with('toast', [
            'icon' => 'success',
            'mensaje' => 'La contraseña se creo correctamente'
        ]);
    }

    public function update(Request $request , $id)
    {
        $password = CompanyPassword::find($id);
        // Validamos los campos
        $data = $this->validate($request, [
            'password' => 'required|max:200',
            'client_id' => 'nullable',
            'user' => 'required',
            'website' => 'required',

        ], [
            'password.required' => 'La contraseña es requerida para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'user.required' => 'El usuario es requerido para continuar',

        ]);


        $crearDominio = $password->update($data);


        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'La contraseña se actualizo correctamente'
        ]);

        return redirect()->route('passwords.index');
    }

    public function destroy(Request $request)
    {
        $domino = CompanyPassword::find($request->id);

        if (!$domino) {
            return response()->json([
                'error' => true,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $domino->delete();
        return response()->json([
            'error' => false,
            'mensaje' => 'La contraseña fue borrada correctamente'
        ]);
    }

    /**
     * Genera una contraseña determinista basada en un dominio
     */
    public function generarPassword(Request $request)
    {
        $request->validate([
            'dominio' => 'required|string|max:255'
        ]);

        $service = new PasswordGeneratorService();
        $resultado = $service->generarPasswordDinamica($request->dominio);

        return response()->json([
            'error' => false,
            'dominio_limpio' => $resultado['dominio_limpio'],
            'password' => $resultado['password']
        ]);
    }
}
