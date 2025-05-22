<?php

namespace App\Http\Controllers\Contratos;

use App\Http\Controllers\Controller;
use App\Models\Dominios\estadosDominios;
use App\Models\Contratos\Contrato;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContratosController extends Controller
{
    public function index()
    {
        if(Auth::user()->access_level_id == 1 || Auth::user()->access_level_id == 2 || Auth::user()->access_level_id == 3  ){

            $contratos = Contrato::paginate(2);
            return view('contratos.index', compact('contratos'));
        }else{
                return redirect()->back()->with('toast', [
                    'icon' => 'error',
                    'mensaje' => 'No tienes permiso para acceder']);
        }
    }
    public function indexUser($id)
    {
        if ($id == Auth::user()->id){
            return view('contratos.index_user', compact('id'));
        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No tienes permiso para acceder']);
        }
    }
    public function show($id)
    {
        $contrato = Contrato::find($id);
        if( $contrato->admin_user_id == Auth::user()->id || Auth::user()->access_level_id == 1 || Auth::user()->access_level_id == 2 || Auth::user()->access_level_id == 3 ){
            return view('contratos.show', compact('contrato'));
        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No tienes permiso para acceder']);
        }
    }
    public function edit($id)
    {
        $contrato = Contrato::find($id);
        $usuarios = User::all();
        if( $contrato->admin_user_id == Auth::user()->id || Auth::user()->access_level_id == 1 || Auth::user()->access_level_id == 2 || Auth::user()->access_level_id == 3  ){
            return view('contratos.edit', compact('contrato','usuarios'));
        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No tienes permiso para acceder']);
        }
    }
    public function create()
    {

        $usuarios = User::all();
        return view('contratos.create', compact('usuarios'));
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
        $filename = 'Contrato_'.$request->admin_user_id.'_'.today()->format('Y_m_d').'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('contratos', $filename, 'public');

        // Crear la nómina
        $contrato = new Contrato;
        $contrato->admin_user_id = $request->admin_user_id;
        $contrato->fecha = $request->fecha;
        $contrato->archivo = $path;
        $contrato->save();

        return redirect()->route('contratos.edit',$contrato->id)->with('toast', [
                'icon' => 'success',
                'mensaje' => 'La contrato se creo correctamente'
            ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'admin_user_id' => 'required|exists:admin_user,id',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $contrato = Contrato::findOrFail($id);

        if ($request->hasFile('archivo')) {
            // Eliminar el archivo anterior si existe
            Storage::delete('public/' . $contrato->archivo);

            // Subir el nuevo archivo
            $file = $request->file('archivo');
            $filename = 'Contrato_'.$request->admin_user_id.'_'.today()->format('Y_m_d').'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('contratos', $filename, 'public');
            $contrato->archivo = $path;
        }

        // Actualizar otros campos
        $contrato->admin_user_id = $request->admin_user_id;
        $contrato->fecha = $request->fecha;
        $contrato->save();

        return redirect()->route('contratos.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'El contrato se actualizo correctamente'
        ]);
    }

    public function destroy(Request $request)
    {
        $contrato = Contrato::find($request->id);

        if (!$contrato) {
            return response()->json([
                'status' => false,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        if(isset($contrato->archivo)){
            Storage::delete('public/' . $contrato->archivo);
        }
        $contrato->delete();
        return response()->json([
            'status' => true,
            'mensaje' => 'El contrato fue borrado correctamente'
        ]);
    }
}
