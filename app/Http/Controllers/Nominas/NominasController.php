<?php

namespace App\Http\Controllers\Nominas;

use App\Http\Controllers\Controller;
use App\Models\Dominios\estadosDominios;
use App\Models\Nominas\Nomina;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NominasController extends Controller
{
    public function index()
    {

        if(Auth::user()->access_level_id == 1 || Auth::user()->access_level_id == 2 || Auth::user()->access_level_id == 3  ){

            $nominas = Nomina::paginate(2);
            return view('nominas.index', compact('nominas'));
        }else{
                return redirect()->back()->with('toast', [
                    'icon' => 'error',
                    'mensaje' => 'No tienes permiso para acceder']);
        }
    }
    public function indexUser($id)
    {
        if ($id == Auth::user()->id){
            return view('nominas.index_user', compact('id'));
        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No tienes permiso para acceder']);
        }
    }
    public function show($id)
    {
        $nomina = Nomina::find($id);
        if( $nomina->admin_user_id == Auth::user()->id){
            return view('nominas.show', compact('nomina'));
        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No tienes permiso para acceder']);
        }
    }
    public function edit($id)
    {
        $nomina = Nomina::find($id);
        $usuarios = User::all();
        if( $nomina->admin_user_id == Auth::user()->id || Auth::user()->access_level_id == 1 || Auth::user()->access_level_id == 2 || Auth::user()->access_level_id == 3  ){
            return view('nominas.edit', compact('nomina','usuarios'));
        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No tienes permiso para acceder']);
        }
    }
    public function create()
    {
        $usuarios = User::all();
        return view('nominas.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'admin_user_id' => 'required|exists:admin_user,id',
            'fecha' => 'required|date',
            'archivo' => 'required|file|mimes:pdf|max:2048', // Asegura que sea un PDF y no supere los 2MB
        ]);

        // Procesar archivo
        $file = $request->file('archivo');
        $filename = 'Nomina_'.$request->admin_user_id.'_'.today()->format('Y_m_d').'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('nominas', $filename, 'public');

        // Crear la nómina
        $nomina = new Nomina;
        $nomina->admin_user_id = $request->admin_user_id;
        $nomina->fecha = $request->fecha;
        $nomina->archivo = $path;
        $nomina->save();

        return redirect()->route('nominas.edit',$nomina->id)->with('toast', [
                'icon' => 'success',
                'mensaje' => 'La nomina se creo correctamente'
            ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'admin_user_id' => 'required|exists:admin_user,id',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $nomina = Nomina::findOrFail($id);

        if ($request->hasFile('archivo')) {
            // Eliminar el archivo anterior si existe
            Storage::delete('public/' . $nomina->archivo);

            // Subir el nuevo archivo
            $file = $request->file('archivo');
            $filename = 'Nomina_'.$request->admin_user_id.'_'.today()->format('Y_m_d').'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('nominas', $filename, 'public');
            $nomina->archivo = $path;
        }

        // Actualizar otros campos
        $nomina->admin_user_id = $request->admin_user_id;
        $nomina->fecha = $request->fecha;
        $nomina->save();

        return redirect()->route('nominas.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'La nomina se actualizo correctamente'
        ]);
    }

    public function destroy(Request $request)
    {
        $nomina = Nomina::find($request->id);

        if (!$nomina) {
            return response()->json([
                'status' => false,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $nomina->delete();
        return response()->json([
            'status' => true,
            'mensaje' => 'La Nomina fue borrada correctamente'
        ]);
    }
}
