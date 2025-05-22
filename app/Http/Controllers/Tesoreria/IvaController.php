<?php

namespace App\Http\Controllers\Tesoreria;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounting\Iva;

class IvaController extends Controller
{

    public function index()
    {
        $ivas = Iva::all();
        return view('tesoreria.iva.index', compact('ivas'));
    }
    public function create()
    {
        return view('tesoreria.iva.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'nombre' => 'required',
            'valor' => 'required',
        ],[
            'nombre.required' => 'El nombre es obligatorio.',
            'valor.required' => 'El valor es obligatorio.',
        ]);
        $iva = new Iva($validated);
        $iva->save();
        return redirect()->route('iva.index')->with('toast',[
            'icon' => 'success',
            'mensaje' => 'IVA creado exitosamente'
        ]);
    }

    public function edit(string $id)
    {
        $iva = Iva::find($id);
        if (!$iva) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El IVA no existe'
            ]);
            return redirect()->route('iva.index');
        }
        return view('tesoreria.iva.edit', compact('iva'));
    }

    public function update(Request $request, string $id)
    {
        $iva = Iva::find($id);
        if (!$iva) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El IVA no existe'
            ]);
            return redirect()->route('iva.index');
        }
        $validated = $this->validate($request, [
            'nombre' => 'required',
            'valor' => 'required',
        ],[
            'nombre.required' => 'El nombre es obligatorio.',
            'valor.required' => 'El valor es obligatorio.',
        ]);
        $ivaUpdated = $iva->update($validated);

        if($ivaUpdated){
            return redirect()->route('iva.index', $iva->id)->with('toast',[
                'icon' => 'success',
                'mensaje' => 'El IVA se actualizo correctamente'
            ]);
        }else{
            return redirect()->back()->with('toast',[
                'icon' => 'error',
                'mensaje' => 'Error al actualizar el IVA'
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $iva = Iva::find($request->id);
        if($iva){
            $iva->delete();
            return redirect()->back()->with('toast',[
                'icon' => 'success',
                'mensaje' => 'El IVA se elimino correctamente'
            ]);
        }else{
            return redirect()->back()->with('toast',[
                'icon' => 'error',
                'mensaje' => 'Error al eliminar el IVA'
            ]);
        }
    }
}
