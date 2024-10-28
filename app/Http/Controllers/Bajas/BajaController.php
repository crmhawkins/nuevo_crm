<?php

namespace App\Http\Controllers\Bajas;

use App\Http\Controllers\Controller;
use App\Models\Bajas\Baja;
use App\Models\Users\User;
use Illuminate\Http\Request;

class BajaController extends Controller
{
    public function index()
    {
        $bajas = Baja::all();
        return view('bajas.index', compact('bajas'));

    }

    public function create()
    {
        $usuarios = User::where('inactive',0)->get();
        return view('bajas.create', compact('usuarios'));
    }

    public function edit(Baja $baja)
    {
        $usuarios = User::where('inactive',0)->get();
        $archivos = json_decode($baja->archivos, true) ?? [];
        return view('bajas.edit', compact('baja', 'usuarios','archivos'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'admin_user_id' => 'required',
            'inicio' => 'required',
            'fin' => 'nullable',
            'observacion' => 'nullable',
            'archivos.*' => 'file|nullable', // Asegura que cada archivo sea válido
        ]);

        $baja = new Baja();
        $baja->admin_user_id = $request->admin_user_id;
        $baja->inicio = $request->inicio;
        $baja->fin = $request->fin;
        $baja->observacion = $request->observacion;

        // Almacenar múltiples archivos en un array y codificar a JSON
        if ($request->hasFile('archivos')) {
            $paths = [];
            foreach ($request->archivos as $archivo) {
                $filename = 'Baja_' . $request->admin_user_id . '_' . today()->format('Y_m_d') . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
                $path = $archivo->storeAs('Bajas', $filename, 'public');
                $paths[] = $path;
            }
            $baja->archivos = json_encode($paths);
        }

        $baja->save();

        return redirect()->route('bajas.index')->with('success', 'Baja creada exitosamente');
    }


    public function update(Request $request , Baja $baja)
    {
        $this->validate($request, [
            'admin_user_id' => 'required',
            'inicio' => 'required',
            'fin' => 'nullable',
            'observacion' => 'nullable',
            'archivos.*' => 'file|nullable', // Asegura que cada archivo sea válido y que no se supere el tamaño máximo permitido
        ]);
        $baja->admin_user_id = $request->admin_user_id;
        $baja->inicio = $request->inicio;
        $baja->fin = $request->fin;
        $baja->observacion = $request->observacion;
        // Almacenar múltiples archivos en un array y codificar a JSON
        if ($request->hasFile('archivos')) {
            $paths = $baja->archivos ? json_decode($baja->archivos, true) : [];

            foreach ($request->file('archivos') as $archivo) {
                $filename = 'Baja_' . $request->admin_user_id . '_' . today()->format('Y_m_d') . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
                $path = $archivo->storeAs('Bajas', $filename, 'public');
                $paths[] = $path;
            }
            $baja->archivos = json_encode($paths);
        }

        $baja->save();

        return redirect()->route('bajas.index')->with('success', 'Baja actualizada exitosamente');
    }

    public function destroy(Request $request,){
        $baja = Baja::find($request->id);
        if($baja){
            $baja->delete();
            return response()->json([
                'status' => true,
                'mensaje' => 'Baja eliminada con éxito!'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'mensaje' => 'Baja no encontrada'
            ]);
        }

    }

}
